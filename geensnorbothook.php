<?php
date_default_timezone_set('Europe/Amsterdam');
include(__DIR__ . '/vendor/autoload.php');
include("advies.php");
include("prijzenparade.php");
include("tourpoule.php");

$telegram = new Telegram(getenv('telegramId'));

$antwoordenArray = json_decode(file_get_contents("snorBotAntwoorden.json"));

$weetjesLocatie = "https://raw.githubusercontent.com/geensnor/DigitaleTuin/master/_data/weetjes.json";
$dooddoenerLocatie = "https://raw.githubusercontent.com/geensnor/DigitaleTuin/master/_data/dooddoeners.json";
$verveelLocatie = "https://raw.githubusercontent.com/geensnor/DigitaleTuin/master/_data/verveellijst.json";
$haikuLocatie = "https://raw.githubusercontent.com/geensnor/DigitaleTuin/master/_data/haiku.json";
$brabantsLocatie = "https://raw.githubusercontent.com/geensnor/DigitaleTuin/master/_data/brabants.json";
$covidLocatie = "https://raw.githubusercontent.com/hungrxyz/infected-data/main/data/latest/national.json";
$voornaamLocatie = "https://raw.githubusercontent.com/reithose/voornamen/master/voornamen.json";

$text = strtolower(ltrim($telegram->Text(), '/'));
$chat_id = $telegram->ChatID();

$losseWoorden = explode(" ", $text);
$antwoord = "";
$send = false;

// Functies
function getBitcoinPrice()
{
    $bitcoinPriceObject = json_decode(file_get_contents("https://api.cryptowat.ch/markets/kraken/btceur/summary"));
    $price  = $bitcoinPriceObject->result->price->last;
    $percentage24Hour  = round($bitcoinPriceObject->result->price->change->percentage *100, 2);
    
    return "Bitcoin koers: € ".$price." (".$percentage24Hour."% in laatste 24 uur)";
}

function getEthereumPrice()
{
    $ethPriceObject = json_decode(file_get_contents("https://api.cryptowat.ch/markets/kraken/etheur/summary"));
    $price  = $ethPriceObject->result->price->last;
    $percentage24Hour  = round($ethPriceObject->result->price->change->percentage *100, 2);
    
    return "Ethereum koers: € ".$price." (".$percentage24Hour."% in laatste 24 uur)";
}

