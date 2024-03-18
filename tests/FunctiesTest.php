<?php

include 'functies.php';

test('Bitcoin prijs', function (): void {
    expect(getBitcoinPrice())->toBeString();
});

test('Vandaag van wikipedia', function (): void {
    $event = getVandaag();
    expect($event->year)->toBeString();
    expect($event->content)->toBeString();
});

test('Mop', function (): void {
    expect(getMop())->toBeString();
});
