<?php

/**
 * Een klasse die dingen ophaalt die met het weer te maken hebben
 *
 * @todo Meer weer methoden toevoegen die nog verspreid zijn over de codebase. Doet nu alleen iets met de UV index
 */
class Weer
{
    public function getUvData(float $lat = 51.92, float $lng = 5.66, int $alt = 10, string $dt = ''): object
    {
        $url = sprintf(
            'https://api.openuv.io/api/v1/uv?lat=%s&lng=%s&alt=%s&dt=%s',
            urlencode((string) $lat),
            urlencode((string) $lng),
            urlencode((string) $alt),
            urlencode($dt)
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-access-token: openuv-5rnrmrbpnrdo-io',
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            return (object) [
                'success' => false,
                'error' => $error,
                'status' => $status,
            ];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return (object) [
                'success' => false,
                'error' => 'Invalid JSON response',
                'response' => $response,
                'status' => $status,
            ];
        }

        $uvMaxTimeUnformatted = new DateTime($data['result']['uv_max_time'], new DateTimeZone('Europe/Amsterdam'));

        return (object) [
            'success' => $status >= 200 && $status < 300,
            'status' => $status,
            'uvNow' => $data['result']['uv'] ?? null,
            'uvMax' => $data['result']['uv_max'] ?? null,
            'uvMaxTime' => $uvMaxTimeUnformatted->format('H:i'),
            'safe_exposure_time' => $data['result']['safe_exposure_time']['st3'] ?? null,
        ];
    }

    private function getUvNowText(stdClass $uvData): string
    {
        $uvNow = $uvData->uvNow;
        $saveExposureTime = $uvData->safe_exposure_time;
        if ($uvNow < 2) {
            return 'De UV index is op dit moment maar '.$uvNow.', je kan prima zonder te smeren de zon in. Als de zon er uberhaupt is.';
        } elseif ($uvNow < 4) {
            return 'De UV index is op dit moment'.$uvNow.' let toch een beetje op en smeer je voor de zekerheid in. Met een normale huid kun je toch nog '.$saveExposureTime.' minuten in de zon.';
        } elseif ($uvNow < 6) {
            return 'De UV index is op dit moment'.$uvNow.' het gaat dus hard nu. Met een normale huid kun je nu maar '.$saveExposureTime.' minuten in de zon.';
        } else {
            return 'De UV index is nu maar liefst '.$uvNow.'. Echt heel goed smeren! Met een normale huid kun je nu maar '.$saveExposureTime.' minuten in de zon voordat je volledig verschroeid.';
        }
    }

    private function getUvMaxText(stdClass $uvData): string
    {
        $uvMax = $uvData->uvMax;
        $uvMaxTime = $uvData->uvMaxTime;

        if ($uvMax < 2) {
            return 'De maximale UV index vandaag is maar '.$uvMax.' om '.$uvMaxTime.'. Lekker naar buiten zonder plakkerige zonnebrand!';
        } elseif ($uvMax < 4) {
            return 'De maximale UV index vandaag is '.$uvMax.' om '.$uvMaxTime.'. Dan moet je je wel even insmeren.';
        } elseif ($uvMax < 6) {
            return 'De maximale UV index vandaag is '.$uvMax.' om '.$uvMaxTime.' uur. Best hefig...';
        } else {
            return 'De maximale UV index vandaag is '.$uvMax.' om '.$uvMaxTime.' uur. Dat is echt heel veel.';
        }
    }

    /**
     * getUvText
     *
     * Plakt de tekst van de UV index nu en de maximale UV index vandaag aan elkaar
     *
     * @return string
     */

    public function getUvText(stdClass $uvData): string
    {
        return $this->getUvNowText($uvData).' '.$this->getUvMaxText($uvData);
    }
}
