<?php

namespace Acelot\Helpers\Tests;

use Acelot\Helpers\Retry;
use PHPUnit\Framework\TestCase;

class RetryTest extends TestCase
{
    public function testShouldFailOnTimeout()
    {
        $this->expectException(\Exception::class);

        Retry::create(function () {
            throw new \Exception('test');
        }, 1 * Retry::MILLISECONDS, -1, 10)->run();
    }

    public function testSuccessAfterOneHundredMilliSecond()
    {
        $i = 0;

        try {
            $start = microtime(true);

            Retry::create(
                function () use (&$i) {
                    if (++$i > 10) {
                        return true;
                    }
                    throw new \Exception('test');
                },
                200 * Retry::MILLISECONDS,
                -1,
                10 * Retry::MILLISECONDS
            )->run();

            $end = microtime(true) - $start;
            $this->assertTrue($end > 0.1 && $end < 0.2);
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    public function testShouldFailOnMaxCount()
    {
        $this->expectException(\Exception::class);

        Retry::create(function () {
            throw new \Exception('test');
        }, -1, 5, 10 * Retry::MILLISECONDS)->run();
    }

    public function testSuccessAfterFiveTries()
    {
        $i = 0;

        try {
            $retries = Retry::create(function () use (&$i) {
                if (++$i === 5) {
                    return $i;
                }
                throw new \Exception('test');
            }, -1, 10, 1 * Retry::MILLISECONDS)->run();

            $this->assertEquals(5, $retries);
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    public function testShouldCatchOnlySpecificException()
    {
        $this->expectException(\Exception::class);

        Retry::create(function () {
            throw new \Exception('test');
        })
            ->setExceptionType(\InvalidArgumentException::class)
            ->run();
    }

    public function testShouldCatchOnlySpecificException2()
    {
        $this->expectException(\InvalidArgumentException::class);

        Retry::create(function () {
            throw new \InvalidArgumentException('test');
        }, -1, 3)
            ->setExceptionType(\InvalidArgumentException::class)
            ->run();
    }

    public function testShouldCallHooks()
    {
        $counter = 0;
        $out = '';

        Retry::create(function () use (&$counter) {
            if (++$counter > 2) {
                return true;
            }
            throw new \InvalidArgumentException('test');
        })
            ->setHook(Retry::BEFORE_PAUSE_HOOK, function () use (&$out) {
                $out .= 'B';
            })
            ->setHook(Retry::AFTER_PAUSE_HOOK, function () use (&$out) {
                $out .= 'A';
            })
            ->run();

        $this->assertEquals('BABA', $out);
    }
}
