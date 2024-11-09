<?php

class TweedeKamer
{
    private $arrContextOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];

    public function getRandomActiviteitOpDag(DateTime $datum): object|string
    {

        $activiteitenResponseContent = file_get_contents('https://gegevensmagazijn.tweedekamer.nl/OData/v4/2.0/Activiteit?$filter=date(Datum)%20eq%20'.$datum->format('Y-m-d'), false, stream_context_create($this->arrContextOptions));
        if ($activiteitenResponseContent === false) {
            return 'Ophalen van de activiteiten gaat niet helemaal goed.';
        } else {

            $activiteitenResponse = json_decode($activiteitenResponseContent);
            if (! $activiteitenResponse->value) {
                return 'Er is vandaag niet veel te doen in de Tweede Kamer';
            } else {
                $randomActiviteit = $activiteitenResponse->value[array_rand($activiteitenResponse->value)];

                $activiteitObject = new stdClass;
                $activiteitObject->soort = $randomActiviteit->Soort;
                $aanvangtijdObject = new DateTime($randomActiviteit->Aanvangstijd);

                $activiteitObject->tijd = $aanvangtijdObject->format('H:i');
                $activiteitObject->onderwerp = $randomActiviteit->Onderwerp;

                return $activiteitObject;
            }
        }

    }

    public function getGeschenk(): object|string
    {

        $geschenkResponseContent = file_get_contents('https://gegevensmagazijn.tweedekamer.nl/OData/v4/2.0/PersoonGeschenk?&$expand=%20Persoon&$orderby=Datum%20desc', false, stream_context_create($this->arrContextOptions));

        if ($geschenkResponseContent === false) {
            return 'Ophalen van de geschenken gaat niet helemaal goed.';
        } else {

            $geschenkResponse = json_decode($geschenkResponseContent);

            $geschenkObject = new stdClass;
            $geschenkObject->tekst = $geschenkResponse->value[0]->Omschrijving;
            $geschenkObject->naam = $geschenkResponse->value[0]->Persoon->Roepnaam.($geschenkResponse->value[0]->Persoon->Tussenvoegsel ? ' '.$geschenkResponse->value[0]->Persoon->Tussenvoegsel.' ' : ' ').$geschenkResponse->value[0]->Persoon->Achternaam;
            $geschenkObject->nummer = $geschenkResponse->value[0]->Persoon->Nummer;
            $geschenkObject->woonplaats = $geschenkResponse->value[0]->Persoon->Woonplaats;
            $datumObject = new DateTime('2024-10-24T00:00:00+02:00');

            $geschenkObject->datum = getFormattedDate(new DateTime($geschenkResponse->value[0]->Datum));

            return $geschenkObject;
        }
    }

    public function getActiviteitTekst(DateTime $datum): string
    {
        $randomActiviteit = $this->getRandomActiviteitOpDag($datum);
        if (is_object($randomActiviteit)) {
            return 'Er gebeurt vandaag weer van alles in de Tweede Kamer. Zo is er om '.$randomActiviteit->tijd.' een '.$randomActiviteit->soort.' over '.$randomActiviteit->onderwerp;
        } else {
            return $randomActiviteit;
        }
    }

    public function getGeschenkTekst(): string
    {
        $geschenkObject = $this->getGeschenk();

        if (is_object($geschenkObject)) {

            return 'Het laatste geschenk voor kamerleden is van '.$geschenkObject->datum.'. ['.$geschenkObject->naam.'](https://berthub.eu/tkconv/persoon.html?nummer='.$geschenkObject->nummer.') uit '.$geschenkObject->woonplaats.' kreeg: '.$geschenkObject->tekst;
        } else {
            return $geschenkObject;
        }
    }
}
