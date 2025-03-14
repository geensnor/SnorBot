<?php

include 'cl_TweedeKamer.php';

include 'utilities.php';

test('Geschenken Tweede Kamer ophalen', function () {
    $tk = new TweedeKamer();
    $tkObject = $tk->getGeschenk();
    expect($tkObject)->toBeObject();
});

test('Activiteitentekst op een zondag, zonder activiteiten', function () {
    $tk = new TweedeKamer();
    $tijd = new DateTime('2025-01-12T16:00:00+01:00'); //Is een zondag
    $activiteitTekst = $tk->getActiviteitTekst($tijd);
    expect($activiteitTekst)->toContain('Er is vandaag niet veel te doen in de Tweede Kamer');
});

test('Activiteiten op een maandag, met activiteiten', function () {
    $tk = new TweedeKamer();
    $tijd = new DateTime('2024-11-04T16:00:00+01:00'); //Is een maandag
    $activiteitTekst = $tk->getActiviteitTekst($tijd);
    expect($activiteitTekst)->toContain('alles in de Tweede Kamer. Zo is er om');
});
