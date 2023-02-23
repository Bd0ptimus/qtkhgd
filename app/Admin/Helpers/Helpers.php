<?php

use App\Models\SchoolClass;

/**
 * appends additional query string data to a url.
 * e.g. ?invoiceresource_type=project&?invoiceresource_id=28
 * Data is set via the [index] middleware
 * @param string $var current users setting
 * @return string css setting
 */
function urlResource($url = '') {

    if (request()->filled('resource_query')) {
        if (strpos($url, '?') !== false) {
            $url = $url . '&' . request('resource_query');
        } else {
            $url = $url . '?' . request('resource_query');
        }
    }

    //return complete ur;
    return url($url);
}


/**
 * this is a trusted transactional email template, coming from the database
 * @return string
 */

function cleanEmail($text = '') {
    $text = str_replace('<script>', '', $text);
    $text = str_replace('</script>', '', $text);
    return $text;
}


/**
 * Format the date accoring to the system setting
 * @return string bootstrap label class
 */
function runtimeDate($date = '') {

    if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00' || $date == '---') {
        return '---';
    }

    if ($date != '') {
        $date_format = config('Y-m-d');
        return \Carbon\Carbon::parse($date)->format($date_format);
    }

    return '---';
}

/**
 * @return string
 */
function mapGradeName($grade): string
{
    return SchoolClass::GRADES[$grade] ?? $grade;
}
