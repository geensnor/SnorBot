<?php

include 'wielrennen.php';

test('Als er geen wielrenkalender opgehaald kan worden, wordt er een melding getoond.', function (): void {
    $emptyArray = [];
    expect(getKoersenTekst($emptyArray, '20240101'))->toBe('Kan geen wielrenkalender ophalen');
});

test('Er wordt geen koers gereden op de gekozen datum.', function (): void {

    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2024.json'));
    expect(getKoersenTekst($parsedICS, '20240101'))->toBe("Er wordt vandaag niet gekoerst.\n");
});

test('Er wordt een koers gereden op de gekozen datum.', function (): void {
    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2024.json'));
    expect(getKoersenTekst($parsedICS, '20240302'))->toStartWith('**Het is koers!**');

});

test('Er start morgen een koers', function (): void {
    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2024.json'));
    expect(getKoersenTekst($parsedICS, '20240301'))->toContain('morgen');
});

test('Datum van koersen in de toekomst is correct', function (): void {
    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2024.json'));
    expect(getKoersenTekst($parsedICS, '20240314'))->toContain('16 maart start Milano');
});
