<?php

include 'cl_Locatie.php';

test('Hotspot met de op twee na kortste afstand kan gevonden worden', function () {
    $collectie = HotspotCollectie::vanBestand(__DIR__.'/fixtures/hotspots.geojson');
    expect($collectie->dichtstbijzijnde(new Locatie(50.862330, 4.874687), 3)[2]->hotspot->naam)->toBe('Cafeterapie');
});

test('Random hotspot kan gevonden worden', function () {
    $collectie = HotspotCollectie::vanBestand(__DIR__.'/fixtures/hotspots.geojson');
    expect($collectie->geefRandom()->naam)->not->toBeNull();
});
