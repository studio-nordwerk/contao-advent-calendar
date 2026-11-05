<?php

declare(strict_types=1);

namespace Nordwerk\AdventCalendarBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * Seit 0.2.0 gibt es die Auswahl „Hintergrund“. Bestehende Kalender behalten
 * ihr Aussehen: mit gewähltem Bild „Eigenes Bild“, sonst „Kein Bild“.
 */
class BackgroundTypeMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function getName(): string
    {
        return 'Adventskalender: Auswahl „Hintergrund“ für bestehende Kalender';
    }

    public function shouldRun(): bool
    {
        $schema = $this->connection->createSchemaManager();

        if (!$schema->tablesExist(['tl_advent_calendar']) || !isset($schema->listTableColumns('tl_advent_calendar')['backgroundtype'])) {
            return false;
        }

        return (int) $this->connection->fetchOne("SELECT COUNT(*) FROM tl_advent_calendar WHERE backgroundType = ''") > 0;
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement("UPDATE tl_advent_calendar SET backgroundType = IF(singleSRC IS NULL, 'none', 'image') WHERE backgroundType = ''");

        return $this->createResult(true);
    }
}
