<?php declare(strict_types=1);

namespace Acelot\Helpers;

const PATH_SPEC = "/->\w+|->{'[^']+'}|\[\d+\]|\['[^']+'\]|\[#first\]|\[#last\]|(?<error>.+)/u";

/**
 * Same as `req_by_path`, but returns default value if path not found.
 *
 * @param mixed  $var
 * @param string $path
 * @param mixed  $default
 *
 * @return mixed
 * @throws \InvalidArgumentException
 */
function get_by_path($var, string $path, $default = null)
{
    try {
        return req_by_path($var, $path);
    } catch (\OutOfBoundsException $e) {
        return $default;
    }
}

/**
 * Returns nested value of any array or an object along a specific path.
 * Throws an OutOfBoundsException if path not found.
 *
 * @param mixed  $var
 * @param string $path
 *
 * @return mixed
 * @throws \InvalidArgumentException
 * @throws \OutOfBoundsException
 */
function req_by_path($var, string $path)
{
    if ($path === '') {
        return $var;
    }

    if (!preg_match_all(PATH_SPEC, $path, $matches)) {
        throw new \InvalidArgumentException('Invalid path');
    }

    if (!empty(array_filter($matches['error']))) {
        throw new \InvalidArgumentException('Invalid path');
    }

    $pointer = &$var;

    foreach ($matches[0] as $part) {
        // Objects
        if (mb_strcut($part, 0, 2) === '->') {
            if (!is_object($pointer)) {
                throw new \OutOfBoundsException('Path not found');
            }

            if (mb_strcut($part, 2, 1) === '{') {
                $prop = mb_strcut($part, 4, -2);
            } else {
                $prop = mb_strcut($part, 2);
            }

            if (!property_exists($pointer, $prop)) {
                throw new \OutOfBoundsException('Path not found');
            }
            $pointer = &$pointer->{$prop};
            continue;
        }

        // Arrays
        if (mb_strcut($part, 0, 1) === '[') {
            if (!is_array($pointer)) {
                throw new \OutOfBoundsException();
            }

            $key = trim($part, '[]');
            if ($key === '#first') {
                reset($pointer);
                $key = key($pointer);
            } elseif ($key === '#last') {
                end($pointer);
                $key = key($pointer);
            } else {
                $key = trim($key, '\'');
            }

            if (!array_key_exists($key, $pointer)) {
                throw new \OutOfBoundsException();
            }
            $pointer = &$pointer[$key];
            continue;
        }
    }

    return $pointer;
}
