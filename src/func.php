<?php declare(strict_types=1);

use Carbon\{CarbonInterface,CarbonImmutable,CarbonPeriod};

// https://isdayoff.ru/api/getdata?year=2019
function getDayoffMap2019(): string
{
    return '11111111000110000011000001100000110000011000001100000110000011000011100000110000011000001100000110000011000001100000110011111000111100000110000011000001100000110010011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000111000011000001100000110000011000001100000110000011000001100';
}

function isDayoff(CarbonInterface $date): bool
{
    return (int)$date->year !== 2019
        ? true
        : (bool)getDayoffMap2019()[$date->dayOfYear - 1]
    ;
}

function nearestWorkingDay(CarbonInterface $date): CarbonInterface
{
    $period = CarbonPeriod::create($date);
    $period->setDateClass(CarbonImmutable::class);
    $period->filter(function ($date) {
        return !isDayoff($date);
    });

    return $period->current();
}

function nearestWorkingHours(CarbonInterface $date, $hours = ['11:00', '21:00']): CarbonInterface
{
    $start = $date->setTimeFromTimeString($hours[0]);
    $end = $date->setTimeFromTimeString($hours[1]);

    if (isDayoff($date) || $date > $end) {
        $date = nearestWorkingDay($start->addDay());
    } elseif ($date < $start) {
        $date = $start;
    }

    return $date->setTimeZone(date_default_timezone_get());
}

// $date = new CarbonImmutable('2018-12-31 23:30', 'Europe/Kaliningrad');
// $date2 = nearestWorkingHours($date);
// dump($date, $date2);
