<?php

/**
 * Kleine klasse met wat gedoe voor latitude en longitude.
 *
 */
class Coordinaten
{
    public function __construct(
        public readonly float $lat,
        public readonly float $lng,
    ) {
    }
}


/**
 * Klasse die dingen doet met latitude en longitude met de website latlng.work.
 * @see https://api.latlng.work/docs
 */
class Latlng
{
    private const BASE_URL = 'https://api.latlng.work/api';

    private string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $apiKey ??= getenv('latlng') ?: ($_ENV['latlng'] ?? $_SERVER['latlng'] ?? null);

        if (empty($apiKey)) {
            throw new RuntimeException('API-key niet gevonden: zet de environment variabele "latlng".');
        }

        $this->apiKey = $apiKey;
    }

    /**
     * Zoekt coördinaten op voor een plaatsnaam, bijvoorbeeld "Molenhoek".
     *
     * @return Coordinaten De gevonden coördinaten
     * @throws InvalidArgumentException als de query leeg is
     * @throws RuntimeException bij cURL-, HTTP- of JSON-fouten
     */
    public function geefCoordinaten(string $query): Coordinaten
    {
        $url = self::BASE_URL.'?'.http_build_query(['q' => $query]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['X-Api-Key: '.$this->apiKey],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);

            throw new RuntimeException('cURL-fout: '.$error);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        if ($status !== 200) {
            throw new RuntimeException("Latlng API gaf HTTP-status $status terug: $response");
        }

        $data = json_decode($response, true);
        $coordinates = $data['features'][0]['geometry']['coordinates'] ?? null;

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Ongeldige JSON: '.json_last_error_msg());
        }

        return new Coordinaten(lat: (float) $coordinates[1], lng: (float) $coordinates[0]);

    }
}
