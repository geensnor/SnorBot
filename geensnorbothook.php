<?php
include (__DIR__ . '/vendor/autoload.php');
include("advies.php");

$telegram = new Telegram(getenv('telegramId'));

$antwoordenArray = json_decode(file_get_contents("snorBotAntwoorden.json"));
$weetjesArray = json_decode(file_get_contents("https://raw.githubusercontent.com/geensnor/weetjes/master/snorBotWeetjes.json"));
$DooddoenerArray = json_decode(file_get_contents("https://raw.githubusercontent.com/geensnor/dooddoeners/master/dooddoener.json"));
$verveelArray = json_decode(file_get_contents("https://raw.githubusercontent.com/geensnor/verveellijst/master/verveellijst.json"));

$text = ltrim($telegram->Text(), '/');
$chat_id = $telegram->ChatID();

$losseWoorden = explode(" ", $text);
$antwoord = "";
$send = FALSE;


//bitcoin koers in euro

	if($text == 'Bitcoin' || $text == 'bitcoin') {
		$BCEuroObject = json_decode(file_get_contents("https://api.bitvavo.com/v1/currencies"));
		$content = array('chat_id' => $chat_id, 'text' => "€ ".$BCEuroObject->data[9]->ask_eur." (".$BCEuroObject->data[9]->pct_change_24hr."% in laatste 24 uur)");
		$telegram->sendMessage($content);
		$send = TRUE;
	}

// end of bitcoin koers in euro

	if($text == 'weer' || $text == 'weerbericht' || $text == 'weersvoorspelling' || $text == 'lekker weertje') {
		$weerObject = json_decode(file_get_contents("https://api.darksky.net/forecast/".getenv('DarkskyToken')."/52.100699,5.1542481?lang=nl&units=ca"));
		$content = array('chat_id' => $chat_id, 'text' => "Het weer voor de komende dagen in De Bilt: ".$weerObject->daily->summary);
		$telegram->sendMessage($content);
		$send = TRUE;
	}

	if($text == 'xkcd' || $text == 'Xkcd') {
		$xkcdData = json_decode(file_get_contents("https://xkcd.com/info.0.json"));
		$randomComicNumber = rand(0, $xkcdData->num);
		$randomComicObject = json_decode(file_get_contents("http://xkcd.com/".$randomComicNumber."/info.0.json"));
        $content = array('chat_id' => $chat_id, 'photo' => $randomComicObject->img);
        $telegram->sendPhoto($content);
        $antwoord = "Random XKCD comic. Typ 'xkcd nieuwste' voor de nieuwste";
        $send = TRUE;
    }

    if($text == 'xkcd nieuwste' || $text == 'Xkcd nieuwste') {
		$xkcdData = json_decode(file_get_contents("https://xkcd.com/info.0.json"));
        $content = array('chat_id' => $chat_id, 'photo' => $xkcdData->img);
        $telegram->sendPhoto($content);
        $send = TRUE;
    }
    
	if($text == 'verjaardag' || $text == 'Verjaardag' || $text == 'jarig' || $text == 'Jarig' || $text == 'Verjaardagen' || $text == 'verjaardagen') {
		include("cl_verjaardagen.php");
		$v = new verjaardag;
		$antwoord = $v->getVerjaardagTekst();
        $send = TRUE;
    }

    if($telegram->Location()){
    	$locatieGebruiker = $telegram->Location();
		$adviesJson = getAdviesArray($locatieGebruiker["latitude"], $locatieGebruiker["longitude"]);

		$contentAdviesTitel = ['chat_id' => $chat_id, 'text' => $adviesJson[0]->name." zit in de buurt:"];
		$contentAdviesToelichting = ['chat_id' => $chat_id, 'text' => "Geensnor zegt: '".$adviesJson[0]->description."'. Kijk op http://advies.geensnor.nl voor meer adviezen"];
		$contentLocation = ['chat_id' => $chat_id, 'latitude' => $adviesJson[0]->lat, 'longitude' => $adviesJson[0]->lon];
		$telegram->sendMessage($contentAdviesTitel);
		$telegram->sendLocation($contentLocation);
		$telegram->sendMessage($contentAdviesToelichting);
		$send = TRUE;

    }

	if($text == "advies" || $text  == "Advies"){
		$option = array(array($telegram->buildKeyBoardButton("Klik hier om je locatie te delen", $request_contact=false, $request_location=true)));
		$keyb = $telegram->buildKeyBoard($option, $onetime=false);
		$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Aaaah, je wilt een advies van Geensnor. Goed idee! Druk op de knop hieronder aan te geven waar je bent.");
		$telegram->sendMessage($content);
		$send = TRUE;
	}

	if($text == "nieuwste weetje" || $text == "Nieuwste weetje"){
		$antwoord = end($weetjesArray);
		$send = TRUE;
	}

	if($text  == "weetje" || $text  == "Weetje"){
		$randKey = array_rand($weetjesArray, 1);
		$antwoord = $weetjesArray[$randKey];
		$send = TRUE;
	}    
	if($text  == "dooddoener" || $text  == "Dooddoener"){
		$randKey = array_rand($DooddoenerArray, 1);
		$antwoord = $DooddoenerArray[$randKey];
		$send = TRUE;
	}
	if($text  == "verveel" || $text  == "wat zal ik doen"){
		$randKey = array_rand($verveelArray, 1);
		$antwoord = $verveelArray[$randKey];
		$send = TRUE;
	}
	if($text == "volgende 1337" || "Volgende 1337"){
		$antwoord = nextLeet();
		$send = TRUE;
	}
    if(!$send){
//Eerst op de hele zin/alle woorden zoeken ($text). Dit werkt voor geen meter....
		foreach ($antwoordenArray as $key => $value) {
	    	if(strstr($text, strtolower($antwoordenArray[$key]->trigger)) || strstr($text, ucfirst($antwoordenArray[$key]->trigger))){
	    		echo $text." ".$key." antwoord: ".$antwoordenArray[$key]->antwoord;
	    		$antwoord = $antwoordenArray[$key]->antwoord;
	    		$send = TRUE;	    			    		
	    	}
	    }
	    	
//Daarna kijken naar de losse woorden
    	if(!$send){
	    	foreach ($losseWoorden as $wKey => $wValue){
	 			foreach ($antwoordenArray as $key => $value) {
			    	if(strstr($losseWoorden[$wKey], strtolower($antwoordenArray[$key]->trigger)) || strstr($losseWoorden[$wKey], ucfirst($antwoordenArray[$key]->trigger))){
			    		$antwoord = $antwoordenArray[$key]->antwoord;
			    		$send = TRUE;	    			    		
			    	}
			    }
	    	}
    	}
    }	
	if(!$send && $text){
		$antwoord = "Ik kan niets met: '".$text."'. Probeer eens een leuk weetje ofzo";
	}
	if($antwoord){
		$content = ['chat_id' => $chat_id, 'text' => $antwoord, 'parse_mode' => 'Markdown'];
		$telegram->sendMessage($content);
	}

