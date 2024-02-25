<?php

function getFormattedDate(string $date): string
{
    $maandNamenEngels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $maandNamenNederlands = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];

    $dateObject = DateTime::createFromFormat('Ymd', $date);

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
