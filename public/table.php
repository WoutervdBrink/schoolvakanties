<?php

use Knevelina\Schoolvakanties\RijksoverheidApi;

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Amsterdam');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schoolvakanties &mdash; Alle vakanties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container">
    <h1>Schoolvakanties</h1>

    <p>In deze tabel staat een overzicht van alle schoolvakanties per regio.</p>

    <p>Op de <a href="index.php">homepagina</a> vind je instructies om deze data aan je digitale agenda toe te voegen.</p>

    <?php
    try {
        $vacations = RijksoverheidApi::getVacations();
    } catch (RuntimeException $e) {
        $vacations = [];
    }
    ?>

    <table class="table">
        <thead>
        <tr>
            <th>Type</th>
            <th>Regio('s)</th>
            <th>Start</th>
            <th>Eind</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($vacations)): ?>
        <tr>
            <td colspan="4">De vakanties konden niet worden geladen.</td>
        </tr>
        <?php endif; ?>

        <?php foreach ($vacations as $vacation): ?>
            <?php $first = true; foreach ($vacation->ranges as $range): ?>
                <tr>
                    <?php if ($first): ?>
                        <th rowspan="<?=count($vacation->ranges)?>"><?=$vacation->type?></th>
                    <?php endif; ?>
                    <td>
                        <?= is_null($range->regions)
                            ? 'Heel Nederland'
                            : ucfirst(htmlspecialchars($range->getHumanReadableRegions())) ?>
                    </td>
                    <td><?= $range->start->format('Y-m-d') ?></td>
                    <td><?= $range->end->format('Y-m-d') ?></td>
                </tr>
            <?php $first = false; endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="https://github.com/WoutervdBrink/schoolvakanties" target="_blank">Broncode</a></p>
</div>

</body>
</html>