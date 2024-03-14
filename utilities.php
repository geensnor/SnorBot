<?php

include __DIR__.'/vendor/autoload.php';

use ICal\ICal;

function getParsedCalendar(string $calendarLocation): array
{

    $ical = new ICal($calendarLocation, [
        'defaultSpan' => 2,     // Default value
        'defaultWeekStart' => 'MO',  // Default value
        'disableCharacterReplacement' => false, // Default value
        'filterDaysAfter' => null,  // Default value
        'filterDaysBefore' => null,  // Default value
        'httpUserAgent' => null,  // Default value
        'skipRecurrence' => false, // Default value
    ]);

    return $ical->events();

}

function getFormattedDate(object $dateObject): string
{
    $maandNamenEngels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $maandNamenNederlands = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];

    return str_replace($maandNamenEngels, $maandNamenNederlands, $dateObject->format('j F'));
}

function getFormattedIntervalDays(string $startDate, string $endDate): string
{

    $dateStartObject = DateTime::createFromFormat('Ymd', $startDate);
    $dateEndObject = DateTime::createFromFormat('Ymd', $endDate);

    $interval = $dateStartObject->diff($dateEndObject);

    if ($interval->days == 1) {
        return '1 dag';
    } else {
        return $interval->days.' dagen';
    }

}
