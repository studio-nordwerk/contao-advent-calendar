<?php

declare(strict_types=1);

namespace Nordwerk\AdventCalendarBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Doctrine\DBAL\Connection;
use Nordwerk\AdventCalendarBundle\Calendar\DoorRules;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsContentElement('advent_calendar', category: 'miscellaneous')]
class AdventCalendarController extends AbstractContentElementController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly UrlGeneratorInterface $urlGenerator,
        #[Autowire(service: 'contao.image.studio')]
        private readonly Studio $studio,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $calendar = $this->connection->fetchAssociative(
            'SELECT * FROM tl_advent_calendar WHERE id = ?',
            [(int) ($model->row()['adventCalendar'] ?? 0)],
        );

        if (false === $calendar) {
            return new Response('');
        }

        // Der Inhalt der Türchen steht bewusst nicht im Quelltext, er wird beim Klick
        // vom Server geholt.
        $template->set('calendar', $calendar);
        $template->set('days', DoorRules::order((int) $calendar['id'], (bool) $calendar['shuffle']));
        $template->set('door_url', $this->urlGenerator->generate('nordwerk_advent_calendar_door', ['id' => $model->id]));
        // Hintergrund: mitgelieferter Weihnachtsstern, eigenes Bild oder keins. Leerer
        // Wert = Kalender aus der Zeit vor 0.2.0, falls die Migration noch nicht lief.
        $type = (string) ($calendar['backgroundType'] ?? '') ?: ($calendar['singleSRC'] ? 'image' : 'none');
        $template->set('background_type', $type);
        $template->set('background', 'image' === $type && $calendar['singleSRC']
            ? $this->studio->createFigureBuilder()->fromUuid($calendar['singleSRC'])->setSize([1920, 0, 'proportional'])->buildIfResourceExists()
            : null);
        $template->set('poinsettia_url', 'bundles/nordwerkadventcalendar/hintergrund-weihnachtsstern.jpg');

        $today = new \DateTimeImmutable('now', new \DateTimeZone(date_default_timezone_get()));
        $template->set('timezone', $today->getTimezone()->getName());
        $template->set('demo', DoorRules::demoActive($today, (int) $calendar['year'], (bool) $calendar['demoMode']));

        // Stylesheet und Skript fügt die Vorlage per {% add %} in die Seite ein. Die Versionsnummer an
        // den Adressen sorgt dafür, dass Browser nach einem Update die neuen Dateien laden.
        $public = \dirname(__DIR__, 3).'/public';
        $template->set('css_url', 'bundles/nordwerkadventcalendar/advent-calendar.css?v='.substr((string) md5_file($public.'/advent-calendar.css'), 0, 8));
        $template->set('js_url', 'bundles/nordwerkadventcalendar/advent-calendar.js?v='.substr((string) md5_file($public.'/advent-calendar.js'), 0, 8));

        // Ändert jemand den Kalender (z. B. Hintergrund, Reihenfolge), leert Contao den Seiten-Cache.
        $this->tagResponse('contao.db.tl_advent_calendar.'.$calendar['id']);

        return $template->getResponse();
    }
}
