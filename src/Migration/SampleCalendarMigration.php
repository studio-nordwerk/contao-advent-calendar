<?php

declare(strict_types=1);

namespace HeikeWiese\ContaoAdventCalendarBundle\Migration;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\Dbafs;
use Contao\Folder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Legt bei der ersten Installation einen „Muster-Adventskalender“ mit 24
 * Beispiel-Türchen an (Seifen, Düfte, Pflege für Männer). Läuft nur, solange
 * es noch keinen Kalender gibt und der Bilderordner fehlt – wer den
 * Musterkalender löscht, bekommt ihn nicht wieder. Beispielbilder: KI-generiert
 * mit Gemini, liegen im Paket unter muster/.
 */
class SampleCalendarMigration extends AbstractMigration
{
    private const FOLDER = 'adventskalender-muster';

    // Tag => [Dachzeile, Überschrift, Bild, Text, Preis, alter Preis,
    // Gutscheincode, Button]
    private const DOORS = [
        1 => ['Unser Tagesangebot', 'Naturseifen im Dreierpack', 'muster-01-seife.jpg', '<p>Lavendel, Honig und eine ganz milde ohne Duft – von Hand gesiedet und in Papier verpackt.</p>', '14,90 €', '18,00 €', '', 'Zum Angebot'],
        2 => ['Gutschein', '10 % auf Ihre ganze Bestellung', '', '<p>Gilt heute und morgen für alles im Shop. Einfach den Code an der Kasse eingeben.</p>', '', '', 'ADVENT10', 'Jetzt einlösen'],
        3 => ['Gut zu wissen', 'Geschenke schön und ohne Plastik verpacken', '', '<p>Packpapier und Paketschnur, ein schönes Tuch zum Knoten oder ein Tannenzweig statt Schleife: So sieht jedes Geschenk liebevoll aus – und der Müll bleibt klein.</p>', '', '', '', ''],
        4 => ['Duft der Woche', 'Eau de Parfum „Winterholz“', 'muster-02-parfum.jpg', '<p>Warm und würzig: Zedernholz, Orange und ein Hauch Zimt. 50 ml im schlichten Glasflakon.</p>', '39,00 €', '49,00 €', '', 'Zum Angebot'],
        5 => ['Kleine Freude', 'Eine Duftprobe gratis', '', '<p>Zu jeder Bestellung legen wir Ihnen heute eine Probe unseres neuen Parfüms dazu.</p>', '', '', '', 'Zum Shop'],
        6 => ['Nikolaus – für Männer', 'Bartpflege-Set', 'muster-03-bart.jpg', '<p>Bartöl, Bartbalsam und ein Kamm aus Holz. Pflegt, duftet dezent und passt in jeden Stiefel.</p>', '29,90 €', '36,00 €', '', 'Zum Angebot'],
        7 => ['Gutschein', 'Heute versandkostenfrei', '', '<p>Ab 30 € Bestellwert verschicken wir Ihre Bestellung heute ohne Versandkosten.</p>', '', '', 'FREIVERSAND', 'Jetzt einlösen'],
        8 => ['Geschenkidee', 'Wichteln für unter 10 Euro', '', '<p>Ein Stück Seife, ein Lippenbalsam oder ein Duftsäckchen für den Kleiderschrank: kleine Dinge, über die sich jeder freut.</p>', '', '', '', 'Zur Auswahl'],
        9 => ['Gemütlich', 'Duftkerze „Zimt & Orange“', 'muster-04-kerze.jpg', '<p>Aus Rapswachs im Glas, brennt rund 30 Stunden und duftet nach Weihnachtsmarkt.</p>', '12,50 €', '15,90 €', '', 'Zum Angebot'],
        10 => ['Für ihn', 'Festes Shampoo für Männer', '', '<p>Mit Zedernholz und Minze, reicht so lange wie zwei Flaschen und braucht keine Verpackung aus Plastik.</p>', '8,90 €', '', '', 'Zum Angebot'],
        11 => ['Gutschein', '5 € geschenkt', '', '<p>Ab 40 € Bestellwert ziehen wir an der Kasse 5 € ab. Gilt bis Sonntag.</p>', '', '', 'FUENF', 'Jetzt einlösen'],
        12 => ['Zum Selbermachen', 'Duftende Orangen mit Nelken', '', '<p>Eine Orange rundherum mit Gewürznelken spicken, nach Wunsch ein Band herumbinden – fertig. Duftet wochenlang und ist ein schönes Mitbringsel.</p>', '', '', '', ''],
        13 => ['Zeit für sich', 'Badesalz mit Rosenblüten', 'muster-05-badesalz.jpg', '<p>Meersalz, Rosenblüten und ein paar Tropfen Rosenöl. Für ein warmes Bad an kalten Abenden.</p>', '9,90 €', '12,50 €', '', 'Zum Angebot'],
        14 => ['Service', 'Geschenkverpackung gratis', '', '<p>Bis Weihnachten verpacken wir jede Bestellung auf Wunsch kostenlos als Geschenk. Einfach in der Kasse ankreuzen.</p>', '', '', '', 'Zum Shop'],
        15 => ['Duft der Woche', 'Parfüm-Probierset', '', '<p>Fünf Düfte zum Kennenlernen, je 2 ml. Den Preis rechnen wir beim Kauf einer großen Flasche wieder an.</p>', '12,00 €', '', '', 'Zum Angebot'],
        16 => ['Gutschein', 'Drei Seifen, nur zwei bezahlen', '', '<p>Legen Sie drei Seifen in den Warenkorb – die günstigste schenken wir Ihnen.</p>', '', '', 'SEIFE3', 'Jetzt einlösen'],
        17 => ['Für Männer', 'Rasierset mit Rasierseife', 'muster-06-rasur.jpg', '<p>Rasierpinsel, Rasierhobel aus Metall und eine Schale Rasierseife. Für eine Rasur wie beim Barbier.</p>', '44,00 €', '54,00 €', '', 'Zum Angebot'],
        18 => ['Gut zu wissen', 'So hält Ihre Seife länger', '', '<p>Nach dem Waschen gut abtropfen lassen und auf eine Seifenschale mit Rillen legen. Trocken gelagert hält ein Stück Naturseife oft zwei Monate.</p>', '', '', '', ''],
        19 => ['Handpflege', 'Handcreme mit Sheabutter', '', '<p>Zieht schnell ein und pflegt raue Winterhände. Klein genug für jede Handtasche.</p>', '7,90 €', '', '', 'Zum Angebot'],
        20 => ['Geschenkidee', 'Geschenkbox nach Ihrem Wunsch', 'muster-07-geschenkbox.jpg', '<p>Seife, Kerze und ein kleiner Duft in einer Box mit Holzwolle – Sie wählen aus, wir packen schön ein.</p>', '34,90 €', '', '', 'Box zusammenstellen'],
        21 => ['Letzte Chance', 'Heute bis 12 Uhr bestellen', '', '<p>Was heute Mittag bestellt ist, kommt noch vor Weihnachten bei Ihnen an.</p>', '', '', '', 'Zum Shop'],
        22 => ['Last minute', 'Geschenkgutschein zum Ausdrucken', '', '<p>Sofort per E-Mail, mit Wunschbetrag und Ihrem persönlichen Gruß. Passt immer.</p>', '', '', '', 'Gutschein kaufen'],
        23 => ['Gutschein', '15 % auf Ihr Lieblingsstück', '', '<p>Ein Artikel Ihrer Wahl, 15 % günstiger. Gilt bis Heiligabend.</p>', '', '', 'LIEBLING15', 'Jetzt einlösen'],
        24 => ['Frohe Weihnachten', 'Danke, dass Sie bei uns einkaufen!', 'muster-08-weihnachten.jpg', '<p>Wir wünschen Ihnen ruhige, schöne Feiertage. Zwischen den Jahren sind wir vom 27. bis 30. Dezember für Sie da.</p>', '', '', '', ''],
    ];

