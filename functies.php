<?php

function getKabinet(): string
{
    $kabinetBericht = '';
    $nosFeed = simplexml_load_file('https://feeds.nos.nl/nosnieuwspolitiek');
    foreach ($nosFeed->channel->item as $nosItem) {
        if (strpos($nosItem->description, 'kabinet') || strpos($nosItem->description, 'asiel') || strpos($nosItem->description, 'stikstof')) {
            $kabinetBericht = '['.$nosItem->title.']('.$nosItem->link.')';
            break;
        }
    }

    return $kabinetBericht;
}

function getBitcoinPrice(): string
{
    $url = 'https://api.kucoin.com/api/v1/market/stats?symbol=BTC-EUR';
    $jsonData = file_get_contents($url);

    // Check if the data was fetched correctly
    if ($jsonData === false) {
        return 'ojee, kucoin doet het niet';
    }

    $response = json_decode($jsonData);

    // Check if the JSON was decoded correctly
    if ($response === null) {
        return 'ojee, kucoin geeft geen valide json';
    }

    // Ensure we are accessing the correct properties
    if (! isset($response->data->last)) {
        return 'ojee, geen laatste waarde in de json ';
    }

    $bitcoinPrice = $response->data->last;

    // Return the formatted price
    return 'Bitcoin prijs: € '.number_format($bitcoinPrice, 2, ',', '.');
}

function getEthereumPrice(): string
{
    $url = 'https://api.kucoin.com/api/v1/market/stats?symbol=ETH-EUR';
    $jsonData = file_get_contents($url);

    // Check if the data was fetched correctly
    if ($jsonData === false) {
        return 'ojee, kucoin doet het niet';
    }

    $response = json_decode($jsonData);

    // Check if the JSON was decoded correctly
    if ($response === null) {
        return 'ojee, kucoin geeft geen valide json';
    }

    // Ensure we are accessing the correct properties
    if (! isset($response->data->last)) {
        return 'ojee, geen laatste waarde in de json';
    }

    $ethPrice = $response->data->last;

    // Return the formatted price
    return 'Ethereum prijs: € '.number_format($ethPrice, 2, ',', '.');
}

function getDagVanDe()
{
    $dagVanDeLocatie = 'https://raw.githubusercontent.com/geensnor/DeDigitaleTuin/refs/heads/main/src/content/data/dagvande.json';

    $dagVanDeArray = json_decode(file_get_contents($dagVanDeLocatie));

    foreach ($dagVanDeArray as $key => $value) {
        if ($dagVanDeArray[$key]->dag == date('d-m')) {
            $dagText = 'Het is vandaag '.$dagVanDeArray[$key]->onderwerp;
        }
    }

    if (isset($dagText)) {
        return $dagText;
    } else {
        return false;
    }
}

function getNews(): string
{
    $nuxml = simplexml_load_file('https://feeds.nos.nl/nosnieuwsalgemeen');

    return "Laatste nieuws van nos.nl: \n[".$nuxml->channel->item[0]->title.']('.$nuxml->channel->item[0]->link.')';
}

function getHackerNews(): string
{
    $hackernewsxml = simplexml_load_file('https://hnrss.org/newest');

    return "Laatste bericht op hackernews: \n[".$hackernewsxml->channel->item[0]->title.']('.$hackernewsxml->channel->item[0]->link.')';
}

function getWeather(): string
{
    $weerObject = json_decode(file_get_contents('https://data.meteoserver.nl/api/liveweer.php?locatie=Utrecht&key='.getenv('meteoserverKey')));

    return "Het weer:\n[".$weerObject->liveweer[0]->verw.'](https://www.knmi.nl/nederland-nu/weer/verwachtingen)';
}

function getWaarschuwing(): string
{
    $weerObject = json_decode(file_get_contents('https://data.meteoserver.nl/api/liveweer.php?locatie=Utrecht&key='.getenv('meteoserverKey')));

    return "Waarschuwing!\n[".$weerObject->liveweer[0]->lkop.'](https://www.knmi.nl/nederland-nu/weer/waarschuwingen/utrecht)';
}

function getDaysSince($date): float
{
    return floor((time() - strtotime((string) $date)) / (60 * 60 * 24));
}

/**
 * Haalt een historisch event op van vandaag
 *
 * @todo events.historylabs.io is doet het op dit moment niet meer zo goed.
 */
function getVandaag(): object
{
    $todayResult = json_decode(file_get_contents('https://events.historylabs.io/date?day='.date('j').'&month='.date('n')));

    return $todayResult->events[array_rand($todayResult->events)];
}

function getWeekNumberToday(): string
{
    $currentDate = date('Y-m-d');
    $timestamp = strtotime($currentDate);
    $weekNumber = date('W', $timestamp);

    return 'week '.$weekNumber;
}

function getThuisarts(): string
{
    $thuisartsrss = simplexml_load_file('https://www.thuisarts.nl/rss.xml');

    return "Laatste bericht op thuisarts.nl: \n[".$thuisartsrss->channel->item[0]->title.']('.$thuisartsrss->channel->item[0]->link.')';
}

/**
 * Geef een item uit een van de simpele lijsten
 *
 * Simpele lijsten zijn JSON bestanden die alleen een array van strings bevatten.
 *
 * @param string $tekst String waarop gezocht moet worden
 * @param string $lijstenLijstURL String van de url van de lijsten lijst. De lijsten lijst is een lijst met lijsten.
 * @return string|bool Geeft het gevonden item terug als string, of false als er geen item gevonden is.
 */
function getItemSimpeleLijst(string $tekst, string $lijstenLijstURL): string|bool
{
    $simpeleLijsten = json_decode(file_get_contents($lijstenLijstURL));

    if ($simpeleLijsten === null) {
        return 'Kan geen lijsten lijst ophalen. Ongeldige JSON: '.$lijstenLijstURL;
    } else {

        foreach ($simpeleLijsten as $lijst) {
            if ($lijst->willekeurig && in_array($tekst, $lijst->willekeurig, true)) {
                $gevondenLijst = json_decode(file_get_contents($lijst->lijstURL), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $randKey = array_rand($gevondenLijst, 1);
                    return $gevondenLijst[$randKey];
                } else {
                    return 'Kan geen '.$lijst->naam.' ophalen. De JSON is niet helemaal lekker.';
                }
            } elseif ($lijst->laatste && in_array($tekst, $lijst->laatste, true)) {
                $gevondenLijst = json_decode(file_get_contents($lijst->lijstURL), true);
                if (json_last_error() === JSON_ERROR_NONE) {

                    return  end($gevondenLijst);
                } else {
                    return 'Kan geen '.$lijst->naam.' ophalen. De JSON is niet helemaal lekker.';
                }

            }
        }

        return false;
    }
}
