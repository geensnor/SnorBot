<?php

class TweedeKamer
{
    public function getActiviteit(DateTime $datum)
    {
        //https://gegevensmagazijn.tweedekamer.nl/OData/v4/2.0/Activiteit?$filter=date(Datum)%20eq%202024-11-04
        //TODO: alles
    }

    public function getGeschenk(): object|string
    {

        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        $geschenkResponseContent = file_get_contents('https://gegevensmagazijn.tweedekamer.nl/OData/v4/2.0/PersoonGeschenk?&$expand=%20Persoon&$orderby=Datum%20desc', false, stream_context_create($arrContextOptions));

        if ($geschenkResponseContent === false) {
            return 'Ophalen van de geschenken gaat niet helemaal goed.';
        } else {

            $geschenkResponse = json_decode($geschenkResponseContent);

            $geschenkObject = new stdClass();
            $geschenkObject->tekst = $geschenkResponse->value[0]->Omschrijving;
            $geschenkObject->naam = $geschenkResponse->value[0]->Persoon->Roepnaam.($geschenkResponse->value[0]->Persoon->Tussenvoegsel ? ' '.$geschenkResponse->value[0]->Persoon->Tussenvoegsel.' ' : ' ').$geschenkResponse->value[0]->Persoon->Achternaam;
            $geschenkObject->nummer = $geschenkResponse->value[0]->Persoon->Nummer;
            $geschenkObject->woonplaats = $geschenkResponse->value[0]->Persoon->Woonplaats;
            $datumObject = new DateTime('2024-10-24T00:00:00+02:00');

            $geschenkObject->datum = getFormattedDate(new DateTime($geschenkResponse->value[0]->Datum));

            return $geschenkObject;
        }
    }

    public function getActiviteitTekst()
    {
        //TODO zie: getActiviteit
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
