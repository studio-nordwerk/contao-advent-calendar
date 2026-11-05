<?php

declare(strict_types=1);

namespace Nordwerk\AdventCalendarBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\FilesModel;
use Contao\Folder;
use Contao\Message;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Warnt in der Türchenliste, wenn ein Bild in einem öffentlichen Ordner liegt. Das Bild selbst
 * prüft kein Datum: Wer seine Adresse kennt oder errät, sieht es schon vor dem Tag.
 */
#[AsCallback(table: 'tl_advent_calendar_door', target: 'config.onload')]
class PublicImageWarningListener
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ContaoFramework $framework,
        private readonly RequestStack $requestStack,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function __invoke(DataContainer|null $dc = null): void
    {
        // Nur in der Liste eines Kalenders (table=tl_advent_calendar_door&id=<Kalender>), nicht beim Bearbeiten
        if (null === $dc || $this->requestStack->getCurrentRequest()?->query->has('act') || !$dc->id) {
            return;
        }

        $doors = $this->connection->fetchAllAssociative(
            'SELECT day, singleSRC FROM tl_advent_calendar_door WHERE pid = ? AND singleSRC IS NOT NULL ORDER BY day',
            [(int) $dc->id],
        );

        $filesModel = $this->framework->getAdapter(FilesModel::class);
        $message = $this->framework->getAdapter(Message::class);

        $days = [];
        $folders = [];

        foreach ($doors as $door) {
            $file = $filesModel->findByUuid($door['singleSRC']);

            if (null === $file) {
                continue;
            }

            $folder = \dirname((string) $file->path);

            // new Folder() legt fehlende Ordner an; gelöschte also überspringen
            if (!is_dir($this->projectDir.'/'.$folder)) {
                continue;
            }

            if ((new Folder($folder))->isUnprotected()) {
                $days[] = $door['day'];
                $folders[$folder] = $folder;
            }
        }

        if ([] !== $days) {
            $message->addInfo(\sprintf(
                $GLOBALS['TL_LANG']['tl_advent_calendar_door']['publicFolder'] ?? 'Doors %s: public folder (%s).',
                implode(', ', $days),
                StringUtil::specialchars(implode(', ', $folders)),
            ));
        }
    }
}
