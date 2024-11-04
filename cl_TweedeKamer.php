<?php

class TweedeKamer
{
    public function getActiviteit(DateTime $datum)
    {
        //https://gegevensmagazijn.tweedekamer.nl/OData/v4/2.0/Activiteit?$filter=date(Datum)%20eq%202024-11-04
        //TODO: alles
    }

    public function getGeschenk(): object
    {

        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        $geschenkResponse = json_decode(file_get_contents('https://gegevensmagazijn.tweedekamer.nl/OData/v4/2.0/PersoonGeschenk?&$expand=%20Persoon&$orderby=Datum%20desc', false, stream_context_create($arrContextOptions)));

        $geschenkObject = new stdClass();
        $geschenkObject->tekst = $geschenkResponse->value[0]->Omschrijving;
        $geschenkObject->naam = $geschenkResponse->value[0]->Persoon->Roepnaam.($geschenkResponse->value[0]->Persoon->Tussenvoegsel ? ' '.$geschenkResponse->value[0]->Persoon->Tussenvoegsel.' ' : ' ').$geschenkResponse->value[0]->Persoon->Achternaam;
        $geschenkObject->nummer = $geschenkResponse->value[0]->Persoon->Nummer;
        $geschenkObject->woonplaats = $geschenkResponse->value[0]->Persoon->Woonplaats;
        $datumObject = new DateTime('2024-10-24T00:00:00+02:00');

        $geschenkObject->datum = getFormattedDate(new DateTime($geschenkResponse->value[0]->Datum));

        return $geschenkObject;
    }

    public function getActiviteitTekst()
    {
        //TODO zie: getActiviteit
    }

    public function getGeschenkTekst(): string
    {
        $geschenkObject = $this->getGeschenk();

        return 'Het laatste geschenk van '.$geschenkObject->datum.' is voor ['.$geschenkObject->naam.'](https://berthub.eu/tkconv/persoon.html?nummer='.$geschenkObject->nummer.') uit '.$geschenkObject->woonplaats.': \n\n'.$geschenkObject->tekst;
    }
}
