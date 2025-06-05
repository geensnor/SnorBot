<?php

include 'functies.php';

test('Bitcoin prijs', function (): void {
    expect(getBitcoinPrice())->toBeString();
});

test('Vandaag van wikipedia', function (): void {
    $event = getVandaag();
    expect($event->year)->toBeString();
    expect($event->content)->toBeString();
})->skip('website doet het niet, test even overslaan.');

test('Simpele lijsten laatste', function (): void {
    $laatsteItemTestLijst = getItemSimpeleLijst("laatste", "tests/fixtures/simpeleLijstenLijst.json");
    expect($laatsteItemTestLijst)->toBe("Simpel item 3");
});

test('Simpele lijsten willekeurig', function (): void {
    $laatsteItemTestLijst = getItemSimpeleLijst("willekeurig", "tests/fixtures/simpeleLijstenLijst.json");
    expect($laatsteItemTestLijst)->toBeString();
});

test('Simpele lijsten niet bestaande tekst', function (): void {
    $laatsteItemTestLijst = getItemSimpeleLijst("niet bestaande tekst", "tests/fixtures/simpeleLijstenLijst.json");
    expect($laatsteItemTestLijst)->toBeFalse();
});
