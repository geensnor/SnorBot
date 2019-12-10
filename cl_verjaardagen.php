<?php
class verjaardag{

    var $geboortedatums;

    function verjaardag(){
        $GithHubAPIUrl = "https://api.github.com/repos/reithose/geboortedatums/contents/";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "reithose:".getenv('githubToken'));
        curl_setopt($curl, CURLOPT_USERAGENT, "User-Agent: reithose");
        curl_setopt($curl, CURLOPT_URL, $GithHubAPIUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if (!curl_exec($curl)) {
          die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        else{
          $curlResult = json_decode(curl_exec($curl));
        }

        curl_close($curl);

        $this->geboortedatums = json_decode(file_get_contents($curlResult[0]->download_url));
    }

    function getVerjaardagenData(){
        $referentieDatum = strtotime(date('Y-m-d'));

        foreach ($this->geboortedatums as $key => $value) {
        	
        	$verjaardagDitJaar = strtotime(date("Y").date("-m-d", strtotime($this->geboortedatums[$key]->geboortedatum)));
        	$verschil = $verjaardagDitJaar - $referentieDatum;

        	$persoon["naam"] = $this->geboortedatums[$key]->naam;
        	$persoon["geboortedatum"] = date('d-m-Y', strtotime($this->geboortedatums[$key]->geboortedatum));
        	$persoon["leeftijd"] = floor(($referentieDatum - strtotime($this->geboortedatums[$key]->geboortedatum))/31557600);
            if($verschil >= 0){
            	
            	$persoon["dagenTotVerjaardag"] = round($verschil/86400);
            	$persoon["datumVerjaardag"] = date('d-m-Y', $verjaardagDitJaar);
                
            } 
            else{//Verjaardag is al geweest dit jaar
            	$verjaardagVolgendJaar = strtotime(date("Y",strtotime("+1 year")).date("-m-d", strtotime($this->geboortedatums[$key]->geboortedatum)));
            	$verschil = $verjaardagVolgendJaar  - $referentieDatum;
            	$persoon["dagenTotVerjaardag"] = round($verschil/86400);
            	$persoon["datumVerjaardag"] = date('d-m-Y', $verjaardagVolgendJaar);

            }
            $verjaardagenData[] = $persoon;
        	
        }

        usort($verjaardagenData, function($a, $b) {
            return $a['dagenTotVerjaardag'] - $b['dagenTotVerjaardag'];
        });

        return $verjaardagenData;
    }

    function getVerjaardagTekst(){
        $verjaardagenData = $this->getVerjaardagenData();
        if($verjaardagenData[0]["dagenTotVerjaardag"] == 0)
            return "Hoera! ".$verjaardagenData[0]["naam"]." wordt vandaag ".($verjaardagenData[0]["leeftijd"])." jaar oud!";
        else
            return $verjaardagenData[0]["naam"]." is de volgende die jarig is. Hij/zij wordt over ".$verjaardagenData[0]["dagenTotVerjaardag"]." dagen (".$verjaardagenData[0]["datumVerjaardag"].") ".($verjaardagenData[0]["leeftijd"] + 1)." jaar.";
    }

    function checkKomendeDagen(){
        $verjaardagenData = $this->getVerjaardagenData();
        if($verjaardagenData[0]["dagenTotVerjaardag"] == 0)
            $returnString = "Hoera! ".$verjaardagenData[0]["naam"]." wordt vandaag ".($verjaardagenData[0]["leeftijd"])." jaar oud!";
        if($verjaardagenData[0]["dagenTotVerjaardag"] == 1)
            $returnString = "Morgen wordt ".$verjaardagenData[0]["naam"]." al weer ".($verjaardagenData[0]["leeftijd"] + 1)." jaar oud!";
        
        return $returnString;
    }
}



?>

