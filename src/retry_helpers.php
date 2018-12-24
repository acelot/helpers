<?php declare(strict_types=1);

namespace Acelot\Helpers;

const SECONDS = 1000000;
const MILLISECONDS = 1000;

/**
 * Repeats the callback until the answer is returned or timeout occurs.
 *
 * @param callable $callable Callback function
 * @param int      $timeout  Microseconds
 * @param int      $pause    Pause between repeats in microseconds
 *
 * @return mixed
 * @throws \Throwable
 */
function retry_timeout(callable $callable, int $timeout, int $pause = 0)
{
    $start = microtime(true);

    while (true) {
        try {
            return $callable();
        } catch (\Throwable $e) {
            if (microtime(true) - $start > ($timeout / SECONDS)) {
                throw $e;
            }
            usleep($pause);
        }
    }
}

/**
 * Repeats the callback until the answer is returned or the callback starts N times.
 *
 * @param callable $callable Callback function
 * @param int      $count    Max number of tries
 * @param int      $pause    Pause between repeats in microseconds
 *
 * @return mixed
 * @throws \Throwable
 */
function retry_count(callable $callable, int $count, int $pause = 0)
{
    while (true) {
        try {
            return $callable();
        } catch (\Throwable $e) {
            if (--$count === 0) {
                throw $e;
            }
            usleep($pause);
        }
    }
}
