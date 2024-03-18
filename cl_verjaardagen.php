<?php

class verjaardag
{
    public $geboortedatums;

    public function __construct()
    {
        $GithHubAPIUrl = 'https://api.github.com/repos/reithose/geboortedatums/contents/';

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

    /**
     * @return array<mixed, array<'dagenTotVerjaardag'|'datumVerjaardag'|'geboortedatum'|'leeftijdDagen'|'leeftijdJaren'|'naam', mixed>>
     */
    public function getVerjaardagenData(): array
    {
        $referentieDatum = strtotime(date('Y-m-d'));
        $verjaardagenData = [];
        foreach ($this->geboortedatums as $key => $value) {
            $verjaardagDitJaar = strtotime(date('Y').date('-m-d', strtotime((string) $this->geboortedatums[$key]->geboortedatum)));
            $verschil = $verjaardagDitJaar - $referentieDatum;

            $persoon['naam'] = $this->geboortedatums[$key]->naam;
            $persoon['geboortedatum'] = date('d-m-Y', strtotime((string) $this->geboortedatums[$key]->geboortedatum));
            $leeftijdObject = date_diff(date_create($this->geboortedatums[$key]->geboortedatum), date_create(date('Y-m-d')));
            $persoon['leeftijdJaren'] = $leeftijdObject->y;
            $persoon['leeftijdDagen'] = $leeftijdObject->days;

            if ($verschil >= 0) {
                $persoon['dagenTotVerjaardag'] = round($verschil / 86400);
                $persoon['datumVerjaardag'] = date('d-m-Y', $verjaardagDitJaar);
            } else {//Verjaardag is al geweest dit jaar
                $verjaardagVolgendJaar = strtotime(date('Y', strtotime('+1 year')).date('-m-d', strtotime((string) $this->geboortedatums[$key]->geboortedatum)));
                $verschil = $verjaardagVolgendJaar - $referentieDatum;
                $persoon['dagenTotVerjaardag'] = round($verschil / 86400);
                $persoon['datumVerjaardag'] = date('d-m-Y', $verjaardagVolgendJaar);
            }
            $verjaardagenData[] = $persoon;
        }

        if (count($verjaardagenData) > 0) {// usort geeft vervelende error als er geen data opgehaald kan worden.
            usort($verjaardagenData, fn (array $a, array $b): float => $a['dagenTotVerjaardag'] - $b['dagenTotVerjaardag']);
        }

        return $verjaardagenData;
    }

    public function getVerjaardagTekst(): string
    {
        $verjaardagenData = $this->getVerjaardagenData();
        if ($verjaardagenData[0]['dagenTotVerjaardag'] == 0) {
            return 'Hoera! '.$verjaardagenData[0]['naam'].' wordt vandaag '.($verjaardagenData[0]['leeftijdJaren']).' jaar oud!';
        } elseif ($verjaardagenData[0]['dagenTotVerjaardag'] == 1) {
            return $verjaardagenData[0]['naam'].' is de volgende die jarig is. Hij/zij wordt morgen ('.$verjaardagenData[0]['datumVerjaardag'].') '.($verjaardagenData[0]['leeftijdJaren'] + 1).' jaar!';
        } else {
            return $verjaardagenData[0]['naam'].' is de volgende die jarig is. Hij/zij wordt over '.$verjaardagenData[0]['dagenTotVerjaardag'].' dagen ('.$verjaardagenData[0]['datumVerjaardag'].') '.($verjaardagenData[0]['leeftijdJaren'] + 1).' jaar. ';
        }
    }

    public function checkKomendeDagen(): string
    {
        $returnString = '';
        $verjaardagenData = $this->getVerjaardagenData();
        if ($verjaardagenData[0]['dagenTotVerjaardag'] == 0) {
            $returnString = 'Hoera! '.$verjaardagenData[0]['naam'].' wordt vandaag '.($verjaardagenData[0]['leeftijdJaren']).' jaar oud!';
        }
        if ($verjaardagenData[0]['dagenTotVerjaardag'] == 1) {
            $returnString = 'Morgen wordt '.$verjaardagenData[0]['naam'].' al weer '.($verjaardagenData[0]['leeftijdJaren'] + 1).' jaar oud!';
        }

        return $returnString;
    }
}
