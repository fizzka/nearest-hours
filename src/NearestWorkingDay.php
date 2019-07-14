<?php declare(strict_types=1);

namespace Fizz\Date;

use Carbon\CarbonInterface;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

final class NearestWorkingDay
{
    // https://isdayoff.ru/api/getdata?year=2019
    private $dayoffMap = [
        2017 => '11111111000001100000110000011000001100000110000011000111100000110010011000001100000110000011000001100000110000011000001110000111100011000001100000110000011000001110000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001110000110000011000001100000110000011000001100000110000011',
        2019 => '11111111000110000011000001100000110000011000001100000110000011000011100000110000011000001100000110000011000001100000110011111000111100000110000011000001100000110010011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000110000011000001100000111000011000001100000110000011000001100000110000011000001100',
    ];

    private function getDateStateFromMap(int $day, int $year): bool
    {
        return (bool)$this->dayoffMap[$year][$day - 1];
    }

    public function isDayoff(CarbonInterface $date): bool
    {
        return array_key_exists($date->year, $this->dayoffMap)
            ? $this->getDateStateFromMap($date->dayOfYear, $date->year)
            : true
        ;
    }

    public function nearestWorkingDay(CarbonInterface $date): CarbonInterface
    {
        $period = CarbonPeriod::create($date);
        // $period->setDateClass(CarbonImmutable::class);
        $period->filter(function ($date) {
            return !$this->isDayoff($date);
        });

        return $period->current();
    }
}
