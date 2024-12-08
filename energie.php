<?php

function getEnergie(): object
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.electricitymap.org/v3/power-breakdown/latest?zone=NL',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'auth-token: '.getenv('electricityKey'),
        ],
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error = curl_error($curl);
        echo "Error: $error";
    } else {
        $data = json_decode($response);
        curl_close($curl);

        return $data;
    }

}
