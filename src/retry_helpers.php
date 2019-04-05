<?php declare(strict_types=1);

namespace Acelot\Helpers;

use const Acelot\Helpers\Repeat\SECONDS as REPEAT_SECONDS;
use const Acelot\Helpers\Repeat\MILLISECONDS as REPEAT_MILLISECONDS;
use function Acelot\Helpers\Repeat\max_attempts;
use function Acelot\Helpers\Repeat\pause;
use function Acelot\Helpers\Repeat\repeat;
use function Acelot\Helpers\Repeat\timeout;

const SECONDS = REPEAT_SECONDS;
const MILLISECONDS = REPEAT_MILLISECONDS;

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
    return repeat($callable, timeout($timeout), pause($pause));
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
    return repeat($callable, max_attempts($count), pause($pause));
}
