<?php declare(strict_types=1);

namespace Acelot\Helpers;

const SECONDS = 1000000;
const MILLISECONDS = 1000;

/**
 * Repeats the specified function if it threw an exception.
 *
 * @param callable $callable    Repeatable function.
 * @param callable ...$handlers Exception handlers.
 *
 * @return mixed
 *
 * @example
 *     // Repeats anonymous function (if it's threw an exception):
 *     // - repeats should take 10 seconds maximum
 *     // - maximum 5 attempts
 *     // - wait 100 ms before each attempt
 *     $callableResult = repeat(
 *         function () { ... },
 *         timeout(10 * SECONDS),
 *         max_attempts(5),
 *         pause(100 * MILLISECONDS)
 *     )
 */
function repeat(callable $callable, callable ...$handlers)
{
    $attempt = 0;
    $start = microtime(true);

    while (true) {
        try {
            return $callable();
        } catch (\Throwable $e) {
            $attempt++;
            foreach ($handlers as $handler) {
                $handler($e, $attempt, microtime(true) - $start);
            }
        }
    }
}

/**
 * Allows you to catch only the specified list of exceptions.
 *
 * @param string ...$exceptions Fully qualified class names of exceptions.
 *
 * @return callable
 */
function catch_only(string ...$exceptions): callable
{
    return function (\Throwable $e, int $attempt, float $elapsed) use ($exceptions) {
        foreach ($exceptions as $exception) {
            if ($e instanceof $exception) {
                return;
            }
        }
        throw $e;
    };
}

/**
 * Throws a caught exception and stops the repetition when a timeout is reached.
 *
 * @param int $microseconds
 *
 * @return callable
 */
function timeout(int $microseconds): callable
{
    return function (\Throwable $e, int $attempt, float $elapsed) use ($microseconds) {
        if ($elapsed > $microseconds / SECONDS) {
            throw $e;
        }
    };
}

/**
 * Throws a caught exception and stops the repetition when it reaches the specified number of attempts.
 *
 * @param int $number
 *
 * @return callable
 */
function max_attempts(int $number): callable
{
    return function (\Throwable $e, int $attempt, float $elapsed) use ($number) {
        if ($attempt >= $number) {
            throw $e;
        }
    };
}

/**
 * Adds a pause between repetitions.
 *
 * @param int $microseconds
 *
 * @return callable
 */
function pause(int $microseconds): callable
{
    return function (\Throwable $e, int $attempt, float $elapsed) use ($microseconds) {
        usleep($microseconds);
    };
}
