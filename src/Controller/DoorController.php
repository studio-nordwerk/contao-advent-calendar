<?php

declare(strict_types=1);

namespace HeikeWiese\ContaoAdventCalendarBundle\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use HeikeWiese\ContaoAdventCalendarBundle\Calendar\DoorRules;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Liefert den Inhalt eines Türchens, aber nur, wenn sein Tag gekommen ist.
 */
#[Route('/_advent_calendar/{id}', name: 'heike_wiese_advent_calendar_door', requirements: ['id' => '\d+'], defaults: ['_scope' => 'frontend'], methods: ['GET'])]
class DoorController extends AbstractController
{
    public function __construct(
        private readonly Connection $connection,
        #[Autowire(service: 'contao.image.studio')]
        private readonly Studio $studio,
        #[Autowire(service: 'contao.security.token_checker')]
        private readonly TokenChecker $tokenChecker,
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

        if (false === $element || !$this->isVisible($element)) {
            return $this->reply(['state' => 'missing'], 404);
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

        // Ohne eingestellte Bildgröße auf 1200 px verkleinern: So landet das Bild in
        // assets/images und ist auch sichtbar, wenn sein Ordner in der Dateiverwaltung
        // nicht öffentlich ist.
        $size = StringUtil::deserialize($element['size'], true);

        if ([] === array_filter($size)) {
            $size = [1200, 0, 'proportional'];
        }

        $figure = $door['singleSRC']
            ? $this->studio->createFigureBuilder()->fromUuid($door['singleSRC'])->setSize($size)->buildIfResourceExists()
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
     * @param array<string, mixed> $element
     */
    private function isVisible(array $element): bool
    {
        $now = time();

        if ($element['invisible'] || ($element['start'] && $element['start'] > $now) || ($element['stop'] && $element['stop'] <= $now)) {
            return $this->tokenChecker->isPreviewMode();
        }

        if ($element['protected']) {
            return $this->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, StringUtil::deserialize($element['groups'], true));
        }

        return true;
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
