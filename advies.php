<?php

function distanceGeoPoints($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 3958.75; //miles
    //$earthRadius = 6.378;

    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
       cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
       sin($dLng / 2) * sin($dLng / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $dist = $earthRadius * $c;

    // from miles
    $meterConversion = 1.609;
    $geopointDistance = $dist * $meterConversion;

    return $geopointDistance;
    //return $dist;
}

function getAdviesArray($inputLat, $inputLon)
{
    $adviesArray = json_decode(file_get_contents('https://raw.githubusercontent.com/geensnor/SnorLijsten/master/advies.json'));

    foreach ($adviesArray as $key => $value) {
        $adviesArray[$key]->distance = round(distanceGeoPoints($inputLat, $inputLon, $adviesArray[$key]->lat, $adviesArray[$key]->lon), 2);
    }

    usort($adviesArray, function ($a, $b) {
        return $a->distance - $b->distance;
    });

    return $adviesArray;
}
