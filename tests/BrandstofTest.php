<?php

include 'brandstof.php';

beforeEach(function () {

    $this->fuelObject = getFuelPrices();

});

test('Station met de hoogste prijs', function (): void {

    expect($this->fuelObject->highestPriceStation->organization)->toBeString();
});

test('Gemiddelde branstofprijs', function (): void {
    expect($this->fuelObject->averagePrice)->toBeFloat();
});
