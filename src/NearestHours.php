<?php declare(strict_types=1);

namespace Fizz\Date;

use Carbon\{CarbonInterface,CarbonImmutable,CarbonPeriod};

final class NearestHours
{
    // https://isdayoff.ru/api/getdata?year=2019
    private $dayoffMap = [
        2017 => '11111111000001100000110000011000001100000110000011000111100000110010011000001100000110000011000001100000110000011000001110000111100011000001100000110000011000001110000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001110000110000011000001100000110000011000001100000110000011',
        2019 => '11111111000110000011000001100000110000011000001100000110000011000011100000110000011000001100000110000011000001100000110011111000111100000110000011000001100000110010011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000111000011000001100000110000011000001100000110000011000001100',
    ];

    private function isDayoff(CarbonInterface $date): bool
    {
        return array_key_exists($date->year, $this->dayoffMap)
            ? (bool)$this->dayoffMap[$date->year][$date->dayOfYear - 1]
            : true
        ;
    }

    private function nearestWorkingDay(CarbonInterface $date): CarbonInterface
    {
        $period = CarbonPeriod::create($date);
        // $period->setDateClass(CarbonImmutable::class);
        $period->filter(function ($date) {
            return !$this->isDayoff($date);
        });

        return $period->current();
    }

    public function nearestWorkingHours(CarbonInterface $date, $hours = ['11:00', '21:00']): CarbonInterface
    {
        $start = $date->setTimeFromTimeString($hours[0]);
        $end = $date->setTimeFromTimeString($hours[1]);

        if ($this->isDayoff($date) || $date > $end) {
            $date = $this->nearestWorkingDay($start->addDay());
        } elseif ($date < $start) {
            $date = $start;
        }

        return $date->setTimeZone(date_default_timezone_get());
    }

    public function nearestWorkingHours2(CarbonInterface $date, $hours = ['11:00', '21:00']): CarbonInterface
    {
        $start = $date->setTimeFromTimeString($hours[0]);
        $end = $date->setTimeFromTimeString($hours[1]);

        if ($date > $end) {
            $date = $start->addDay();
        } elseif ($date < $start) {
            $date = $start;
        }

        return $date->setTimeZone(date_default_timezone_get());
    }
}
