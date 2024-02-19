<?php

include 'functies.php';

test('Bitcoin prijs', function () {
    expect(getBitcoinPrice())->toBeString();
});

test('Vandaag van wikipedia', function () {
    $event = getVandaag();
    expect($event->year)->toBeString()->toHaveLength(4);
    expect($event->content)->toBeString();
});

test('Mop', function () {
    expect(getMop())->toBeString();
});
