<?php

include 'cl_weekenden.php';

test('Weekendtekst voor tekst met jaartal', function (): void {

    $weekend = new weekend;
    $weekendenJSON = json_decode(file_get_contents('tests/fixtures/weekenden.json'));
    $weekend->setWeekenden($weekendenJSON);
    $weekendTekst = $weekend->getWeekendText('Wat was er in 2015?');
    expect($weekendTekst)->toBe('In 2015 gingen we naar Rotterdam: Havenstad');
});

test('Weekendtekst voor tekst zonder jaartal', function (): void {

    $weekend = new weekend;
    $weekendenJSON = json_decode(file_get_contents('tests/fixtures/weekenden.json'));
    $weekend->setWeekenden($weekendenJSON);
    $weekendTekst = $weekend->getWeekendText('Wat was er zonder jaartal?');
    expect($weekendTekst)->toBe('Dit is geen jaartal');
});

test('Weekendtekst tekst met jaartal die niet voorkomt', function (): void {

    $weekend = new weekend;
    $weekendenJSON = json_decode(file_get_contents('tests/fixtures/weekenden.json'));
    $weekend->setWeekenden($weekendenJSON);
    $weekendTekst = $weekend->getWeekendText('Wat was er in 2115?');
    expect($weekendTekst)->toBe('In 2115 gingen we geen weekend weg');
});
