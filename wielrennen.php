<?php

require_once 'utilities.php';

function getKoersenTekst(array $parsedICS, int $referentieDatum): string
{

    if (! isset($parsedICS[0])) {
        return 'Kan geen wielrenkalender ophalen';
    } else {
        $koersenTekst = '';
        $koersenTekstBinnenkort = '';
        foreach ($parsedICS as $koers) {
            //Actuele koersen
            if ($koers->dtstart == $referentieDatum && $koers->dtend == $referentieDatum + 1) {//Eendaagse koers, vandaag
                $koersenTekst .= " \nVandaag wordt ".$koers->summary.' gereden.';
            } elseif ($koers->dtstart <= $referentieDatum && $koers->dtend - 1 >= $referentieDatum) {//Meerdaagse koers, vandaag bezig

                if ($koers->dtstart == $referentieDatum) {//Meerdaagse koers, en de start is vandaag
                    $koersenTekst .= " \nVandaag start ".$koers->summary.'. Deze duurt tot en met '.getFormattedDate((DateTime::createFromFormat('Ymd', $koers->dtend))->modify('-1 day'));

                } elseif ($koers->dtend - 1 == $referentieDatum) {

                    $koersenTekst .= "\nVandaag is de finish van ".$koers->summary.'.';

                } else {//Meerdaagse koers en hij is eerder gestart
                    $dagVanKoers = $referentieDatum - $koers->dtstart + 1;
                    $koersenTekst .= "\nVandaag is dag ".$dagVanKoers.' van '.$koers->summary.'. Deze duurt tot en met '.getFormattedDate(DateTime::createFromFormat('Ymd', $koers->dtend)->modify('-1 day')).'.';
                }

            }

            //Binnenkort
            if (strtotime((string) $koers->dtstart) > strtotime((string) $referentieDatum) && strtotime((string) $koers->dtstart) < strtotime('+1 week', strtotime((string) $referentieDatum))) {
                if ($koers->dtstart == date('Ymd', strtotime('+1 day', strtotime((string) $referentieDatum)))) {
                    $startTekst = 'morgen';
                } else {
                    $startTekst = getFormattedDate(DateTime::createFromFormat('Ymd', $koers->dtstart));
                }
                $koersenTekstBinnenkort .= "\n- ".$startTekst.' start '.$koers->summary.'.';
                if ((strtotime((string) $koers->dtend) - strtotime((string) $koers->dtstart)) > 86400) {

                    $koersenTekstBinnenkort .= ' Deze duurt tot en met '.getFormattedDate((DateTime::createFromFormat('Ymd', $koers->dtend))->modify('-1 day'));
                }
            }
        }

        if ($koersenTekst) {
            $koersenTekst = "**Het is koers!** \n".$koersenTekst;
        } else {
            $koersenTekst = "Er wordt vandaag niet gekoerst.\n";
        }

        if ($koersenTekstBinnenkort) {
            $koersenTekst .= "\n\nBinnenkort:".$koersenTekstBinnenkort;
        }

        return $koersenTekst;
    }
}

function getCyclingNews(): string
{
    $nuxml = simplexml_load_file('http://feeds.nos.nl/nossportwielrennen');

    return "Laatste wielrennieuws van nos.nl: \n[".$nuxml->channel->item[0]->title.']('.$nuxml->channel->item[0]->link.')';
}
