<?php declare(strict_types=1);

namespace Acelot\Helpers;

/**
 * Converts \DateInterval object to ISO8601 duration format.
 *
 * @param \DateInterval $interval
 *
 * @return string
 */
function interval_to_string(\DateInterval $interval): string
{
    [$date, $time] = explode('T', $interval->format('P%yY%mM%dDT%hH%iM%sS'));
    $date = str_replace(['M0D', 'Y0M', 'P0Y'], ['M', 'Y', 'P'], $date);
    $time = rtrim(str_replace(['M0S', 'H0M', 'T0H'], ['M', 'H', 'T'], "T$time"), 'T');
    $result = $date . $time;
    if ($result === 'P') {
        return 'PT0S';
    }

    return $result;
}

