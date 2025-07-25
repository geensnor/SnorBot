<?php

date_default_timezone_set('Europe/Amsterdam');

include 'config.php';
include __DIR__.'/vendor/autoload.php';

include 'advies.php';
include 'prijzenparade.php';
include 'tourpoule.php';
include 'functies.php';
include 'wielrennen.php';
include 'energie.php';

$telegram = new Telegram(getenv('telegramId'));

$antwoordenArray = json_decode(file_get_contents('snorBotAntwoorden.json'));

$voornaamLocatie = 'https://raw.githubusercontent.com/reithose/voornamen/master/voornamen.json';
$wielrenKalender = 'https://www.wielerkrant.be/wielrennen/kalender.ics';

$text = strtolower(ltrim((string) $telegram->Text(), '/'));
$chat_id = $telegram->ChatID();

$losseWoorden = explode(' ', $text);
$antwoord = '';
$send = false;

$antwoordSimpeleLijst = getItemSimpeleLijst($text, 'lijstenLijst.json');

if ($antwoordSimpeleLijst) {
    $content = ['chat_id' => $chat_id, 'text' => $antwoordSimpeleLijst, 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);
    $send = true;
}

//Kabinet

if (strpos($text, 'kabinet') !== false) {
    $kabinetTekst = getKabinet();

    $content = ['chat_id' => $chat_id, 'text' => $kabinetTekst, 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);
    $send = true;
}

