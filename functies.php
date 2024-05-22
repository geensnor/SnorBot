<?php

function getKabinet(): string
{

    $nosFeed = simplexml_load_file('https://feeds.nos.nl/nosnieuwspolitiek');
    foreach ($nosFeed->channel->item as $nosItem) {
        if (strpos($nosItem->description, 'kabinet') || strpos($nosItem->description, 'asiel') || strpos($nosItem->description, 'stikstof')) {
            return '['.$nosItem->title.']('.$nosItem->link.')';

        }
    }

}

function getBitcoinPrice(): string
{
    $url = 'https://api.kucoin.com/api/v1/market/stats?symbol=BTC-EUR';
    $jsonData = file_get_contents($url);

    // Check if the data was fetched correctly
    if ($jsonData === false) {
        return 'Error fetching data from kucoin';
    }

    $response = json_decode($jsonData);

    // Check if the JSON was decoded correctly
    if ($response === null) {
        return 'Error decoding JSON data';
    }

    // Ensure we are accessing the correct properties
    if (! isset($response->data->last)) {
        return 'Error: Last rate not found in response';
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
        return 'Error fetching data from kucoin';
    }

    $response = json_decode($jsonData);

    // Check if the JSON was decoded correctly
    if ($response === null) {
        return 'Error decoding JSON data';
    }

    // Ensure we are accessing the correct properties
    if (! isset($response->data->last)) {
        return 'Error: last rate not found in response';
    }

    $ethPrice = $response->data->last;

    // Return the formatted price
    return 'Ethereum prijs: € '.number_format($ethPrice, 2, ',', '.');
}

function getDagVanDe()
{
    $dagVanDeLocatie = 'https://raw.githubusercontent.com/geensnor/DigitaleTuin/master/_data/dagvande.json';
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

function getMop()
{
    $jsonMop = json_decode(file_get_contents('https://moppenbot.nl/api/random/'));

    return $jsonMop->joke->joke;
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
