<?php declare(strict_types=1);

namespace Acelot\Helpers;

const SECONDS = Retry::SECONDS;
const MILLISECONDS = Retry::MILLISECONDS;

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
    return Retry::create($callable, $timeout, -1, $pause)->run();
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
    return Retry::create($callable, -1, $count, $pause)->run();
}
