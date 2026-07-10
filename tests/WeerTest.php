<?php

include 'cl_Weer.php';

test('UV index tekst ziet er goed uit', function () {
    $weer = new Weer();

    $uvObject = (object) [
        'success' => 1,
        'status' => 200,
        'uvNow' => 6.5498,
        'uvMax' => 2.6617,
        'uvMaxTime' => '12:43',
        'safe_exposure_time' => 172
    ];
    $expectString = "De UV index is nu maar liefst 6.5498. Echt heel goed smeren! Met een normale huid kun je nu maar 172 minuten in de zon voordat je volledig verschroeid. De maximale UV index vandaag is 2.6617 om 12:43. Dan moet je je wel even insmeren.";
    expect($expectString)->toBe($weer->getUvText($uvObject));
});
