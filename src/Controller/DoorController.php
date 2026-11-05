<?php

declare(strict_types=1);

namespace Nordwerk\AdventCalendarBundle\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\Util\LocaleUtil;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Nordwerk\AdventCalendarBundle\Calendar\DoorRules;
use Nordwerk\AdventCalendarBundle\Security\DoorAccess;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/**
 * Liefert den Inhalt eines Türchens, aber nur, wenn sein Tag gekommen ist.
 */
#[Route('/_advent_calendar/{id}', name: 'nordwerk_advent_calendar_door', requirements: ['id' => '\d+'], defaults: ['_scope' => 'frontend'], methods: ['GET'])]
class DoorController extends AbstractController
{
    public function __construct(
        private readonly Connection $connection,
        #[Autowire(service: 'contao.image.studio')]
        private readonly Studio $studio,
        #[Autowire(service: 'contao.security.token_checker')]
        private readonly TokenChecker $tokenChecker,
        #[Autowire(service: 'translator')]
        private readonly LocaleAwareInterface $translator,
        #[Autowire(service: 'contao.insert_tag.parser')]
        private readonly InsertTagParser $insertTags,
    ) {
    }

    public function __invoke(int $id, Request $request): JsonResponse
    {
        $this->initializeContaoFramework();

        $day = $request->query->getInt('day');
        $element = $this->connection->fetchAssociative(
            "SELECT * FROM tl_content WHERE id = ? AND type = 'advent_calendar'",
            [$id],
        );

        $pages = false === $element ? [] : $this->visiblePages($element);

        if ([] === $pages) {
            return $this->reply(['state' => 'missing'], 404);
        }

        // Texte und Bildtexte in der Sprache der Website (die Adresse selbst hat keine Seite und keine Sprache)
        $locale = null;

        if ($language = (string) ($pages[array_key_last($pages)]['language'] ?? '')) {
            $locale = LocaleUtil::formatAsLocale($language);
            $request->setLocale($locale);
            $this->translator->setLocale($locale);
        }

        $calendar = $this->connection->fetchAssociative(
            'SELECT * FROM tl_advent_calendar WHERE id = ?',
            [(int) $element['adventCalendar']],
        );

        if (false === $calendar) {
            return $this->reply(['state' => 'missing'], 404);
        }

        // In der Vorschau des Backends sind alle Türchen offen, damit man den Kalender
        // vorab prüfen kann.
        $state = $this->tokenChecker->isPreviewMode()
            ? DoorRules::OPEN
            : DoorRules::state(
                $day,
                new \DateTimeImmutable('now', new \DateTimeZone(date_default_timezone_get())),
                (int) $calendar['year'],
                (bool) $calendar['keepOpen'],
                (bool) $calendar['demoMode'],
            );

        if (DoorRules::OPEN !== $state) {
            return $this->reply(['state' => $state]);
        }

        $door = $this->connection->fetchAssociative(
            "SELECT * FROM tl_advent_calendar_door WHERE pid = ? AND day = ? AND published = '1'",
            [(int) $calendar['id'], $day],
        );

        if (false === $door) {
            return $this->reply(['state' => 'empty']);
        }

        foreach (['kicker', 'headline', 'price', 'oldPrice', 'couponCode', 'linkTitle'] as $field) {
            $door[$field] = StringUtil::decodeEntities((string) $door[$field]);
        }

        // Insert-Tags hier auflösen statt im Template: gleiches Ergebnis in Contao 5 und 6 (dort
        // entfernt der Twig-Filter insert_tag HTML, z. B. aus {{link::*}}).
        $door['text'] = $this->insertTags->replaceInline((string) $door['text']);
        $door['url'] = StringUtil::decodeEntities($this->insertTags->replaceInline((string) $door['url']));

        // Ohne eingestellte Bildgröße auf 1200 px verkleinern: So landet das Bild in
        // assets/images und ist auch sichtbar, wenn sein Ordner in der Dateiverwaltung
        // nicht öffentlich ist.
        $size = StringUtil::deserialize($element['size'], true);

        if ([] === array_filter($size)) {
            $size = [1200, 0, 'proportional'];
        }

        $figure = $door['singleSRC']
            ? $this->studio->createFigureBuilder()->fromUuid($door['singleSRC'])->setSize($size)->setLocale($locale)->buildIfResourceExists()
            : null;

        $html = $this->renderView('@Contao/advent_calendar/door.html.twig', [
            'door' => $door,
            'figure' => $figure,
        ]);

        // Kleines Bild für die Innenseite der geöffneten Tür
        $thumb = $door['singleSRC']
            ? $this->studio->createFigureBuilder()->fromUuid($door['singleSRC'])->setSize([320, 320, 'crop'])->buildIfResourceExists()?->getImage()->getImageSrc()
            : null;

        return $this->reply(['state' => DoorRules::OPEN, 'html' => $html, 'thumb' => $thumb]);
    }

    /**
     * Sichtbar nur, wenn der Besucher auch die Seite mit dem Element sehen darf (siehe DoorAccess).
     * Elemente außerhalb von Artikeln (z. B. in Nachrichten) liefern keine Türchen.
     *
     * @param array<string, mixed> $element
     *
     * @return list<array<string, mixed>> Seite des Elements bis zur Startseite; leer, wenn nicht sichtbar
     */
    private function visiblePages(array $element): array
    {
        $elements = [$element];

        // Verschachtelte Inhalte (Element in Element) bis zum Artikel hinauf
        while ('tl_content' === ($elements[array_key_last($elements)]['ptable'] ?? '') && \count($elements) < 20) {
            $parent = $this->connection->fetchAssociative('SELECT * FROM tl_content WHERE id = ?', [(int) $elements[array_key_last($elements)]['pid']]);

            if (false === $parent) {
                return [];
            }

            $elements[] = $parent;
        }

        $outer = $elements[array_key_last($elements)];
        $article = 'tl_article' === ($outer['ptable'] ?: 'tl_article')
            ? $this->connection->fetchAssociative('SELECT * FROM tl_article WHERE id = ?', [(int) $outer['pid']])
            : false;

        $pages = [];
        $pageId = false === $article ? 0 : (int) $article['pid'];

        while ($pageId > 0 && \count($pages) < 50) {
            $page = $this->connection->fetchAssociative('SELECT * FROM tl_page WHERE id = ?', [$pageId]);

            if (false === $page) {
                return [];
            }

            $pages[] = $page;
            $pageId = (int) $page['pid'];
        }

        $visible = DoorAccess::isVisible(
            $elements,
            false === $article ? null : $article,
            $pages,
            time(),
            $this->tokenChecker->isPreviewMode(),
            fn (array $groups): bool => $this->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, $groups),
        );

        return $visible ? $pages : [];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function reply(array $data, int $status = 200): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-store');

        return $response;
    }
}
