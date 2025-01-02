<?php

include 'cl_verjaardagen.php';

test('Eén jarige vandaag', function (): void {
    $v = new verjaardag();
    $verjaardagenJSON = json_decode(file_get_contents('tests/fixtures/verjaardagen.json'));

    $v->setGeboortedatums($verjaardagenJSON);

    $testDate = new DateTime('2024-03-03 00:00:00');
    $verjaardagTekst = $v->getVerjaardagTekst($testDate);

    expect($verjaardagTekst)->toBe('Hoera! Alice wordt vandaag 40 jaar oud!');
});

test('Eén jarige morgen', function (): void {
    $v = new verjaardag();
    $verjaardagenJSON = json_decode(file_get_contents('tests/fixtures/verjaardagen.json'));
    $v->setGeboortedatums($verjaardagenJSON);
    $testDate = new DateTime('2024-03-02 00:00:00');
    $verjaardagTekst = $v->getVerjaardagTekst($testDate);

    expect($verjaardagTekst)->toBe('Alice is de volgende die jarig is. Hij/zij wordt morgen (03-03-2024) 40 jaar!');
});

test('Eén jarige over een tijd', function (): void {
    $v = new verjaardag();
    $verjaardagenJSON = json_decode(file_get_contents('tests/fixtures/verjaardagen.json'));
    $v->setGeboortedatums($verjaardagenJSON);
    $testDate = new DateTime('2024-02-10 00:00:00');
    $verjaardagTekst = $v->getVerjaardagTekst($testDate);
    expect($verjaardagTekst)->toBe('Alice is de volgende die jarig is. Hij/zij wordt over 22 dagen (03-03-2024), 40 jaar.');
});

test('Twee jarigen vandaag', function (): void {
    $v = new verjaardag();
    $verjaardagenJSON = json_decode(file_get_contents('tests/fixtures/verjaardagen.json'));
    $v->setGeboortedatums($verjaardagenJSON);

    $testDate = new DateTime('2024-01-02 00:00:00');
    $verjaardagTekst = $v->getVerjaardagTekst($testDate);

    expect($verjaardagTekst)->toBe('Hoera! Bob en Henk zijn vandaag jarig! Bob wordt 30 en Henk wordt 25 jaar oud. Gefeliciteerd beide!');
});

test('Twee jarigen morgen', function (): void {
    $v = new verjaardag();
    $verjaardagenJSON = json_decode(file_get_contents('tests/fixtures/verjaardagen.json'));
    $v->setGeboortedatums($verjaardagenJSON);

    $testDate = new DateTime('2024-02-01 00:00:00');
    $verjaardagTekst = $v->getVerjaardagTekst($testDate);

    expect($verjaardagTekst)->toBe('Morgen zijn Marieke en Jan jarig! Marieke wordt 37 jaar oud en Jan wordt 43.');
});

test('Twee jarigen over een tijd', function (): void {
    $v = new verjaardag();
    $verjaardagenJSON = json_decode(file_get_contents('tests/fixtures/verjaardagen.json'));
    $v->setGeboortedatums($verjaardagenJSON);

    $testDate = new DateTime('2024-01-12 00:00:00');
    $verjaardagTekst = $v->getVerjaardagTekst($testDate);

    expect($verjaardagTekst)->toBe('Marieke en Jan zijn over 21 dagen jarig. Zij zijn de volgende die jarig zijn. Marieke wordt 37 jaar oud en Jan wordt 43 jaar oud.');
});

test('Verjaardag over jaargrens heen', function (): void {
    $v = new verjaardag();
    $verjaardagenJSON = json_decode(file_get_contents('tests/fixtures/verjaardagen.json'));
    $v->setGeboortedatums($verjaardagenJSON);
    $testDate = new DateTime('2024-12-30 10:00:00');
    $verjaardagTekst = $v->getVerjaardagTekst($testDate);

    expect($verjaardagTekst)->toBe('Bob en Henk zijn over 3 dagen jarig. Zij zijn de volgende die jarig zijn. Bob wordt 31 jaar oud en Henk wordt 26 jaar oud.');
});
