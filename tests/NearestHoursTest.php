<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Carbon\CarbonImmutable;

final class NearestHoursTest extends TestCase
{
    /**
     * @test
     */
    public function ny()
    {
        $this->assertEquals(
            new CarbonImmutable('2019-01-09 12:00'),
            nearestWorkingHours(new CarbonImmutable('2018-12-31 23:30', 'Europe/Kaliningrad'))
        );
    }
}
