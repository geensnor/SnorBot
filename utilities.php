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

function getFormattedDate(DateTime $dateObject): string
{
    $maandNamenNederlands = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
    $dagNamenNederlands = ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'];

    return $dagNamenNederlands[$dateObject->format('w')].' '.$dateObject->format('j').' '.$maandNamenNederlands[$dateObject->format('n') - 1];
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
