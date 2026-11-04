<?php

declare(strict_types=1);

namespace HeikeWiese\ContaoAdventCalendarBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Doctrine\DBAL\Connection;
use HeikeWiese\ContaoAdventCalendarBundle\Calendar\DoorRules;
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
        $template->set('door_url', $this->urlGenerator->generate('heike_wiese_advent_calendar_door', ['id' => $model->id]));
        // Hintergrund: mitgelieferter Weihnachtsstern, eigenes Bild oder keins. Leerer
        // Wert = Kalender aus der Zeit vor 0.2.0, falls die Migration noch nicht lief.
        $type = (string) ($calendar['backgroundType'] ?? '') ?: ($calendar['singleSRC'] ? 'image' : 'none');
        $template->set('background_type', $type);
        $template->set('background', 'image' === $type && $calendar['singleSRC']
            ? $this->studio->createFigureBuilder()->fromUuid($calendar['singleSRC'])->setSize([1920, 0, 'proportional'])->buildIfResourceExists()
            : null);
        $template->set('poinsettia_url', 'bundles/heikewiesecontaoadventcalendar/hintergrund-weihnachtsstern.jpg');

        // Versionsnummer an den Adressen, damit Browser nach einem Update die neuen
        // Dateien laden
        $public = \dirname(__DIR__, 3).'/public';
        $css = substr((string) md5_file($public.'/advent-calendar.css'), 0, 8);
        $js = substr((string) md5_file($public.'/advent-calendar.js'), 0, 8);

        $GLOBALS['TL_HEAD']['advent_calendar'] = '<link rel="stylesheet" href="bundles/heikewiesecontaoadventcalendar/advent-calendar.css?v='.$css.'">';
        $GLOBALS['TL_BODY']['advent_calendar'] = '<script src="bundles/heikewiesecontaoadventcalendar/advent-calendar.js?v='.$js.'" defer></script>';

        return $template->getResponse();
    }
}
