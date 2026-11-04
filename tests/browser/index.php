<?php

declare(strict_types=1);

// Browser-Test ohne Contao: php -S 127.0.0.1:8766 tests/browser/index.php Mit
// ?heute=2026-12-10 lässt sich ein Datum vorgeben, mit ?demo=1 der Demo-Modus.

require __DIR__.'/../../src/Calendar/DoorRules.php';

use HeikeWiese\ContaoAdventCalendarBundle\Calendar\DoorRules;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($path, '/bundles/')) {
    $file = __DIR__.'/../../public/'.basename($path);
    header('Content-Type: '.match (pathinfo($file, PATHINFO_EXTENSION)) {
        'css' => 'text/css', 'jpg' => 'image/jpeg', default => 'text/javascript',
    });
    readfile($file);
    exit;
}

if (str_starts_with($path, '/img/')) {
    return false;
}

$doors = [
    1 => ['kicker' => 'Unser Tagesangebot', 'headline' => 'Geschenkkorb „Hof Muster“', 'img' => 'geschenkkorb-ki.jpg', 'text' => '<p>Honig, Marmelade, Nudeln und ein Stück Käse, liebevoll gepackt.</p>', 'price' => '24,95 €', 'oldPrice' => '32,00 €', 'url' => '#', 'linkTitle' => 'Vorbestellen'],
    2 => ['kicker' => 'Gutschein', 'headline' => '5 € auf Ihre Vorbestellung', 'img' => '', 'text' => '<p>Gilt bis Sonntag für alle Bestellungen ab 25 €.</p>', 'couponCode' => 'ADVENT5', 'url' => '#', 'linkTitle' => 'Gutschein einlösen'],
    3 => ['kicker' => 'Aus dem Hühnerstall', 'headline' => '10 Eier gratis', 'img' => '', 'text' => '<p>Zu jeder Bestellung ab 20 € legen wir 10 frische Eier dazu.</p>'],
    10 => ['kicker' => 'Rezept', 'headline' => 'Kürbissuppe nach Omas Art', 'img' => '', 'text' => '<p>Ein Hokkaido, eine Zwiebel, ein Stück Ingwer – das ganze Rezept steht in unserem Blog.</p>', 'url' => '#', 'linkTitle' => 'Zum Rezept'],
    24 => ['kicker' => 'Frohe Weihnachten', 'headline' => 'Danke für ein schönes Jahr!', 'img' => '', 'text' => '<p>Zwischen den Jahren haben wir vom 27. bis 30.12. für Sie geöffnet.</p>'],
];

$today = new DateTimeImmutable($_GET['heute'] ?? 'now', new DateTimeZone('Europe/Berlin'));
$demo = (bool) ($_GET['demo'] ?? false);
$keepOpen = '0' !== ($_GET['offen'] ?? '1');

if ('/door' === $path) {
    header('Content-Type: application/json');
    $day = (int) ($_GET['day'] ?? 0);
    $state = DoorRules::state($day, $today, 2026, $keepOpen, $demo);

    if (DoorRules::OPEN !== $state) {
        echo json_encode(['state' => $state]);
        exit;
    }

    if (!isset($doors[$day])) {
        echo json_encode(['state' => 'empty']);
        exit;
    }

    $d = $doors[$day] + ['couponCode' => '', 'price' => '', 'oldPrice' => '', 'url' => '', 'linkTitle' => ''];
    ob_start(); ?>
<article class="advent-calendar__door-content">
    <p class="advent-calendar__kicker"><?= htmlspecialchars($d['kicker']) ?></p>
    <h2 class="advent-calendar__headline"><?= htmlspecialchars($d['headline']) ?></h2>
    <?php if ($d['img']) { ?><figure><img src="/img/<?= $d['img'] ?>" alt="" width="900" height="600"></figure><?php } ?>
    <div class="advent-calendar__text"><?= $d['text'] ?></div>
    <?php if ($d['price']) { ?><p class="advent-calendar__price"><span class="advent-calendar__price-new"><?= $d['price'] ?></span><?php if ($d['oldPrice']) { ?> <s class="advent-calendar__price-old"><?= $d['oldPrice'] ?></s><?php } ?></p><?php } ?>
    <?php if ($d['couponCode']) { ?><p class="advent-calendar__coupon"><span>Ihr Code:</span><button type="button" class="advent-calendar__code" data-copy="<?= $d['couponCode'] ?>"><?= $d['couponCode'] ?></button></p><?php } ?>
    <?php if ($d['url']) { ?><p class="advent-calendar__action"><a class="advent-calendar__button" href="<?= $d['url'] ?>"><?= $d['linkTitle'] ?: 'Zum Angebot' ?></a></p><?php } ?>
</article>
<?php
    echo json_encode(['state' => 'open', 'html' => ob_get_clean(), 'thumb' => $d['img'] ? '/img/'.$d['img'] : null]);
    exit;
}

