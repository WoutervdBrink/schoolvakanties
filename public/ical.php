<?php

use Knevelina\Schoolvakanties\RijksoverheidApi;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $vacations = RijksoverheidApi::getVacations();
} catch (RuntimeException $e) {
    http_response_code(500);
    echo $e->getMessage();
    die;
}

$calendar = Calendar::create();

foreach ($vacations as $vacation) {
    foreach ($vacation->ranges as $range) {
        $name = $vacation->type;
        $regions = $range->getHumanReadableRegions();

        if ($regions !== null) {
            $name .= ' (regio ' . $regions . ')';
        }

        $calendar->event(Event::create($name)
            ->startsAt($range->start)
            ->endsAt($range->end)
        );
    }
}

header('Content-Type: text/calendar; charset=utf-8');
echo $calendar->get();