    public function __construct(
        private readonly Connection $connection,
        #[Autowire(service: 'contao.framework')]
        private readonly ContaoFramework $framework,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        #[Autowire('%contao.upload_path%')]
        private readonly string $uploadPath,
    ) {
    }

    public function getName(): string
    {
        return 'Adventskalender: Muster-Adventskalender anlegen';
    }

    public function shouldRun(): bool
    {
        $schema = $this->connection->createSchemaManager();

        if (!$schema->tablesExist(['tl_advent_calendar', 'tl_advent_calendar_door']) || !isset($schema->listTableColumns('tl_advent_calendar')['backgroundtype'])) {
            return false;
        }

        return 0 === (int) $this->connection->fetchOne('SELECT COUNT(*) FROM tl_advent_calendar')
            && !is_dir($this->projectDir.'/'.$this->uploadPath.'/'.self::FOLDER);
    }

    public function run(): MigrationResult
    {
        $this->framework->initialize();

        $folder = $this->uploadPath.'/'.self::FOLDER;
        new Folder($folder);

        $now = time();
        $this->connection->insert('tl_advent_calendar', [
            'tstamp' => $now,
            'title' => 'Muster-Adventskalender',
            'year' => 0,
            'keepOpen' => '1',
            'shuffle' => '1',
            'demoMode' => '1',
            'backgroundType' => 'poinsettia',
            'snow' => '1',
        ]);
        $calendar = (int) $this->connection->lastInsertId();

        foreach (self::DOORS as $day => [$kicker, $headline, $image, $text, $price, $oldPrice, $code, $button]) {
            $uuid = null;

            if ('' !== $image && copy(\dirname(__DIR__, 2).'/muster/'.$image, $this->projectDir.'/'.$folder.'/'.$image)) {
                $uuid = Dbafs::addResource($folder.'/'.$image)->uuid;
            }

            $this->connection->insert(
                'tl_advent_calendar_door',
                [
                    'pid' => $calendar,
                    'tstamp' => $now,
                    'day' => $day,
                    'kicker' => $kicker,
                    'headline' => $headline,
                    'singleSRC' => $uuid,
                    'text' => $text,
                    'price' => $price,
                    'oldPrice' => $oldPrice,
                    'couponCode' => $code,
                    'url' => '' !== $button ? '#' : '',
                    'linkTitle' => $button,
                    'published' => '1',
                ],
                ['singleSRC' => ParameterType::BINARY],
            );
        }

        return $this->createResult(true, 'Adventskalender: Muster-Adventskalender mit 24 Türchen angelegt (Demo-Modus an).');
    }
}
