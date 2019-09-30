<?php
include (__DIR__ . '/vendor/autoload.php');
include("advies.php");

$telegram = new Telegram(getenv('telegramId'));

$antwoordenArray = json_decode(file_get_contents("snorBotAntwoorden.json"));
$weetjesArray = json_decode(file_get_contents("https://raw.githubusercontent.com/geensnor/weetjes/master/snorBotWeetjes.json"));
$DooddoenerArray = json_decode(file_get_contents("https://raw.githubusercontent.com/geensnor/dooddoeners/master/dooddoener.json"));

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

    foreach ($losseWoorden as $wKey => $wValue){
    	//Dit werkt niet op heroku. Wel lokaal. Als dit uitstaat werkt dat if verhaal hierboven met location ook niet. Dus die staat gewoon aan.
    	if($losseWoorden[$wKey] == "advies" || $losseWoorden[$wKey] == "Advies"){
		
			$option = array(array($telegram->buildKeyBoardButton("Klik hier om je locatie te delen", $request_contact=false, $request_location=true)));
			$keyb = $telegram->buildKeyBoard($option, $onetime=false);
			$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Aaaah, je wilt een advies van Geensnor. Goed idee! Druk op de knop hieronder aan te geven waar je bent.");
			$telegram->sendMessage($content);
			$send = TRUE;
		}
		if($losseWoorden[$wKey] == "weetje" || $losseWoorden[$wKey] == "Weetje"){
    		$randKey = array_rand($weetjesArray, 1);
    		$antwoord = $weetjesArray[$randKey];
    		$send = TRUE;
		}		
		if($losseWoorden[$wKey] == "dooddoener" || $losseWoorden[$wKey] == "Dooddoener"){
			$randKey = array_rand($DooddoenerArray, 1);
			$antwoord = $DooddoenerArray[$randKey];
			$send = TRUE;
		}
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

 //}