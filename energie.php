<?php

/**
 * getEnergie
 *
 * Haalt een hele berg data op van de API van electricitymap.org en geeft die hele berg data in een object terug.
 * Hier staat wat je er allemaal nog meer mee kan: https://docs.electricitymaps.com
 */
function getEnergie(): object
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.electricitymap.org/v3/power-breakdown/latest?zone=NL',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'auth-token: '.getenv('electricitymapKey'),
        ],
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error = curl_error($curl);
        echo "Error: $error";
        return (object) [];
    } else {
        $data = json_decode($response);
        return $data;
    }
}
