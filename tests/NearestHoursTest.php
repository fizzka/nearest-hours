<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Fizz\Date\NearestHours;
use Carbon\CarbonImmutable;

final class NearestHoursTest extends TestCase
{
    public function dates()
    {
        return [
            ['2017-10-11 17:01:25', '2017-10-11 17:01:25'],
            ['2017-10-11 11:00', '2017-10-11 11:00'],
            ['2017-10-11 21:00', '2017-10-11 21:00'],
            ['2017-10-11 21:01', '2017-10-12 11:00'],
            ['2017-10-11 07:40', '2017-10-11 11:00'],
            ['2018-10-11 11:00', '2019-01-09 11:00'],
        ];
    }

    /**
     * @test
     * @dataProvider dates
     */
    public function workHours($initial, $expected)
    {
        $actual = (new NearestHours)->nearestWorkingHours(new CarbonImmutable($initial));
        $this->assertEquals(new CarbonImmutable($expected), $actual);
    }

    public function testNyHolidays()
    {
        $nearest = new NearestHours;

        $expected = new CarbonImmutable('2019-01-09 12:00');
        $actual = $nearest->nearestWorkingHours(
            new CarbonImmutable('2018-12-31 23:30', 'Europe/Kaliningrad')
        );
        $this->assertEquals($expected, $actual);
    }

    public function testScenario1()
    {
        $nearest = new NearestHours;

        $dateModifier = function ($date) {
            return $date->modify('+1 day +4 hour');
        };

        // Если +4 часа след. дня будет позже 21:00, перезвон послезавтра в 11:00
        $ts = $dateModifier(new CarbonImmutable('2017-10-11 17:01:00'));
        $expected = new CarbonImmutable('2017-10-13 11:00:00');
        // $actual = \Z\packages\Helper::getSoonestWorkingTime($ts, 0, ['11:00', '21:00']);
        $actual = $nearest->nearestWorkingHours($ts);
        $this->assertEquals($expected, $actual);
    }

    public function testScenarios()
    {
        $nearest = new NearestHours;

        $dateModifier = function ($date) {
            return $date->modify('+1 day +4 hour');
        };

        $ts = $dateModifier(new CarbonImmutable('2017-10-14 13:48:30'));

        // Пример прямо из MAIN-2972. Середина дня, и завтра это же время + 4 часа попадает в рабочее время.
        $expected = new CarbonImmutable('2017-10-15 17:48:30');
        $actual = $nearest->nearestWorkingHours2($ts->setTimeZone('Europe/Kaliningrad'));
        $this->assertEquals($expected, $actual);

        // Тест на пропуск выходного дня, с разницей в таймзонах
        $expected = new CarbonImmutable('2017-10-16 12:00:00');
        $actual = $nearest->nearestWorkingHours($ts->setTimeZone('Europe/Kaliningrad'));
        $this->assertEquals($expected, $actual);

        // Тест на пропуск выходного дня, без разницы в таймзонах
        $expected = new CarbonImmutable('2017-10-16 11:00:00');
        $actual = $nearest->nearestWorkingHours($ts);
        $this->assertEquals($expected, $actual);
    }
}
