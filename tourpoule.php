<?php

/**
 * Maakt een mooi overzicht van het uitslag van de laatste etappe van de Geensnor Tourpoule
 *
 * Haalt de uitslag informatie op van de Geensnor Tourpoule API maakt er een lijst van
 *
 * @return string Lijst van deelnemers met hun score in de laatste etappe
 */
function getTodayRanking(): string
{
    $pouleResult = json_decode(file_get_contents('https://www.geensnor.nl/tourpoule/api/summaryToday/'));

    $place = 1;
    $rankingReturn = '';

    foreach ($pouleResult->latestStageUserRanking as $rank) {
        $rankingReturn .= $place.". ".html_entity_decode($rank->name).": ".$rank->points." \n";
        $place++;
    }

    return "*Score etappe ".$pouleResult->lastUpdate->stageNumber.": ".$pouleResult->stageToday->route."*\n\n".$rankingReturn."\nZie de volledige uitslag op: [geensnor.nl/tourpoule/](https://www.geensnor.nl/tourpoule/)";
}

/**
 * Maakt een mooi overzicht van het Geensnor Tourpoule klassement
 *
 * Haalt de ranking informatie op van de Geensnor Tourpoule API maakt er een lijst van
 *
 * @return string Lijst van deelnemers met hun punten
 */
function getTourRanking(): string
{
    $pouleResult = json_decode(file_get_contents('https://www.geensnor.nl/tourpoule/api/summaryToday/'));

    $place = 1;
    $rankingReturn = '';

    // Verzamel alle namen en punten om de maximale lengte te bepalen
    foreach ($pouleResult->ranking as $rank) {
        $rankingReturn .= $place.". ".html_entity_decode($rank->user).": ".$rank->totalPoints." \n";
        $place++;
    }

    return "*Klassement Geensnor Tourpoule*\n\n".$rankingReturn."\nBijgewerkt tot en met etappe ".$pouleResult->lastUpdate->stageNumber." op ".$pouleResult->lastUpdate->stageDate."\nZie de volledige uitslag op: [geensnor.nl/tourpoule/](https://www.geensnor.nl/tourpoule/)";
}