if (strpos($text, 'geschenk') !== false) {
    include 'cl_TweedeKamer.php';

    $tk = new TweedeKamer();

    $content = ['chat_id' => $chat_id, 'text' => $tk->getGeschenkTekst(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);
    $send = true;
}

if (strpos($text, 'activiteit') !== false) {
    include 'cl_TweedeKamer.php';

    $tk = new TweedeKamer();

    $content = ['chat_id' => $chat_id, 'text' => $tk->getActiviteitTekst(new DateTime()), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);
    $send = true;

}

//Wielrenkoersen
if (in_array($text, ['koers', 'koersen', 'wielrennen'], true)) {
    $parsedICS = getParsedCalendar($wielrenKalender);
    $koersTekst = getKoersenTekst($parsedICS, (int) date('Ymd'));

    $content = ['chat_id' => $chat_id, 'text' => htmlspecialchars_decode($koersTekst, ENT_QUOTES), 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);
    $send = true;
}

//Dag van de - Start
if ($text == 'dag van de' || $text == 'het is vandaag' || $text == 'dag' || $text == 'dag van' || $text == 'dagvan') {
    $dagVanDeText = getDagVanDe();
    if ($dagVanDeText) {
        $sendText = $dagVanDeText;
    } else {
        $sendText = "Ik heb geen idee waar het vandaag een dag van is. Maar op bijvoorbeeld https://www.beleven.org/feesten/ en https://www.fijnedagvan.nl/overzicht/kalender/ staan heel veel dagen.\n\nDe lijst van de bot staat op Github: https://github.com/geensnor/DeDigitaleTuin/blob/main/src/content/data/dagvande.json, dus ga je gang!";
    }

    $content = ['chat_id' => $chat_id, 'text' => $sendText, 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);
    $send = true;
}
//Dag van de - Einde

//Historische gebeurtenissen van wikipedia
if (in_array($text, ['vandaag', 'geschiedenis', 'deze dag'], true)) {
    $event = getVandaag();

    $sendText = '**Vandaag in '.$event->year."**:\n".$event->content;

    $content = ['chat_id' => $chat_id, 'text' => $sendText, 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);
    $send = true;
}

//Environment
if ($text == 'env') {
    $content = ['chat_id' => $chat_id, 'text' => getenv('environment')];
    $telegram->sendMessage($content);
    $send = true;
}

//BTC (bitcoin) koers
if ($text == 'bitcoin' || $text == 'btc') {
    $priceString = getBitcoinPrice();
    if (! $priceString) {
        $text = 'Kan bitcoinprijs niet ophalen';
    } else {
        $text = $priceString;
    }
    $content = ['chat_id' => $chat_id, 'text' => $text];
    $telegram->sendMessage($content);

    $send = true;
}
// end of bitcoin

//ETH koers
if ($text == 'eth') {
    $content = ['chat_id' => $chat_id, 'text' => getEthereumPrice()];
    $telegram->sendMessage($content);

    $send = true;
}
// end of ETH koers

//Random snack
if ($text == 'random snack' || $text == 'snack') {
    $snackResponse = json_decode(file_get_contents('https://europe-west1-speedy-realm-379713.cloudfunctions.net/generate-snack-v1'));
    $content = ['chat_id' => $chat_id, 'text' => $snackResponse->snack];
    $telegram->sendMessage($content);
    $send = true;
}

// Goedemorgen! Een dag overzicht!
if ($text == 'goedemorgen' || $text == 'goede morgen') {
    $dagVanDeText = getDagVanDe();
    $goedeMorgenText = "Goedemorgen! \nHier volgt het dagoverzicht van ".date('d-m-Y').' ('.getWeekNumberToday().')';
    if ($dagVanDeText) {
        $goedeMorgenText .= "\n\n".$dagVanDeText;
    }

    //Goede morgen tweede kamer!

    include 'cl_TweedeKamer.php';
    $tk = new TweedeKamer();

    $goedeMorgenText .= "\n\nDe koersen:\n".getBitcoinPrice()."\n".getEthereumPrice()."\n\n".getWeather()."\n\n".getWaarschuwing()."\n\n".getNews()."\n\n".$tk->getActiviteitTekst(new DateTime());

    $content = ['chat_id' => $chat_id, 'text' => $goedeMorgenText, 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);
    $send = true;
}
// Einde goedemorgen

// Crypto overzicht
if ($text == 'crypto') {
    $content = ['chat_id' => $chat_id, 'text' => getBitcoinPrice()." \n".getEthereumPrice()];
    $telegram->sendMessage($content);

    $send = true;
}
// Einde crypto overzicht

//Hieronder staan weerdingen
if ($text == 'weer' || $text == 'weerbericht' || $text == 'weersvoorspelling' || $text == 'lekker weertje') {
    $content = ['chat_id' => $chat_id, 'text' => getWeather(), 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);

    $send = true;
}

if ($text == 'waarschuwing' || $text == 'waarschuwingen' || $text == 'code rood' || $text == 'code geel') {
    $content = ['chat_id' => $chat_id, 'text' => getWaarschuwing(), 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);

    $send = true;
}

if (in_array($text, ['energie', 'energiemix', 'electriciteit'], true)) {
    $energieData = getEnergie();
    $content = ['chat_id' => $chat_id, 'text' => 'Op dit moment wordt in Nederland '.$energieData->renewablePercentage.'% van de electriciteit opgewekt uit hernieuwbare bronnen. '.$energieData->powerProductionBreakdown->wind.' MW komt uit windenergie, '.$energieData->powerProductionBreakdown->solar.' MW uit zonne-energie.', 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);
    $send = true;

}

if (in_array($text, ['stroom', 'stroomprijs'], true)) {
    $stroomObject = json_decode(file_get_contents('https://www.geensnor.nl/api/stroom/index.php?key='.getenv('stroomKey')));
    $highestPriceTimeFormatted = (new DateTime($stroomObject->rangePriceToday->highest->time))->format('H:i');
    $lowestPriceTimeFormatted = (new DateTime($stroomObject->rangePriceToday->lowest->time))->format('H:i');
    if ($stroomObject->negativePriceToday) {
        $negativePriceText = "Tussen ".(new DateTime($stroomObject->negativePriceToday->start))->format('H:i')." en ".(new DateTime($stroomObject->negativePriceToday->end))->format('H:i')." is de stroomprijs negatief";
    } else {
        $negativePriceText = '';
    }

    $content = ['chat_id' => $chat_id, 'text' => 'De stroomprijs van Tibber is op dit moment '.str_replace(".", ",", $stroomObject->currentPrice).' euro per kWh. De hoogste prijs van vandaag is '.str_replace(".", ",", $stroomObject->rangePriceToday->highest->price).' om '.$highestPriceTimeFormatted.'. De laagste is '.str_replace(".", ",", $stroomObject->rangePriceToday->lowest->price).' om '.$lowestPriceTimeFormatted.'. '.$negativePriceText, 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);
    $send = true;
}

if (in_array($text, ['temperatuur', 'koud', 'warm', 'brr'], true)) {
    $weerObject = json_decode(file_get_contents('https://data.meteoserver.nl/api/liveweer.php?locatie=Utrecht&key='.getenv('meteoserverKey')));
    $content = ['chat_id' => $chat_id, 'text' => 'Het is '.$weerObject->liveweer[0]->temp.' graden, maar het voelt als '.$weerObject->liveweer[0]->gtemp, 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);
    $send = true;
}

//Hieronder het aantal dagen dat Sywert ons geld nog niet heeft terug betaald.
if ($text == 'sywert' || $text == 'sywert van lienden') {
    $antwoord = 'Het is '.getDaysSince('06-06-2021').' dagen geleden dat Sywert van Lienden beloofde om het rendement van de 9 miljoen euro die hij onterecht verdiende, aan een goed doel te schenken.';

    $send = true;
}

//Hieronder staat 'getal onder de'. Werkt niet in een groep

if (str_starts_with($text, 'getal onder de')) {
    $content = ['chat_id' => $chat_id, 'text' => random_int(1, ((int) substr($text, 15) - 1))];
    $telegram->sendMessage($content);
    $send = true;
}

if ($text == 'nieuwste post' || $text == 'nieuwste bericht') {
    $geensnorFeed = new SimpleXMLElement(file_get_contents('https://geensnor.netlify.app/feed.xml'));
    $content = ['chat_id' => $chat_id, 'text' => 'Nieuwste bericht op geensnor.nl: ['.$geensnorFeed->entry[0]->title.']('.$geensnorFeed->entry[0]->link->attributes()->href.')', 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);
    $send = true;
}

if ($text == 'random post' || $text == 'random bericht') {
    $geensnorFeed = new SimpleXMLElement(file_get_contents('https://geensnor.netlify.app/feed.xml'));
    $randomPostNummer = random_int(0, count($geensnorFeed->entry));
    $content = ['chat_id' => $chat_id, 'text' => 'Een van de laatste 10 berichten op geensnor.nl: ['.$geensnorFeed->entry[$randomPostNummer]->title.']('.$geensnorFeed->entry[$randomPostNummer]->link->attributes()->href.')', 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);
    $send = true;
}

// Hieronder de wiki dingen

if (str_starts_with($text, 'wiki')) {
    $wikiResult = json_decode(file_get_contents('https://nl.wikipedia.org/w/api.php?action=opensearch&search='.substr($text, 5).'&limit=10&namespace=0&format=json'));
    if ($wikiResult[1]) {
        $htmlList = '';
        foreach ($wikiResult[1] as $key => $value) {
            $htmlList .= '<a href="'.$wikiResult[3][$key].'">'.$wikiResult[1][$key]."</a>\n";
        }
        $content = ['chat_id' => $chat_id, 'text' => 'Ah, je wil iets van <strong>'.substr($text, 5)."</strong> weten. Dit vond ik op Wikipedia:\n\n".$htmlList, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true];
    } else {
        $content = ['chat_id' => $chat_id, 'text' => 'Ah, je wil iets van *'.substr($text, 5).'* weten. Daar heb ik helaas niets van kunnen vinden op Wikipedia', 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    }

    $telegram->sendMessage($content);
    $send = true;
}

//Beetje nieuws.....

if ($text == 'nieuws') {
    $content = ['chat_id' => $chat_id, 'text' => getNews(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);

    $send = true;
}

// thuisarts nieuws
if ($text == 'thuisarts') {
    $content = ['chat_id' => $chat_id, 'text' => getThuisarts(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);

    $send = true;
}

//Wielrenieuws

if ($text == 'wielrennieuws') {
    $content = ['chat_id' => $chat_id, 'text' => getCyclingNews(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);

    $send = true;
}

//Beetje hacker nieuws.....
if ($text == 'hacker') {
    $content = ['chat_id' => $chat_id, 'text' => getHackerNews(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);

    $send = true;
}
//hackerNieuws hierboven

//Week number
if ($text == 'week') {
    $content = ['chat_id' => $chat_id, 'text' => getWeekNumberToday(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);

    $send = true;
}

//PI number
if ($text == 'pi' || $text == 'π') {
    $content = ['chat_id' => $chat_id, 'text' => pi(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);

    $send = true;
}

//Is het al 5 uur?
if ($text == 'is het al vijf uur' || $text == 'is het al 5 uur') {
    $content = ['chat_id' => $chat_id, 'text' => 'Nee, het is '.date('H:i:s')];
    $telegram->sendMessage($content);
    $send = true;
}
////////////////

if ($text == 'xkcd') {
    $xkcdData = json_decode(file_get_contents('https://xkcd.com/info.0.json'));
    $randomComicNumber = random_int(0, $xkcdData->num);
    $randomComicObject = json_decode(file_get_contents('http://xkcd.com/'.$randomComicNumber.'/info.0.json'));
    $content = ['chat_id' => $chat_id, 'photo' => $randomComicObject->img];
    $telegram->sendPhoto($content);
    $antwoord = "Random XKCD comic. Typ 'xkcd nieuwste' voor de nieuwste";
    $send = true;
}

if ($text == 'xkcd nieuwste') {
    $xkcdData = json_decode(file_get_contents('https://xkcd.com/info.0.json'));
    $content = ['chat_id' => $chat_id, 'photo' => $xkcdData->img];
    $telegram->sendPhoto($content);
    $send = true;
}

if (in_array($text, ['plaatje', 'random plaatje', 'vet plaatje', 'kunst', 'archillect'], true)) {
    $randomId = random_int(1, 408749);
    $randomPageURL = 'https://archillect.com/'.$randomId;
    $randomPageSource = file_get_contents($randomPageURL);

    $start = stripos($randomPageSource, 'ii') + 9;
    $end = stripos($randomPageSource, '">', $start);
    $length = $end - $start;

    $content = ['chat_id' => $chat_id, 'photo' => substr($randomPageSource, $start, $length)];
    $telegram->sendPhoto($content);

    $content = ['chat_id' => $chat_id, 'text' => '[bron]('.$randomPageURL.')', 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];

    $telegram->sendMessage($content);

    $send = true;
}

if ($text == 'genereer wachtwoord') {
    $wachtwoord = json_decode(file_get_contents('https://www.passwordrandom.com/query?command=password&format=json&count=10'));
    $content = ['chat_id' => $chat_id, 'text' => 'Random wachtwoord: '.$wachtwoord->char[1]];
    $telegram->sendMessage($content);
    $send = true;
}

if ($text == 'guid') {
    $guid = json_decode(file_get_contents('https://www.passwordrandom.com/query?command=guid&format=json&count=10'));
    $content = ['chat_id' => $chat_id, 'text' => 'Random guid: '.$guid->char[1]];
    $telegram->sendMessage($content);
    $send = true;
}

//Geeft het chat id van de huidige groep weer
if ($text == 'chatid') {
    $content = ['chat_id' => $chat_id, 'text' => 'Chat id van deze groep: '.$chat_id];
    $telegram->sendMessage($content);
    $send = true;
}

// Wie is er jarig?

if ($text == 'verjaardag' || $text == 'jarig' || $text == 'verjaardagen') {
    if ($chat_id == getenv('verjaardagenGroupId')) {
        include 'cl_verjaardagen.php';

        $nu = new DateTime();
        $vandaag = new DateTime($nu->format('Y-m-d')); //Dit is een beetje funky. Maar anders sprint hij van dag op en neer.

        $v = new verjaardag();
        $v->getVerjaardagen();
        $content = ['chat_id' => $chat_id, 'text' => $v->getVerjaardagTekst($vandaag)];
        $telegram->sendMessage($content);
        $send = true;
    } else {
        $content = ['chat_id' => $chat_id, 'text' => 'Geen verjaardagsinformatie in deze groep'];
        $telegram->sendMessage($content);
        $send = true;
    }
}

if (preg_match('/.*\d{4}.*/', $text) && $text != '1337') {//Controleren of er in de vraag vier cijfers (jaartal...) in een string voorkomt. Dan beschouwen we het maar als een jaartal. Behalve als het natuurlijk 1337 is....
    if ($chat_id == getenv('verjaardagenGroupId')) {
        include 'cl_weekenden.php';
        $v = new weekend();
        $content = ['chat_id' => $chat_id, 'text' => $v->getWeekendText($text)];
        $telegram->sendMessage($content);
        $send = true;
    } else {
        $content = ['chat_id' => $chat_id, 'text' => 'Hier kan ik niets over zeggen.'];
        $telegram->sendMessage($content);
        $send = true;
    }
}

if ($telegram->Location()) {
    $locatieGebruiker = $telegram->Location();
    $adviesJson = getAdviesArray($locatieGebruiker['latitude'], $locatieGebruiker['longitude']);

    $contentAdviesTitel = ['chat_id' => $chat_id, 'text' => $adviesJson[0]->name.' zit in de buurt:'];
    $contentAdviesToelichting = ['chat_id' => $chat_id, 'text' => "Geensnor zegt: '".$adviesJson[0]->description."'. Kijk op http://advies.geensnor.nl voor meer adviezen"];
    $contentLocation = ['chat_id' => $chat_id, 'latitude' => $adviesJson[0]->lat, 'longitude' => $adviesJson[0]->lon];
    $telegram->sendMessage($contentAdviesTitel);
    $telegram->sendLocation($contentLocation);
    $telegram->sendMessage($contentAdviesToelichting);
    $send = true;
}

if ($text == 'advies') {
    $option = [[$telegram->buildKeyBoardButton('Klik hier om je locatie te delen', $request_contact = false, $request_location = true)]];
    $keyb = $telegram->buildKeyBoard($option, $onetime = false);
    $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'Aaaah, je wilt een advies van Geensnor. Goed idee! Druk op de knop hieronder aan te geven waar je bent.'];
    $telegram->sendMessage($content);
    $send = true;
}

if ($text == 'voornaam' || $text == 'naam' || $text == 'babynaam') {
    $voornaamObject = json_decode(file_get_contents($voornaamLocatie));
    if (json_last_error() === JSON_ERROR_NONE) {
        $randKey = array_rand($voornaamObject, 1);
        $antwoord = $voornaamObject[$randKey]->naam;
    } else {
        $antwoord = 'De namen zijn foetsie';
    }
    $send = true;
}

if ($text == '1337') {
    $dateString = date('y-m-d H:i:s');

    //Tijdzonde conversie, voor het geval de server niet op onze tijdzone zit
    $timeZone = 'Europe/Amsterdam';
    $timeZoneSource = date_default_timezone_get();
    $currentTime = new DateTime($dateString, new DateTimeZone($timeZoneSource));
    $currentTime->setTimezone(new DateTimeZone($timeZone));

    $currentHour = $currentTime->format('H');
    $currentMinute = $currentTime->format('i');

    //Truukje te zorgen dat hij altijd het verschil met een toekomstige tijd berekent (anders neemt ie vandaag)
    $dayCompensator = 0;
    if ($currentHour >= 14 or ($currentHour == 13 and $currentMinute > 36)) {
        $dayCompensator = 1;
    }

    $dayDate = date('y-m-d', strtotime("+ $dayCompensator day"));

    //Verschil berekenen en in tekst zetten
    $completedTime = new DateTime("$dayDate 13:37:00", new DateTimeZone($timeZone));
    $interval = $completedTime->diff($currentTime);
    $leetTime = $interval->format('%H uur, %I minuten, %S seconden');
    $leetText = "Tijd tot volgende 1337: $leetTime.";

    $antwoord = $leetText;
    $send = true;
}

if (in_array($text, ['winnen', 'prijzenparade'], true)) {
    $prijzenparade_url = get_prijzen_parade_url();
    if ($prijzenparade_url) {
        $antwoord = 'De link van de Tweakers December Prijzen Parade van vandaag is: '.$prijzenparade_url;
    } else {
        $antwoord = 'Helaas, voor vandaag is er geen prijzenlink beschikbaar. Probeer het morgen nog eens!';
    }
    $send = true;
}

//Tourpoule. Functie staat in apart bestand.
if (in_array($text, ['tourpoule', 'tour', 'poule', 'stand poule', 'stand tourpoule', 'tussenstand'], true)) {
    $content = ['chat_id' => $chat_id, 'text' => getTourRanking(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);
    $send = true;
}

if (in_array($text, ['uitslag vandaag', 'uitslag', 'tourpoule vandaag', 'etappe', 'vandaag', 'uitslag etappe'], true)) {
    $content = ['chat_id' => $chat_id, 'text' => getTodayRanking(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true];
    $telegram->sendMessage($content);
    $send = true;
}

if (! $send) {
    //Eerst op de hele zin/alle woorden zoeken ($text). Dit werkt voor geen meter....
    foreach ($antwoordenArray as $key => $value) {
        if (strstr($text, strtolower((string) $antwoordenArray[$key]->trigger)) || strstr($text, ucfirst((string) $antwoordenArray[$key]->trigger))) {
            $antwoord = $antwoordenArray[$key]->antwoord;
            $send = true;
        }
    }

    //Daarna kijken naar de losse woorden
    if (! $send) {
        foreach ($losseWoorden as $wKey => $wValue) {
            foreach ($antwoordenArray as $key => $value) {
                if (strstr($losseWoorden[$wKey], strtolower((string) $antwoordenArray[$key]->trigger)) || strstr($losseWoorden[$wKey], ucfirst((string) $antwoordenArray[$key]->trigger))) {
                    $antwoord = $antwoordenArray[$key]->antwoord;
                    $send = true;
                }
            }
        }
    }
}

//Random antwoord geven als hij niets weet...
if (! $send && $text) {
    $randKey = array_rand($antwoordenArray, 1);
    $antwoord = $antwoordenArray[$randKey]->antwoord;

    //Vroeger deed de Snorbot dit
    //$antwoord = "Ik kan niets met: '".$text."'. Probeer eens een leuk weetje ofzo";
}
if ($antwoord) {
    $content = ['chat_id' => $chat_id, 'text' => $antwoord, 'parse_mode' => 'Markdown'];
    $telegram->sendMessage($content);
}
