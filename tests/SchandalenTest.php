<?php

include 'cl_metaSchandalen.php';

test('Random schandaal', function (): void {
    $schandalenLijst = new schandalenLijst(__DIR__.'/fixtures/schandalen.json');
    $schandaal = $schandalenLijst->getWillekeurigSchandaal();
    expect($schandaal)->toBeInstanceOf(schandaal::class);
});

test('Laatste schandaal', function (): void {
    $schandalenLijst = new schandalenLijst(__DIR__.'/fixtures/schandalen.json');
    $schandaal = $schandalenLijst->getLaatsteSchandaal();
    expect($schandaal)->toBeInstanceOf(schandaal::class);
    expect($schandaal->bron)->toBe('Tweakers');
});

test('Tekst schandaal', function (): void {
    $schandalenLijst = new schandalenLijst(__DIR__.'/fixtures/schandalen.json');
    $schandaal = $schandalenLijst->getLaatsteSchandaal();
    $tekst = schandaalTekst::geefTekstLaatste($schandaal);
    expect($tekst)->toContain('Het laastste Meta schandaal:');
});
