<?php

function getTodayRanking(): string
{
    $pouleResult = json_decode(file_get_contents('https://www.geensnor.nl/tourpoule/api/summaryToday/'));

    $place = 1;
    $rankingReturn = '';

    // Verzamel alle namen en punten om de maximale lengte te bepalen
    $maxNameLen = 0;
    $maxPointsLen = 0;
    foreach ($pouleResult->latestStageUserRanking as $todayRank) {
        $nameLen = mb_strlen($todayRank->name);
        $pointsLen = mb_strlen((string)$todayRank->points);
        if ($nameLen > $maxNameLen) {
            $maxNameLen = $nameLen;
        }
        if ($pointsLen > $maxPointsLen) {
            $maxPointsLen = $pointsLen;
        }
    }

    // Bouw de uitgelijnde lijst op
    foreach ($pouleResult->latestStageUserRanking as $todayRank) {
        $name = html_entity_decode($todayRank->name);
        $points = $todayRank->points;
        // Bereken het aantal punten dat nodig is om op te vullen
        $dotsCount = ($maxNameLen - mb_strlen($name)) + 3; // 3 extra voor vaste spatie na naam
        $dots = str_repeat('.', $dotsCount);
        $rankingReturn .= sprintf(
            "`%2d. %s%s %{$maxPointsLen}d`\n",
            $place,
            $name,
            $dots,
            $points
        );
        $place++;
    }

    return "*Score etappe ".$pouleResult->lastUpdate->stageNumber.": ".$pouleResult->stageToday->route."*\n\n".$rankingReturn."\nZie de volledige uitslag op: [geensnor.nl/tourpoule/](https://www.geensnor.nl/tourpoule/)";
}

/**
 * Maakt een mooi overzicht van de Geensnor Tourpoule deelnemers
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
    $maxNameLen = 0;
    $maxPointsLen = 0;
    foreach ($pouleResult->ranking as $rank) {
        $nameLen = mb_strlen(html_entity_decode($rank->user));
        $pointsLen = mb_strlen((string)$rank->totalPoints);
        if ($nameLen > $maxNameLen) {
            $maxNameLen = $nameLen;
        }
        if ($pointsLen > $maxPointsLen) {
            $maxPointsLen = $pointsLen;
        }
    }

    // Bouw de uitgelijnde lijst op
    foreach ($pouleResult->ranking as $rank) {
        $name = html_entity_decode($rank->user);
        $points = $rank->totalPoints;
        // Bereken het aantal punten dat nodig is om op te vullen
        $dotsCount = ($maxNameLen - mb_strlen($name)) + 3; // 3 extra voor vaste spatie na naam
        $dots = str_repeat('.', $dotsCount);
        $rankingReturn .= sprintf(
            "`%2d. %s%s %{$maxPointsLen}d`\n",
            $place,
            $name,
            $dots,
            $points
        );
        $place++;
    }

    return "*Klassement Geensnor Tourpoule*\n\n".$rankingReturn."\nBijgewerkt tot en met etappe ".$pouleResult->lastUpdate->stageNumber." op ".$pouleResult->lastUpdate->stageDate."\nZie de volledige uitslag op: [geensnor.nl/tourpoule/](https://www.geensnor.nl/tourpoule/)";
}