$query = http_build_query(array_filter(['heute' => $_GET['heute'] ?? null, 'demo' => $_GET['demo'] ?? null, 'offen' => $_GET['offen'] ?? null]));
$days = DoorRules::order(1, true);
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Adventskalender – Browser-Test</title>
    <link rel="stylesheet" href="/bundles/advent-calendar.css">
    <style>
        body { margin: 0; padding: 1rem; font: 16px/1.5 system-ui, sans-serif; background: #f6f2e7; color: #3a342b; }
        main { max-width: 64rem; margin: 0 auto; }
        h1 { font-family: Georgia, serif; font-weight: 600; }
        .hinweis { font-size: .9rem; }
    </style>
</head>
<body>
<main>
    <h1>Adventskalender Hof Muster</h1>
    <p class="hinweis">Testdatum: <?= $today->format('d.m.Y') ?><?= $demo ? ' · Demo-Modus' : '' ?></p>
    <div class="advent-calendar advent-calendar--image advent-calendar--poinsettia"
         data-advent-calendar="/door<?= $query ? '?'.$query : '' ?>"
         data-year="2026" data-today="<?= $today->format('Y-m-d') ?>" data-keep-open="<?= $keepOpen ? 1 : 0 ?>" data-demo="<?= $demo ? 1 : 0 ?>" data-snow
         data-text-early="<p class=&quot;advent-calendar__kicker&quot;>Noch ein bisschen Geduld</p><h2>Dieses Türchen öffnet sich am %s. Dezember.</h2><p>Bis dahin: Schauen Sie gern ins heutige Türchen.</p>"
         data-text-past="<p class=&quot;advent-calendar__kicker&quot;>Schon vorbei</p><h2>Dieses Türchen hat sich wieder geschlossen.</h2><p>Das heutige Türchen wartet auf Sie.</p>"
         data-text-empty="<h2>Hier ist heute leider nichts drin.</h2>"
         data-text-error="<h2>Das hat gerade nicht geklappt.</h2><p>Bitte versuchen Sie es gleich noch einmal.</p>">
        <div class="advent-calendar__background" aria-hidden="true"><figure><img src="/bundles/hintergrund-weihnachtsstern.jpg" alt=""></figure></div>
        <?php if ($demo) { ?><p class="advent-calendar__demo">Vorschau: Alle Türchen sind zum Ausprobieren geöffnet.</p><?php } ?>
        <ol class="advent-calendar__grid">
            <?php foreach ($days as $day) { ?>
                <li class="advent-calendar__item<?= 24 === $day ? ' advent-calendar__item--big' : '' ?>">
                    <button type="button" class="advent-calendar__door" data-day="<?= $day ?>" aria-haspopup="dialog" aria-label="Türchen <?= $day ?>"><span class="advent-calendar__number"><?= $day ?></span></button>
                </li>
            <?php } ?>
        </ol>
        <dialog class="advent-calendar__dialog" aria-label="Türchen">
            <form method="dialog" class="advent-calendar__close-form"><button class="advent-calendar__close" aria-label="Schließen">×</button></form>
            <div class="advent-calendar__content" aria-live="polite"></div>
        </dialog>
    </div>
</main>
<script src="/bundles/advent-calendar.js" defer></script>
</body>
</html>
