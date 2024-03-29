<?php

function getTourRanking($tourLocation, string $tourName): string
{
    $ranking = json_decode(file_get_contents('https://www.geensnor.nl/tourpoule/rankingData/2021/tour-du-test/totalRanking.json'));

    $rankingReturn = '';
    foreach ($ranking as $rank) {
        $rankingReturn .= $rank->userName.': '.$rank->points." \n";
    }

    return 'Klassement Geensnor Tourpoule: '.$tourName." \n\n".$rankingReturn."\n\n[https://www.geensnor.nl/tourpoule/](https://www.geensnor.nl/tourpoule/)";
}

function getTourInfo(): string
{
    $currentTourLocationJSON = json_decode(file_get_contents('https://raw.githubusercontent.com/geensnor/Geensnor-Tourpoule-Data/main/currentTour.json'));
    $tourConfig = json_decode(file_get_contents('https://raw.githubusercontent.com/geensnor/Geensnor-Tourpoule-Data/main'.$currentTourLocationJSON->currentTourLocation.'/tourConfig.json'));

    if ($tourConfig->status != 'open') {
        $returnText = 'De volgende tour is '.$tourConfig->name.' die op '.$tourConfig->start." start. \nZodra er meer renners bekend zijn, kun je je eigen team maken op \n[https://www.geensnor.nl/tourpoule/](https://www.geensnor.nl/tourpoule/)";
    } else {//Tour is geopend.
        if (strtotime((string) $tourConfig->start) < time()) {
            $returnText = getTourRanking($currentTourLocationJSON->currentTourLocation, $tourConfig->name);
        } else {
            $returnText = 'Je kan je team voor '.$tourConfig->name." samenstellen op https://www.geensnor.nl/tourpoule. (even toegangscode regelen...) \nJe hebt de tijd tot de start op ".$tourConfig->start;
        }
    }

    return $returnText;
}