//Functies

//Functie om volgende 1337 te berekenen
function nextLeet()
	{
	$newDate = changeTimeZone(date('y-m-d H:i:s'),'','Europe/Amsterdam');
	$assignedTime= $newDate;
	$dayCompensator = pastleetCheck();
	$dayDate = date("y-m-d", strtotime("+ $dayCompensator day"));
	$completedTime   = "$dayDate 13:37:00";

	$d1 = new DateTime($assignedTime);
	$d2 = new DateTime($completedTime);
	$interval = $d2->diff($d1);
	$leetTime = $interval->format('%H uur, %I minuten, %S seconden');
	$tmzn = date_default_timezone_get();

	return "Tijd tot volgende 1337: $leetTime.";
}

//Functie om te zorgen dat de juiste tijdzonde gebruikte wordt
function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null)
	{
  	if (empty($timeZoneSource)) 
		{$timeZoneSource = date_default_timezone_get();}
  	if (empty($timeZoneTarget)) 
		{$timeZoneTarget = date_default_timezone_get();}
	$dt = new DateTime($dateString, new DateTimeZone($timeZoneSource));
  	$dt->setTimezone(new DateTimeZone($timeZoneTarget));
	return $dt->format('y-m-d H:i:s');
	}

//Functie om te kijken of 1337 vandaag al geweest is om morgen (tijdsverschil werkt anders niet)
function pastleetCheck()
	{
	$currentHour = Date('H');
	$currentMinute = Date('i');
	if (14 > $currentHour or ($currentHour = 13 and $currentMinute < 37))
		{$dayCompensator = 0;}
	else
		{$dayCompensator = 1;}
	}

 //}
