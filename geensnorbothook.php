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

//Dag van de - Start
	if($text == 'dag van de' || $text == 'Dag van de' || $text == 'Het is vandaag' || $text == 'het is vandaag') {
		$dagVanDeArray = json_decode(file_get_contents("snorBotDagVanDe.json"));
    foreach ($dagVanDeArray as $key => $value) {
      if($dagVanDeArray[$key]->dag == date('d-m'))
        $dagText =  "Het is vandaag: \n".$dagVanDeArray[$key]->onderwerp;
    }
    if(!$dagText)
    	$dagText =  "Ik heb geen idee waar het vandaag een dag van is. Probeer het morgen nog een keer zou ik zeggen.";

    $content = array('chat_id' => $chat_id, 'text' => $dagText);	
		$telegram->sendMessage($content);
		$send = TRUE;
	}
//Dag van de - Einde


//bitcoin koers in euro

	if($text == 'Bitcoin' || $text == 'bitcoin') {
		$BCEuroObject = json_decode(file_get_contents("https://api.bitvavo.com/v1/currencies"));
		$content = array('chat_id' => $chat_id, 'text' => "€ ".$BCEuroObject->data[9]->ask_eur." (".$BCEuroObject->data[9]->pct_change_24hr."% in laatste 24 uur)");
		$telegram->sendMessage($content);
		$send = TRUE;
	}

// end of bitcoin koers in euro

//Hieronder staan weerdingen
	if($text == 'weer' || $text == 'weerbericht' || $text == 'weersvoorspelling' || $text == 'lekker weertje') {
		$weerObject = json_decode(file_get_contents("https://api.darksky.net/forecast/".getenv('DarkskyToken')."/52.100699,5.1542481?lang=nl&units=ca"));
		$content = array('chat_id' => $chat_id, 'text' => "Het weer voor de komende dagen in De Bilt: ".$weerObject->daily->summary);
		$telegram->sendMessage($content);
		$send = TRUE;
	}

	if($text == 'temperatuur Nijmegen') {
		$weerObject = json_decode(file_get_contents("https://api.darksky.net/forecast/".getenv('DarkskyToken')."/51.827359,5.853042?lang=nl&units=ca"));
		$content = array('chat_id' => $chat_id, 'text' => "In Nijmegen is het nu ".$weerObject->currently->temperature." graden celsius");
		$telegram->sendMessage($content);
		$send = TRUE;
	}

	if($text == 'temperatuur Utrecht') {
		$weerObject = json_decode(file_get_contents("https://api.darksky.net/forecast/".getenv('DarkskyToken')."/52.092921,5.123173?lang=nl&units=ca"));
		$content = array('chat_id' => $chat_id, 'text' => "In Utrecht is het nu ".$weerObject->currently->temperature." graden celsius");
		$telegram->sendMessage($content);
		$send = TRUE;
	}

//Hierboven staan weerdingen

//Beetje nieuws.....
	if($text == 'nieuws' || $text == 'Nieuws') {
		$nuxml = simplexml_load_file("https://www.nu.nl/rss");
		$content = array('chat_id' => $chat_id, 'text' => "Laatste nieuws van nu.nl: \n".$nuxml->channel->item[0]->title);
		$telegram->sendMessage($content);
		$send = TRUE;
	}
//Nieuws hierboven

$xml = simplexml_load_file("http://feeds.nos.nl/nosjournaal?format=xml");


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
	

	if($text == 'genereer wachtwoord') {
		$wachtwoord = json_decode (file_get_contents("https://www.passwordrandom.com/query?command=password&format=json&count=10"));
		$content = array('chat_id' => $chat_id, 'text' => "Random wachtwoord: ".$wachtwoord->char[1]);
		$telegram->sendMessage($content);
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
	if($text == "1337"){	
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
		if (14 <= $currentHour or ($currentHour == 13 and $currentMinute > 36))
			{$dayCompensator = 1;}
		$dayDate = date("y-m-d", strtotime("+ $dayCompensator day"));
		
		//Verschil berekenen en in tekst zetten
		$completedTime = new DateTime("$dayDate 13:37:00", new DateTimeZone($timeZone));
		$interval = $completedTime->diff($currentTime);
		$leetTime = $interval->format('%H uur, %I minuten, %S seconden');
		$leetText = "Tijd tot volgende 1337: $leetTime.";
		
		$antwoord = $leetText;
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




