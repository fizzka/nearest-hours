<?php declare(strict_types=1);

namespace Fizz\Date;

use Carbon\CarbonInterface;

final class NearestHours
{
    public function nearestWorkingHours(CarbonInterface $date, $hours = ['11:00', '21:00']): CarbonInterface
    {
        $start = $date->setTimeFromTimeString($hours[0]);
        $end = $date->setTimeFromTimeString($hours[1]);

        $date = $this->modifyDateWithWorkingHours($date, $start, $end);

        return $date->setTimeZone(date_default_timezone_get());
    }

    public function nearestWorkingHours2(CarbonInterface $date, $hours = ['11:00', '21:00']): CarbonInterface
    {
        $start = $date->setTimeFromTimeString($hours[0]);
        $end = $date->setTimeFromTimeString($hours[1]);

        $date = $this->modifyDateWithWorkingHours2($date, $start, $end);

        return $date->setTimeZone(date_default_timezone_get());
    }

    private function modifyDateWithWorkingHours($date, $start, $end)
    {
        $nearestWorkingDay = new NearestWorkingDay;

        if ($nearestWorkingDay->isDayoff($date) || $date > $end) {
            $date = $nearestWorkingDay->nearestWorkingDay($start->addDay());
        } elseif ($date < $start) {
            $date = $start;
        }

        return $date;
    }

    private function modifyDateWithWorkingHours2($date, $start, $end)
    {
        if ($date > $end) {
            $date = $start->addDay();
        } elseif ($date < $start) {
            $date = $start;
        }

        return $date;
    }
}
