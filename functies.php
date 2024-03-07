<?php

function getBitcoinPrice()
{
    $url = 'https://api.coinbase.com/v2/exchange-rates?currency=BTC';
    $jsonData = file_get_contents($url);
    $response = json_decode($jsonData);

    return 'Bitcoin prijs: € '.number_format($response->data->rates->EUR, 2, ',', '.');
}

function getEthereumPrice()
{
    $url = 'https://api.coinbase.com/v2/exchange-rates?currency=ETH';
    $jsonData = file_get_contents($url);
    $response = json_decode($jsonData);

    if ($response && isset($response->data->rates->EUR)) {
        $price = $response->data->rates->EUR;
        // $percentage24Hour = round($response->data->rates->EUR_change_percentage * 100, 2);

        return 'Ethereum prijs: € '.number_format($price, 2, ',', '.');
    } else {
        return 'Error: Unable to retrieve the ETH price.';
    }
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

    if ($dagText) {
        return $dagText;
    } else {
        return false;
    }
}

function getNews()
{
    $nuxml = simplexml_load_file('https://feeds.nos.nl/nosnieuwsalgemeen');

    return "Laatste nieuws van nos.nl: \n[".$nuxml->channel->item[0]->title.']('.$nuxml->channel->item[0]->link.')';
}

function getHackerNews()
{
    $hackernewsxml = simplexml_load_file('https://hnrss.org/newest');

    return "Laatste bericht op hackernews: \n[".$hackernewsxml->channel->item[0]->title.']('.$hackernewsxml->channel->item[0]->link.')';
}

function getMop()
{
    $jsonMop = json_decode(file_get_contents('https://moppenbot.nl/api/random/'));

    return $jsonMop->joke->joke;
}

function getWeather()
{
    $weerObject = json_decode(file_get_contents('https://data.meteoserver.nl/api/liveweer.php?locatie=Utrecht&key='.getenv('meteoserverKey')));

    return "Het weer:\n[".$weerObject->liveweer[0]->verw.'](https://www.knmi.nl/nederland-nu/weer/verwachtingen)';
}

function getWaarschuwing()
{
    $weerObject = json_decode(file_get_contents('https://data.meteoserver.nl/api/liveweer.php?locatie=Utrecht&key='.getenv('meteoserverKey')));

    return "Waarschuwing!\n[".$weerObject->liveweer[0]->lkop.'](https://www.knmi.nl/nederland-nu/weer/waarschuwingen/utrecht)';
}

function getDaysSince($date)
{
    return floor((time() - strtotime($date)) / (60 * 60 * 24));
}

function getVandaag(): object
{
    $todayResult = json_decode(file_get_contents('https://events.historylabs.io/date?day='.date('j').'&month='.date('n')));

    return $todayResult->events[array_rand($todayResult->events)];
}

function getWeekNumberToday()
{
    $currentDate = date('Y-m-d');
    $timestamp = strtotime($currentDate);
    $weekNumber = date('W', $timestamp);

    return 'week '.$weekNumber;
}
