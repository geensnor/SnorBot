<?php

/**
 * verjaardag
 */
class verjaardag
{
    private $geboortedatums;

    //Geboortedatums apart zetten voor het testen
    public function setGeboortedatums(array $geboortedatums): void
    {
        $this->geboortedatums = $geboortedatums;
    }

    /**
     * getVerjaardagen
     *
     * Geboortedatums uit GitHub Repo halen
     */
    public function getVerjaardagen(): void
    {
        $GithHubAPIUrl = 'https://api.github.com/repos/reithose/snorbot-extra/contents/';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, 'reithose:'.getenv('githubToken'));
        curl_setopt($curl, CURLOPT_USERAGENT, 'User-Agent: reithose');
        curl_setopt($curl, CURLOPT_URL, $GithHubAPIUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if (! curl_exec($curl)) {
            exit('Error: "'.curl_error($curl).'" - Code: '.curl_errno($curl));
        } else {
            $curlResult = json_decode(curl_exec($curl));
        }

        curl_close($curl);

        $this->geboortedatums = json_decode(file_get_contents($curlResult[0]->download_url));
    }

    public function getVerjaardagenData(DateTime $referentieDatum): array
    {
        $verjaardagenData = [];
        foreach ($this->geboortedatums as $key => $value) {
            if (isset($this->geboortedatums[$key]->geboortedatum)) {
                $geboortedatumDateTime = DateTime::createFromFormat('Y-m-d', $this->geboortedatums[$key]->geboortedatum);

                $verjaardagDitJaar = DateTime::createFromFormat('Ymd', $referentieDatum->format('Y').$geboortedatumDateTime->format('m').$geboortedatumDateTime->format('d'));

                $persoon = new stdClass();

                $persoon->naam = $this->geboortedatums[$key]->naam;
                $persoon->geboortedatum = $geboortedatumDateTime->format('d-m-Y');
                $leeftijdObject = date_diff($geboortedatumDateTime, $referentieDatum);
                $persoon->leeftijdJaren = $leeftijdObject->y;
                $verjaardagObject = date_diff($verjaardagDitJaar, $referentieDatum);


                if ($verjaardagDitJaar < $referentieDatum) {//Als de verjaardag al is geweest dit jaar, naar volgend jaar kijken
                    $volgendeVerjaardag = DateTime::createFromFormat('Ymd', ($referentieDatum->format('Y') + 1).$geboortedatumDateTime->format('m').$geboortedatumDateTime->format('d'));
                    $persoon->datumVerjaardag = $volgendeVerjaardag->format('d-m-Y');
                } else {
                    $volgendeVerjaardag = $verjaardagDitJaar;
                    $persoon->datumVerjaardag = $verjaardagDitJaar->format('d-m-Y');
                }


                $verjaardagObject = date_diff($volgendeVerjaardag, $referentieDatum);
                $persoon->dagenTotVerjaardag = $verjaardagObject->days;






                $verjaardagenData[] = $persoon;
                // }

                if (count($verjaardagenData) > 0) {// usort geeft vervelende error als er geen data opgehaald kan worden.
                    usort($verjaardagenData, fn ($a, $b): int => (int) ($a->dagenTotVerjaardag - $b->dagenTotVerjaardag));
                }
            }
        }

        return $verjaardagenData;
    }

    public function getVerjaardagTekst(DateTime $referentieDatum): string
    {
        $verjaardagenData = $this->getVerjaardagenData($referentieDatum);
        if ($verjaardagenData[0]->dagenTotVerjaardag == 0) { //Vandaag iemand jarig
            if (isset($verjaardagenData[1]->dagenTotVerjaardag) && $verjaardagenData[1]->dagenTotVerjaardag == 0) {//Twee jarigen vandaag
                return 'Hoera! '.$verjaardagenData[0]->naam.' en '.$verjaardagenData[1]->naam.' zijn vandaag jarig! '.$verjaardagenData[0]->naam.' wordt '.($verjaardagenData[0]->leeftijdJaren + 1).' en '.$verjaardagenData[1]->naam.' wordt '.($verjaardagenData[1]->leeftijdJaren + 1).' jaar oud. Gefeliciteerd beide!';
            } else {//Een jarige vandaag
                return 'Hoera! '.$verjaardagenData[0]->naam.' wordt vandaag '.($verjaardagenData[0]->leeftijdJaren + 1).' jaar oud!';
            }
        } elseif ($verjaardagenData[0]->dagenTotVerjaardag == 1) {//Morgen iemand jarig
            if (isset($verjaardagenData[1]->dagenTotVerjaardag) && $verjaardagenData[1]->dagenTotVerjaardag == 1) {//Twee jarigen morgen
                return 'Morgen zijn '.$verjaardagenData[0]->naam.' en '.$verjaardagenData[1]->naam.' jarig! '.$verjaardagenData[0]->naam.' wordt '.($verjaardagenData[0]->leeftijdJaren + 1).' jaar oud en '.$verjaardagenData[1]->naam.' wordt '.($verjaardagenData[1]->leeftijdJaren + 1).'.';
            } else { //Een jarige morgen
                return $verjaardagenData[0]->naam.' is de volgende die jarig is. Hij/zij wordt morgen ('.$verjaardagenData[0]->datumVerjaardag.') '.($verjaardagenData[0]->leeftijdJaren + 1).' jaar!';
            }
        } else {//Vandaag en morgen is niemand jarig. Dan maar uit de toekomst ophalen
            if (isset($verjaardagenData[1]->dagenTotVerjaardag) && ($verjaardagenData[0]->dagenTotVerjaardag == $verjaardagenData[1]->dagenTotVerjaardag)) {//Twee jarigen in de toekomst
                return $verjaardagenData[0]->naam.' en '.$verjaardagenData[1]->naam.' zijn over '.$verjaardagenData[0]->dagenTotVerjaardag.' dagen jarig. Zij zijn de volgende die jarig zijn. '.$verjaardagenData[0]->naam.' wordt '.($verjaardagenData[0]->leeftijdJaren + 1).' jaar oud en '.$verjaardagenData[1]->naam.' wordt '.($verjaardagenData[1]->leeftijdJaren + 1).' jaar oud.';
            } else {
                return $verjaardagenData[0]->naam.' is de volgende die jarig is. Hij/zij wordt over '.$verjaardagenData[0]->dagenTotVerjaardag.' dagen ('.$verjaardagenData[0]->datumVerjaardag.'), '.($verjaardagenData[0]->leeftijdJaren + 1).' jaar.';
            }
        }
    }
}
