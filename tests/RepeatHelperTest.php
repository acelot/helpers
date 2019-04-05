<?php

namespace Acelot\Helpers\Tests;

use function Acelot\Helpers\catch_only;
use function Acelot\Helpers\max_attempts;
use const Acelot\Helpers\MILLISECONDS;
use function Acelot\Helpers\pause;
use function Acelot\Helpers\repeat;
use function Acelot\Helpers\timeout;
use PHPUnit\Framework\TestCase;

class RepeatHelperTest extends TestCase
{
    public function testShouldFailOnTimeout()
    {
        $this->expectException(\Exception::class);

        repeat(
            function () {
                throw new \Exception('test');
            },
            timeout(1 * MILLISECONDS),
            pause(10)
        );
    }

    public function testSuccessAfterOneHundredMilliSecond()
    {
        try {
            $attempts = 0;
            $start = microtime(true);

            repeat(
                function () use (&$attempts) {
                    if (++$attempts > 10) {
                        return true;
                    }
                    throw new \Exception('test');
                },
                timeout(200 * MILLISECONDS),
                pause(10 * MILLISECONDS)
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

        repeat(
            function () {
                throw new \Exception('test');
            },
            max_attempts(5),
            pause(10 * MILLISECONDS)
        );
    }

    public function testSuccessAfterFiveTries()
    {
        try {
            $attempts = 0;

            $retries = repeat(
                function () use (&$attempts) {
                    if (++$attempts === 5) {
                        return $attempts;
                    }
                    throw new \Exception('test');
                },
                max_attempts(10),
                pause(1 * MILLISECONDS)
            );

            $this->assertEquals(5, $retries);
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    public function testCatchOnlyHandler()
    {
        $this->expectException(\RuntimeException::class);

        repeat(
            function () {
                throw new \RuntimeException();
            },
            max_attempts(2),
            catch_only(\OutOfBoundsException::class)
        );
    }

    public function testCatchOnlyHandler2()
    {
        try {
            $attempts = 0;

            repeat(
                function () use (&$attempts) {
                    $attempts++;
                    throw new \RuntimeException();
                },
                catch_only(\RuntimeException::class),
                max_attempts(2)
            );
        } catch (\RuntimeException $e) {
            $this->assertEquals(2, $attempts);
        }
    }
}
