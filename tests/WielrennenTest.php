<?php

include 'wielrennen.php';

test('Als er geen wielrenkalender opgehaald kan worden, wordt er een melding getoond.', function (): void {
    $emptyArray = [];
    expect(getKoersenTekst($emptyArray, 20250101))->toBe('Kan geen wielrenkalender ophalen');
});

test('Er wordt geen koers gereden op de gekozen datum.', function (): void {

    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2025.json'));
    expect(getKoersenTekst($parsedICS, 20240101))->toBe("Er wordt vandaag niet gekoerst.\n");
});

test('Er wordt een koers gereden op de gekozen datum.', function (): void {
    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2025.json'));
    expect(getKoersenTekst($parsedICS, 20250330))->toStartWith('**Het is koers!**');
});

test('Er start morgen een koers', function (): void {
    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2025.json'));
    expect(getKoersenTekst($parsedICS, 20250401))->toContain('morgen');
});

test('Datum van koersen in de toekomst is correct', function (): void {
    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2025.json'));
    expect(getKoersenTekst($parsedICS, 20250331))->toContain('2 april start Dwars door Vlaanderen');
});

test('Meerdaagse koers die eerder dan vandaag is gestart', function (): void {
    $parsedICS = json_decode(file_get_contents('tests/fixtures/cyclingCalendar2025.json'));
    expect(getKoersenTekst($parsedICS, 20250325))->toContain('Vandaag is dag 2 van Volta a Catalunya');
});
