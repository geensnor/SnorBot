<?php

include 'utilities.php';

function getCyclingNews()
{
    $nuxml = simplexml_load_file('http://feeds.nos.nl/nossportwielrennen');

    return "Laatste wielrennieuws van nos.nl: \n[".$nuxml->channel->item[0]->title.']('.$nuxml->channel->item[0]->link.')';
}
