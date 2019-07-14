<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Fizz\Date\NearestHours;
use Carbon\CarbonImmutable;

final class NearestHoursTest extends TestCase
{
    /**
     * @test
     */
    public function ny()
    {
        $nearest = new NearestHours;
        $actual = $nearest->nearestWorkingHours(
            new CarbonImmutable('2018-12-31 23:30', 'Europe/Kaliningrad')
        );
        $this->assertEquals(
            $expected = new CarbonImmutable('2019-01-09 12:00'),
            $actual
        );
    }
}
