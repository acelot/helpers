<?php declare(strict_types=1);

namespace Acelot\Helpers\Tests;

use function Acelot\Helpers\interval_to_string;
use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    public function intervalToStringProvider()
    {
        return [
            ['PT0S', 'PT0S'],
            ['PT0M0S', 'PT0S'],
            ['PT0H0M0S', 'PT0S'],
            ['P1DT0S', 'P1D'],
            ['P1DT0H0M1S', 'P1DT1S'],
            ['P1Y0M0D', 'P1Y'],
            ['P1Y1M1DT0H0M0S', 'P1Y1M1D'],
            ['P10Y10M10DT10H10M10S', 'P10Y10M10DT10H10M10S'],
            ['P0Y0M1DT0H0M1S', 'P1DT1S'],
            ['P1Y1DT1H1S', 'P1Y1DT1H1S'],
        ];
    }

    /**
     * @dataProvider intervalToStringProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testIntervalToString($input, $expected)
    {
        $this->assertEquals($expected, interval_to_string(new \DateInterval($input)));
    }
}
