<?php

include 'utilities.php';

include __DIR__.'/vendor/autoload.php';

use ICal\ICal;

function getPresentCyclingRaces(): object
{
    $presentCyclingRaces = new stdClass();

    $ical = new ICal('https://www.wielerkrant.be/wielrennen/wielerkalender24.ics', [
        'defaultSpan' => 2,     // Default value
        'defaultWeekStart' => 'MO',  // Default value
        'disableCharacterReplacement' => false, // Default value
        'filterDaysAfter' => null,  // Default value
        'filterDaysBefore' => true,  // Default value
        'httpUserAgent' => null,  // Default value
        'skipRecurrence' => false, // Default value
    ]);

    //Wedstrijden die nu bezig zijn
    $racesTodayiCal = $ical->eventsFromInterval('today');
    if ($racesTodayiCal) {
        foreach ($racesTodayiCal as $race) {

            $racesToday[] = $race->summary;

        }
        $presentCyclingRaces->racesToday = $racesToday;
    }

    //Wedstrijden die in de toekomst starten
    $racesFutureiCal = $ical->eventsFromRange('tomorrow', 'next year');
    foreach ($racesFutureiCal as $race) {

        $raceObject = new stdClass();
        $raceObject->name = $race->summary;
        $raceObject->dateString = getFormattedDate($race->dtstart);

        $raceObject->intervalString = getFormattedIntervalDays(date('Ymd'), $race->dtstart);

        $racesFuture[] = $raceObject;

    }
    $presentCyclingRaces->futureRaces = array_slice($racesFuture, 0, 5);

    return $presentCyclingRaces;
}

function getCyclingNews()
{
    $nuxml = simplexml_load_file('http://feeds.nos.nl/nossportwielrennen');

    return "Laatste wielrennieuws van nos.nl: \n[".$nuxml->channel->item[0]->title.']('.$nuxml->channel->item[0]->link.')';
}
