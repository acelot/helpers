<?php

namespace Acelot\Helpers\Tests;

use const Acelot\Helpers\MILLISECONDS;
use function Acelot\Helpers\retry_count;
use function Acelot\Helpers\retry_timeout;
use PHPUnit\Framework\TestCase;

class RetryHelpersTest extends TestCase
{
    public function testShouldFailOnTimeout()
    {
        $this->expectException(\Exception::class);

        retry_timeout(function () {
            throw new \Exception('test');
        }, 1 * MILLISECONDS, 10);
    }

    public function testSuccessAfterOneHundredMilliSecond()
    {
        $i = 0;

        try {
            $start = microtime(true);

            retry_timeout(
                function () use (&$i) {
                    if (++$i > 10) return true;
                    throw new \Exception('test');
                },
                200 * MILLISECONDS,
                10 * MILLISECONDS
            );

            $end = microtime(true) - $start;
            $this->assertTrue($end > 0.1 && $end < 0.2);
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    public function testShouldFailOnMaxCount()
    {
        $this->expectException(\Exception::class);

        retry_count(function () {
            throw new \Exception('test');
        }, 5, 10 * MILLISECONDS);
    }

    public function testSuccessAfterFiveTries()
    {
        $i = 0;

        try {
            $retries = retry_count(function () use (&$i) {
                if (++$i === 5) return $i;
                throw new \Exception('test');
            }, 1 * MILLISECONDS, 10);

            $this->assertEquals(5, $retries);
        } catch (\Exception $e) {
            $this->fail();
        }
    }
}
