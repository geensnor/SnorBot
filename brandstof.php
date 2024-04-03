<?php

function getFuelPrices(): object
{

    $brandstofResponse = json_decode(file_get_contents('https://www.tankplanner.nl/api/v1/price/euro95/'));

    $lowestPriceStation = new stdClass();
    $lowestPriceStation->price = INF;

    $highestPriceStation = new stdClass();
    $highestPriceStation->price = -INF;

    $total = 0;

    foreach ($brandstofResponse as $tankstation) {
        if ($tankstation->price > $highestPriceStation->price) {
            $highestPriceStation = $tankstation;
        }

        if ($tankstation->price < $lowestPriceStation->price) {
            $lowestPriceStation = $tankstation;
        }

        $total = $total + $tankstation->price;

    }

    $fuelObject = new stdClass();

    $fuelObject->averagePrice = $total / count($brandstofResponse);
    $fuelObject->lowestPriceStation = $lowestPriceStation;

    $fuelObject->highestPriceStation = $highestPriceStation;

    return $fuelObject;

}
