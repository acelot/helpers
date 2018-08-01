<?php declare(strict_types=1);

namespace Acelot\Helpers;

const TK_TRIM = 1;
const TK_LOWERCASE = 2;
const TK_UPPERCASE = 4;
const TK_TO_INT = 8;
const TK_NO_EMPTY = 16;
const TK_DEFAULTS = TK_TRIM | TK_NO_EMPTY;

/**
 * Converts underscore string to camelcase.
 *
 * @example "brown_fox_jumps_over_lazy_dog" -> "BrownFoxJumpsOverLazyDog"
 *
 * @param string $underscore Underscore string
 * @param bool   $lcfirst    Lower case first letter
 *
 * @return string
 */
function camelcase(string $underscore, bool $lcfirst = false): string
{
    $words = ucwords(strtolower(str_replace('_', ' ', $underscore)));
    if ($lcfirst) {
        $words = lcfirst($words);
    }

    return str_replace(' ', '', $words);
}

/**
 * Converts camelcase string to underscore.
 *
 * @example "BrownFoxJumpsOverLazyDog" -> "brown_fox_jumps_over_lazy_dog"
 *
 * @param string $camelcase
 *
 * @return string
 */
function underscore(string $camelcase): string
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelcase));
}

/**
 * Splits the string into parts by delimiter and returns as array.
 *
 * @param string $string    Input string
 * @param string $delimiter Delimiter (comma by default)
 * @param int    $options   Operations performing for each part (* - default options):
 *                          - *TK_TRIM = trim whitespaces
 *                          - TK_LOWERCASE = lowercase
 *                          - TK_UPPERCASE = uppercase
 *                          - TK_TO_INT = convert part to integer
 *                          - *TK_NO_EMPTY = ignore empty part
 *
 * @return array
 */
function tokenize(string $string, $delimiter = ',', int $options = TK_DEFAULTS): array
{
    $arr = preg_split("/\\$delimiter/u", $string);

    $arr = array_map(function ($item) use ($options) {
        if ($options & TK_TRIM) {
            $item = trim($item);
        }

        if ($options & TK_LOWERCASE) {
            $item = mb_strtolower($item);
        }

        if ($options & TK_UPPERCASE) {
            $item = mb_strtoupper($item);
        }

        if ($options & TK_TO_INT) {
            $item = intval($item);
        }

        return $item;
    }, $arr);

    if ($options & TK_NO_EMPTY) {
        $arr = array_filter($arr);
    }

    if ($options & TK_TO_INT) {
        $arr = array_map('intval', $arr);
    }

    return $arr;
}
