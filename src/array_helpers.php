<?php declare(strict_types=1);

namespace Acelot\Helpers;

/**
 * Determines that the array is "flat" - all keys are numeric, starts from zero, sequential and haven't breaks.
 *
 * @param array $arr Input array
 *
 * @return bool
 */
function is_array_flat(array $arr): bool
{
    if (empty($arr)) {
        return true;
    }

    return array_keys($arr) === range(0, count($arr) - 1);
}

/**
 * Determines that the all elements of the array is scalar.
 *
 * @param array $arr Input array
 *
 * @return bool
 */
function is_array_scalar(array $arr): bool
{
    foreach ($arr as $value) {
        if (!is_scalar($value)) {
            return false;
        }
    }

    return true;
}

/**
 * Defines that an array has an element along a specific path.
 *
 * @example $a = [
 *     'items' => [
 *          [
 *              'person' => [
 *                  'surname' => 'John',
 *                  'name' => 'Doe'
 *              ]
 *          ],
 *          ...
 *     ]
 * ];
 *
 * $isFirstPersonHasName = array_has($a, '0.person.name'); // true
 *
 * @param array  $arr       Input array
 * @param string $path      Path
 * @param string $delimiter Path delimiter
 *
 * @return bool
 */
function array_has(array $arr, string $path, string $delimiter = '.'): bool
{
    try {
        array_req($arr, $path, $delimiter);
        return true;
    } catch (\OutOfBoundsException $e) {
        return false;
    }
}

/**
 * Returns an array element along a specific path. Returns default value if path not exists.
 *
 * @param array  $arr       Input array
 * @param string $path      Path
 * @param mixed  $default   Default value
 * @param string $delimiter Path delimiter
 *
 * @return mixed
 */
function array_get(array $arr, string $path, $default = null, string $delimiter = '.')
{
    try {
        return array_req($arr, $path, $delimiter);
    } catch (\OutOfBoundsException $e) {
        return $default;
    }
}

/**
 * Same as `array_get`, but throws an exception if path not exists.
 *
 * @param array  $arr       Input array
 * @param string $path      Path
 * @param string $delimiter Path delimiter
 *
 * @return mixed
 */
function array_req(array $arr, string $path, string $delimiter = '.')
{
    $value = $arr;
    $parts = explode($delimiter, $path);

    foreach ($parts as $path) {
        if (!is_array($value) || !array_key_exists($path, $value)) {
            throw new \OutOfBoundsException(sprintf('Key "%s" not exists in array', $path));
        }

        $value = $value[$path];
    }

    return $value;
}

/**
 * Returns the first element of an array. Returns default value if array is empty.
 *
 * @param array $arr     Input array
 * @param mixed $default Default value
 *
 * @return mixed
 */
function array_first(array $arr, $default = null)
{
    if (empty($arr)) {
        return $default;
    }

    return reset($arr);
}

/**
 * Returns the first element of the array that satisfies the callback, otherwise returns default value.
 *
 * @param array    $arr      Input array
 * @param callable $callback Conditional function
 * @param mixed    $default  Default value
 *
 * @return mixed
 */
function array_find(array $arr, callable $callback, $default = null)
{
    foreach ($arr as $key => $value) {
        if ($callback($value, $key)) {
            return $value;
        }
    }

    return $default;
}

/**
 * Same as `array_find`, but returns the key instead of value.
 *
 * @param array    $arr      Input array
 * @param callable $callback Conditional function
 *
 * @return int|string|false
 */
function array_find_index(array $arr, callable $callback)
{
    foreach ($arr as $key => $value) {
        if ($callback($value, $key)) {
            return $key;
        }
    }

    return false;
}

/**
 * Converts array to object. Equivalent to `json_decode(json_encode($arr))`.
 *
 * @param array         $arr       Input array
 * @param callable|null $processor Callback that applies to each element
 *
 * @return array|object
 */
function array_to_object(array $arr, callable $processor = null)
{
    $flat = is_array_flat($arr);
    $out = $flat ? [] : (object)[];

    foreach ($arr as $key => $value) {
        $value = is_array($value)
            ? array_to_object($value, $processor)
            : ($processor ? $processor($value) : $value);

        if ($flat) {
            $out[] = $value;
        } else {
            $out->{$key} = $value;
        }
    }

    return $out;
}
