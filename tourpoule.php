<?php

function getTourRanking(): string
{
    $pouleResult = json_decode(file_get_contents('https://www.geensnor.nl/tourpoule/api/summaryToday/'));

    $place = 1;
    $rankingReturn = '';
    foreach ($pouleResult->ranking as $rank) {
        $rankingReturn .= $place.". ".$rank->user.": ".$rank->totalPoints." \n";
        $place++;
    }

    return "*Klassement Geensnor Tourpoule*\n\n".$rankingReturn."\nBijgewerkt tot en met etappe ".$pouleResult->lastUpdate->stageNumber." op ".$pouleResult->lastUpdate->stageDate."\nZie de volledige uitslag op: [geensnor.nl/tourpoule/](https://www.geensnor.nl/tourpoule/)";
}
