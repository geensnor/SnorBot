<?php

require 'energie.php';

test('Er wordt nog energie gebruikt in Nederland', function (): void {
    $energieObject = getEnergie();
    expect($energieObject->powerConsumptionTotal)->not->toBeNull();
});
