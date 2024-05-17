<?php

include 'brandstof.php';

test('Station met de hoogste prijs', function (): void {

    $fuelObject = getFuelPrices();

    expect($fuelObject->highestPriceStation->organization)->toBeString();
})->skip('brandstof site doet het niet meer');

test('Gemiddelde branstofprijs', function (): void {

    $fuelObject = getFuelPrices();

    expect($fuelObject->averagePrice)->toBeFloat();
})->skip('brandstof site doet het niet meer');