function getDagVanDe()
{
    $dagVanDeLocatie = "https://raw.githubusercontent.com/geensnor/DigitaleTuin/master/_data/dagvande.json";
    $dagVanDeArray = json_decode(file_get_contents($dagVanDeLocatie));
    foreach ($dagVanDeArray as $key => $value) {
        if ($dagVanDeArray[$key]->dag == date('d-m')) {
            $dagText =  "Het is vandaag ".$dagVanDeArray[$key]->onderwerp;
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
    $nuxml = simplexml_load_file("http://feeds.nos.nl/nosjournaal");
    
    return "Laatste nieuws van nos.nl: \n[".$nuxml->channel->item[0]->title."](".$nuxml->channel->item[0]->link.")";
}

function getCyclingNews()
{
    $nuxml = simplexml_load_file("http://feeds.nos.nl/nossportwielrennen");
    
    return "Laatste wielrennieuws van nos.nl: \n[".$nuxml->channel->item[0]->title."](".$nuxml->channel->item[0]->link.")";
}

function getHackerNews()
{
    $hackernewsxml = simplexml_load_file("https://hnrss.org/newest");

    return "Laatste bericht op hackernews: \n[".$hackernewsxml->channel->item[0]->title."](".$hackernewsxml->channel->item[0]->link.")";
}

function getWeather()
{
    $weerObject = simplexml_load_string(file_get_contents("https://cdn.knmi.nl/knmi/xml/rss/rss_KNMIverwachtingen.xml"));
    return "Het weer:\n[".$weerObject->channel->item[0]->title."](https://www.knmi.nl/nederland-nu/weer/verwachtingen)";
}
// einde functies

//Begin van de commando's

//Dag van de - Start
    if ($text == 'dag van de' || $text == 'het is vandaag' || $text == 'dag' || $text == 'dag van' || $text == 'dagvan') {
        $dagVanDeText = getDagVanDe();
        if ($dagVanDeText) {
            $sendText = $dagVanDeText;
        } else {
            $sendText =  "Ik heb geen idee waar het vandaag een dag van is. Maar op bijvoorbeeld https://www.beleven.org/feesten/ en https://www.fijnedagvan.nl/overzicht/kalender/ staan heel veel dagen.\n\nDe lijst van de bot staat op Github: https://github.com/geensnor/DigitaleTuin/blob/master/_data/dagvande.json, dus ga je gang!";
        }

        $content = array('chat_id' => $chat_id, 'text' => $sendText, 'disable_web_page_preview' => true);
        $telegram->sendMessage($content);
        $send = true;
    }
//Dag van de - Einde


//BTC (bitcoin) koers
    if ($text == 'bitcoin' || $text == 'btc') {
        $content = array('chat_id' => $chat_id, 'text' => getBitcoinPrice());
        $telegram->sendMessage($content);

        $send = true;
    }
// end of bitcoin

//ETH koers
    if ($text == 'eth') {
        $content = array('chat_id' => $chat_id, 'text' => getEthereumPrice());
        $telegram->sendMessage($content);
        
        $send = true;
    }
// end of ETH koers

// Goedemorgen! Een dag overzicht!
    if ($text == 'goedemorgen') {
        $dagVanDeText = getDagVanDe();

        $goedeMorgenText = "Goedemorgen, hier volgt het dagoverzicht ...";
        if ($dagVanDeText) {
            $goedeMorgenText.= "\n\n".$dagVanDeText;
        }

        $goedeMorgenText.="\n\nDe koersen:\n" .getBitcoinPrice(). "\n" .getEthereumPrice(). "\n\n" .getWeather(). "\n\n" .getNews();

        $content = array('chat_id' => $chat_id, 'text' => $goedeMorgenText, 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true);
        $telegram->sendMessage($content);
        $send = true;
    }
// Einde goedemorgen

// Crypto overzicht
if ($text == 'crypto') {
    $content = array('chat_id' => $chat_id, 'text' => getBitcoinPrice(). " \n" .getEthereumPrice());
    $telegram->sendMessage($content);
    
    $send = true;
}
// Einde crypto overzicht

//Hieronder staan weerdingen
    if ($text == 'weer' || $text == 'weerbericht' || $text == 'weersvoorspelling' || $text == 'lekker weertje') {
        $content = array('chat_id' => $chat_id, 'text' => getWeather(), 'parse_mode' => 'Markdown');
        $telegram->sendMessage($content);

        $send = true;
    }


//Hieronder staat 'getal onder de'. Werkt niet in een groep

    if (substr($text, 0, 14) == 'getal onder de') {
        $content = array('chat_id' => $chat_id, 'text' => rand(1, (substr($text, 15)-1)));
        $telegram->sendMessage($content);
        $send = true;
    }


    if ($text == 'nieuwste post' || $text == 'nieuwste bericht') {
        $geensnorFeed = new SimpleXMLElement(file_get_contents("https://geensnor.netlify.app/feed.xml"));
        $content = array('chat_id' => $chat_id, 'text' => "Nieuwste bericht op geensnor.nl: [".$geensnorFeed->entry[0]->title."](".$geensnorFeed->entry[0]->link->attributes()->href.")", 'parse_mode' => 'Markdown');
        $telegram->sendMessage($content);
        $send = true;
    }

    if ($text == 'random post' || $text == 'random bericht') {
        $geensnorFeed = new SimpleXMLElement(file_get_contents("https://geensnor.netlify.app/feed.xml"));
        $randomPostNummer = rand(0, count($geensnorFeed->entry));
        $content = array('chat_id' => $chat_id, 'text' => "Een van de laatste 10 berichten op geensnor.nl: [".$geensnorFeed->entry[$randomPostNummer]->title."](".$geensnorFeed->entry[$randomPostNummer]->link->attributes()->href.")", 'parse_mode' => 'Markdown');
        $telegram->sendMessage($content);
        $send = true;
    }

/* Hieronder de wiki dingen

  if(substr($text, 0, 4) == 'wiki') {
    $wikiResult = json_decode(file_get_contents("https://nl.wikipedia.org/w/api.php?action=opensearch&search=".substr($text, 5)."&limit=10&namespace=0&format=json"));
    if($wikiResult[1]){
          foreach ($wikiResult[1] as $key => $value) {
              $htmlList .= "<a href=\"".$wikiResult[3][$key]."\">". $wikiResult[1][$key]."</a>\n";
          }
      $content = array('chat_id' => $chat_id, 'text' => "Ah, je wil iets van <strong>".substr($text, 5)."</strong> weten. Dit vond ik op Wikipedia:\n\n".$htmlList, 'parse_mode' => 'HTML', 'disable_web_page_preview' => TRUE);
        }
      else{
      $content = array('chat_id' => $chat_id, 'text' => "Ah, je wil iets van *".substr($text, 5)."* weten. Daar heb ik helaas niets van kunnen vinden op Wikipedia", 'parse_mode' => 'Markdown', 'disable_web_page_preview' => TRUE);
    }

    $telegram->sendMessage($content);
    $send = TRUE;
  } */

//Beetje nieuws.....

    if ($text == 'nieuws') {
        $content = array('chat_id' => $chat_id, 'text' => getNews(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true);
        $telegram->sendMessage($content);

        $send = true;
    }
//Nieuws hierboven

if ($text == 'wielrennieuws') {
    $content = array('chat_id' => $chat_id, 'text' => getCyclingNews(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true);
    $telegram->sendMessage($content);

    $send = true;
}


//Beetje hacker nieuws.....
if ($text == 'hacker') {
    $content = array('chat_id' => $chat_id, 'text' => getHackerNews(), 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true);
    $telegram->sendMessage($content);

    $send = true;
}
//hackerNieuws hierboven

//Is het al 5 uur?
    if ($text == 'is het al vijf uur' || $text == 'is het al 5 uur') {
        $content = array('chat_id' => $chat_id, 'text' => "Nee, het is ".date("H:i:s"));
        $telegram->sendMessage($content);
        $send = true;
    }
////////////////

    if ($text == 'xkcd') {
        $xkcdData = json_decode(file_get_contents("https://xkcd.com/info.0.json"));
        $randomComicNumber = rand(0, $xkcdData->num);
        $randomComicObject = json_decode(file_get_contents("http://xkcd.com/".$randomComicNumber."/info.0.json"));
        $content = array('chat_id' => $chat_id, 'photo' => $randomComicObject->img);
        $telegram->sendPhoto($content);
        $antwoord = "Random XKCD comic. Typ 'xkcd nieuwste' voor de nieuwste";
        $send = true;
    }

  if ($text == 'xkcd nieuwste') {
      $xkcdData = json_decode(file_get_contents("https://xkcd.com/info.0.json"));
      $content = array('chat_id' => $chat_id, 'photo' => $xkcdData->img);
      $telegram->sendPhoto($content);
      $send = true;
  }
     
    if ($text == 'genereer wachtwoord') {
        $wachtwoord = json_decode(file_get_contents("https://www.passwordrandom.com/query?command=password&format=json&count=10"));
        $content = array('chat_id' => $chat_id, 'text' => "Random wachtwoord: ".$wachtwoord->char[1]);
        $telegram->sendMessage($content);
        $send = true;
    }
    
    if ($text == 'guid') {
        $guid = json_decode(file_get_contents("https://www.passwordrandom.com/query?command=guid&format=json&count=10"));
        $content = array('chat_id' => $chat_id, 'text' => "Random guid: ".$guid->char[1]);
        $telegram->sendMessage($content);
        $send = true;
    }
    

//Geeft het chat id van de huidige groep weer
    if ($text == 'chatid') {
        $content = array('chat_id' => $chat_id, 'text' => "Chat id van deze groep: ".$chat_id);
        $telegram->sendMessage($content);
        $send = true;
    }

// Wie is er jarig?

    if ($text == 'verjaardag' || $text == 'jarig' || $text == 'verjaardagen') {
        if ($chat_id == getenv('verjaardagenGroupId')) {
            include("cl_verjaardagen.php");
            $v = new verjaardag;
            $content = array('chat_id' => $chat_id, 'text' => $v->getVerjaardagTekst());
            $telegram->sendMessage($content);
            $send = true;
        } else {
            $content = array('chat_id' => $chat_id, 'text' => "Geen verjaardagsinformatie in deze groep");
            $telegram->sendMessage($content);
            $send = true;
        }
    }

    if ($telegram->Location()) {
        $locatieGebruiker = $telegram->Location();
        $adviesJson = getAdviesArray($locatieGebruiker["latitude"], $locatieGebruiker["longitude"]);

        $contentAdviesTitel = ['chat_id' => $chat_id, 'text' => $adviesJson[0]->name." zit in de buurt:"];
        $contentAdviesToelichting = ['chat_id' => $chat_id, 'text' => "Geensnor zegt: '".$adviesJson[0]->description."'. Kijk op http://advies.geensnor.nl voor meer adviezen"];
        $contentLocation = ['chat_id' => $chat_id, 'latitude' => $adviesJson[0]->lat, 'longitude' => $adviesJson[0]->lon];
        $telegram->sendMessage($contentAdviesTitel);
        $telegram->sendLocation($contentLocation);
        $telegram->sendMessage($contentAdviesToelichting);
        $send = true;
    }

    if ($text == "advies") {
        $option = array(array($telegram->buildKeyBoardButton("Klik hier om je locatie te delen", $request_contact=false, $request_location=true)));
        $keyb = $telegram->buildKeyBoard($option, $onetime=false);
        $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Aaaah, je wilt een advies van Geensnor. Goed idee! Druk op de knop hieronder aan te geven waar je bent.");
        $telegram->sendMessage($content);
        $send = true;
    }


    
    if ($text == "nieuwste weetje") {
        $weetjesArray = json_decode(file_get_contents($weetjesLocatie));
        if (json_last_error() === JSON_ERROR_NONE) {
            $antwoord = end($weetjesArray);
        } else {
            $antwoord = "Kan geen nieuw weetje ophalen. De weetjes json is niet helemaal lekker.";
        }
        $send = true;
    }

    if ($text  == "weetje") {
        $weetjesArray = json_decode(file_get_contents($weetjesLocatie));
        if (json_last_error() === JSON_ERROR_NONE) {
            $randKey = array_rand($weetjesArray, 1);
            $antwoord = $weetjesArray[$randKey];
        } else {
            $antwoord = "Kan geen weetje ophalen. De weetjes json is niet helemaal lekker.";
        }
        $send = true;
    }

    if ($text  == "brabants" || $text  == "alaaf" || $text  == "brabant" || $text  == "wa zedde gij") {
        $brabantsArray = json_decode(file_get_contents($brabantsLocatie));
        if (json_last_error() === JSON_ERROR_NONE) {
            $randKey = array_rand($brabantsArray, 1);
            $antwoord = $brabantsArray[$randKey];
        } else {
            $antwoord = "Kan geen brabants ophalen. De brabant json is niet helemaal lekker.";
        }
        $send = true;
    }

    if ($text  == "dooddoener") {
        $dooddoenerArray = json_decode(file_get_contents($dooddoenerLocatie));
        if (json_last_error() === JSON_ERROR_NONE) {
            $randKey = array_rand($dooddoenerArray, 1);
            $antwoord = $dooddoenerArray[$randKey];
        } else {
            $antwoord = "Kan geen dooddoener vinden. De json file is naar de vaantjes";
        }
        $send = true;
    }

    if ($text  == "verveel" || $text  == "wat zal ik doen") {
        $verveelArray = json_decode(file_get_contents($verveelLocatie));
        if (json_last_error() === JSON_ERROR_NONE) {
            $randKey = array_rand($verveelArray, 1);
            $antwoord = $verveelArray[$randKey];
        } else {
            $antwoord = "Verveel je je ja? Nou, ik kan je ook niet helpen want ik kan de json met leuke dingen om te doen niet vinden.";
        }
        $send = true;
    }

    if ($text  == "haiku") {
        $haikuArray = json_decode(file_get_contents($haikuLocatie));
        if (json_last_error() === JSON_ERROR_NONE) {
            $randKey = array_rand($haikuArray, 1);
            $antwoord = $haikuArray[$randKey];
        } else {
            $antwoord = "De JSON is stuk \nde haiku's zijn verdwenen \nwie kan mij helpen?";
        }
        $send = true;
    }

    if ($text == "nieuwste haiku") {
        $haikuArray = json_decode(file_get_contents($haikuLocatie));
        if (json_last_error() === JSON_ERROR_NONE) {
            $antwoord = end($haikuArray);
        } else {
            $antwoord = "De JSON is stuk \nde haiku's zijn verdwenen \nwie kan mij helpen?";
        }
        $send = true;
    }

    if ($text == "corona" || $text == "covid") {
        $covidObject = json_decode(file_get_contents($covidLocatie));

        if (substr($covidObject->positiveCases->trend, 0, 1) == "-") {
            $displayTrend = substr($covidObject->positiveCases->trend, 1);
            $trendText = $displayTrend." minder";
        } else {
            $trendText = $covidObject->positiveCases->trend." meer";
        }
        
        $antwoord = "Op ".date("d-m-Y", strtotime($covidObject->numbersDate))." zijn er ".$covidObject->positiveCases->new." besmettingen gemeld. Dat zijn er ".$trendText." dan de dag ervoor.";
        $send = true;
    }

    if ($text == "vaccinaties" || $text == "vaccin") {
        $covidObject = json_decode(file_get_contents($covidLocatie));
        $antwoord = "Tot ".date("d-m-Y", strtotime($covidObject->updatedAt))." hebben ".$covidObject->vaccinations->total." mensen een vaccin in hun arm gehad. Dat zijn er ".$covidObject->vaccinations->new." meer dan de dag ervoor. Ongeveer ".round($covidObject->vaccinations->percentageOfPopulation*100, 2)."% van Nederland is nu gevaccineerd.";
        $send = true;
    }

    if ($text == "voornaam" || $text == "naam" || $text == "babynaam") {
        $voornaamObject = json_decode(file_get_contents($voornaamLocatie));
        if (json_last_error() === JSON_ERROR_NONE) {
            $randKey = array_rand($voornaamObject, 1);
            $antwoord = $voornaamObject[$randKey]->naam;
        } else {
            $antwoord = "De namen zijn foetsie";
        }
        $send = true;
    }


    if ($text == "1337") {
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
        if (14 <= $currentHour or ($currentHour == 13 and $currentMinute > 36)) {
            $dayCompensator = 1;
        }
        
        $dayDate = date("y-m-d", strtotime("+ $dayCompensator day"));
        
        //Verschil berekenen en in tekst zetten
        $completedTime = new DateTime("$dayDate 13:37:00", new DateTimeZone($timeZone));
        $interval = $completedTime->diff($currentTime);
        $leetTime = $interval->format('%H uur, %I minuten, %S seconden');
        $leetText = "Tijd tot volgende 1337: $leetTime.";
        
        $antwoord = $leetText;
        $send = true;
    }

    if (in_array($text, array("winnen", "prijzenparade"))) {
        $prijzenparade_url = get_prijzen_parade_url();
        if ($prijzenparade_url) {
            $antwoord = "De link van de Tweakers December Prijzen Parade van vandaag is: " . $prijzenparade_url;
        } else {
            $antwoord = "Helaas, voor vandaag is er geen prijzenlink beschikbaar. Probeer het morgen nog eens!";
        }
        $send = true;
    }

    //Tourpoule. Functie staat in apart bestand.
    if (in_array($text, array("tourpoule", "tour", "poule"))) {
        $tourInfo = getTourInfo();
        if ($tourInfo) {
            $antwoord = $tourInfo;
        } else {
            $antwoord = "Er is even geen tourpoule info nu.";
        }
        $send = true;
    }


  if (!$send) {
      //Eerst op de hele zin/alle woorden zoeken ($text). Dit werkt voor geen meter....
      foreach ($antwoordenArray as $key => $value) {
          if (strstr($text, strtolower($antwoordenArray[$key]->trigger)) || strstr($text, ucfirst($antwoordenArray[$key]->trigger))) {
              echo $text." ".$key." antwoord: ".$antwoordenArray[$key]->antwoord;
              $antwoord = $antwoordenArray[$key]->antwoord;
              $send = true;
          }
      }
        
      //Daarna kijken naar de losse woorden
      if (!$send) {
          foreach ($losseWoorden as $wKey => $wValue) {
              foreach ($antwoordenArray as $key => $value) {
                  if (strstr($losseWoorden[$wKey], strtolower($antwoordenArray[$key]->trigger)) || strstr($losseWoorden[$wKey], ucfirst($antwoordenArray[$key]->trigger))) {
                      $antwoord = $antwoordenArray[$key]->antwoord;
                      $send = true;
                  }
              }
          }
      }
  }

//Random antwoord geven als hij niets weet...
    if (!$send && $text) {
        $randKey = array_rand($antwoordenArray, 1);
        $antwoord = $antwoordenArray[$randKey]->antwoord;

        //Vroeger deed de Snorbot dit
        //$antwoord = "Ik kan niets met: '".$text."'. Probeer eens een leuk weetje ofzo";
    }
    if ($antwoord) {
        $content = ['chat_id' => $chat_id, 'text' => $antwoord, 'parse_mode' => 'Markdown'];
        $telegram->sendMessage($content);
    }
