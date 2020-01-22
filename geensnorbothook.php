<?php
date_default_timezone_set('Europe/Amsterdam');
include (__DIR__ . '/vendor/autoload.php');
include("advies.php");

$telegram = new Telegram(getenv('telegramId'));

$antwoordenArray = json_decode(file_get_contents("snorBotAntwoorden.json"));

$weetjesLocatie = "https://raw.githubusercontent.com/geensnor/SnorLijsten/master/weetjes.json";
$dooddoenerLocatie = "https://raw.githubusercontent.com/geensnor/SnorLijsten/master/dooddoeners.json";
$verveelLocatie = "https://raw.githubusercontent.com/geensnor/verveellijst/master/verveellijst.json";
$dagVanDeLocatie = "https://raw.githubusercontent.com/geensnor/SnorLijsten/master/dagvande.json";
$haikuLocatie = "https://raw.githubusercontent.com/geensnor/SnorLijsten/master/haiku.json";



$text = ltrim($telegram->Text(), '/');
$chat_id = $telegram->ChatID();

$losseWoorden = explode(" ", $text);
$antwoord = "";
$send = FALSE;

//Dag van de - Start
	if($text == 'dag van de' || $text == 'Dag van de' || $text == 'Het is vandaag' || $text == 'het is vandaag' || $text == 'dag' || $text == 'Dag' || $text == 'dag van' || $text == 'Dag van') {
		$dagVanDeArray = json_decode(file_get_contents($dagVanDeLocatie));
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
	if($text == 'weer'|| $text == 'Weer' || $text == 'weerbericht' || $text == 'weersvoorspelling' || $text == 'lekker weertje') {
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

//Hieronder de wiki dingen

	if(substr($text, 0, 4) == 'wiki') {
		$wikiResult = json_decode(file_get_contents("https://nl.wikipedia.org/w/api.php?action=opensearch&search=".substr($text, 5)."&limit=10&namespace=0&format=json"));

		foreach ($wikiResult[1] as $key => $value) {
  		$markdownList .= "[".$wikiResult[1][$key]."](". $wikiResult[3][$key].")\n";
		}
		$content = array('chat_id' => $chat_id, 'text' => "Ah, je wil iets van ".substr($text, 5)." weten. Staat hier iets tussen?\n\n"$markdownList, 'parse_mode' => 'Markdown', 'disable_web_page_preview' => FALSE);
		//$content = "lalalaalal";
		$telegram->sendMessage($content);
		$send = TRUE;
	}


//Hierboven de wiki dingen	

//Beetje nieuws.....
	if($text == 'nieuws' || $text == 'Nieuws') {
		$nuxml = simplexml_load_file("https://www.nu.nl/rss");
		$content = array('chat_id' => $chat_id, 'text' => "Laatste nieuws van nu.nl: \n".$nuxml->channel->item[0]->title);
		$telegram->sendMessage($content);
		$send = TRUE;
	}
//Nieuws hierboven


//Is het al 5 uur?
	if($text == 'is het al vijf uur' || $text == 'Is het al vijf uur' || $text == 'Is het al 5 uur' || $text == 'is het al 5 uur') {
		$content = array('chat_id' => $chat_id, 'text' => "Nee, het is ".date("H:i:s"));
		$telegram->sendMessage($content);
		$send = TRUE;
	}
////////////////

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
	
	if($text == 'guid') {
		$guid = json_decode (file_get_contents("https://www.passwordrandom.com/query?command=guid&format=json&count=10"));
		$content = array('chat_id' => $chat_id, 'text' => "Random guid: ".$guid->char[1]);
		$telegram->sendMessage($content);
		$send = TRUE;
	}
	
//nog niet klaar
	//if($text == 'random word') {
	//	$word = json_decode (file_get_contents("https://raw.githubusercontent.com/RazorSh4rk/random-word-api/master/words.json"));
	//	$randword = array_rand ($word, 1);
	//	$content = array('chat_id' => $chat_id, 'text' => "Random engels woord: ".word[]);
	//	$telegram->sendMessage($content);
	//	$send = TRUE;
	//}

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
		$weetjesArray = json_decode(file_get_contents($weetjesLocatie));
		if(json_last_error() === JSON_ERROR_NONE)
			$antwoord = end($weetjesArray);
		else
			$antwoord = "Kan geen nieuw weetje ophalen. De weetjes json is niet helemaal lekker.";
		$send = TRUE;
	}

	if($text  == "weetje" || $text  == "Weetje"){
		$weetjesArray = json_decode(file_get_contents($weetjesLocatie));
		if(json_last_error() === JSON_ERROR_NONE) {
			$randKey = array_rand($weetjesArray, 1);
			$antwoord = $weetjesArray[$randKey];
		}
		else
			$antwoord = "Kan geen weetje ophalen. De weetjes json is niet helemaal lekker.";
		$send = TRUE;
	}    

	if($text  == "dooddoener" || $text  == "Dooddoener"){
		$dooddoenerArray = json_decode(file_get_contents($dooddoenerLocatie));
		if(json_last_error() === JSON_ERROR_NONE){
			$randKey = array_rand($dooddoenerArray, 1);
			$antwoord = $dooddoenerArray[$randKey];
		}
		else
			$antwoord = "Kan geen dooddoener vinden. De json file is naar de vaantjes";
		$send = TRUE;
	}

	if($text  == "verveel" || $text  == "wat zal ik doen"){
		$verveelArray = json_decode(file_get_contents($verveelLocatie));
		if(json_last_error() === JSON_ERROR_NONE){
			$randKey = array_rand($verveelArray, 1);
			$antwoord = $verveelArray[$randKey];
		}
		else 
			$antwoord = "Verveel je je ja? Nou, ik kan je ook niet helpen want ik kan de json met leuke dingen om te doen niet vinden.";
		$send = TRUE;
	}

	if($text  == "haiku" || $text  == "Haiku"){
		$haikuArray = json_decode(file_get_contents($haikuLocatie));
		if(json_last_error() === JSON_ERROR_NONE){
			$randKey = array_rand($haikuArray, 1);
			$antwoord = $haikuArray[$randKey];
		}
		else
			$antwoord = "De JSON is stuk \nde haiku's zijn verdwenen \nwie kan mij helpen?";
		$send = TRUE;
	}

	if($text == "nieuwste haiku" || $text == "Nieuwste haiku"){
		$haikuArray = json_decode(file_get_contents($haikuLocatie));
		if(json_last_error() === JSON_ERROR_NONE)
			$antwoord = end($haikuArray);
		else
			$antwoord = "De JSON is stuk \nde haiku's zijn verdwenen \nwie kan mij helpen?";
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




