<?php

function isToday($date_str)
{
    // based on https://stackoverflow.com/a/25623230/204807
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $match_date = new DateTime($date_str);
    $match_date->setTime(0, 0, 0);
    $diff = $today->diff($match_date);
    $diff_days = (int)$diff->format("%R%a");
    return $diff_days === 0;
}

function get_prijzen_parade_url()
{
    // based on https://stackoverflow.com/q/4887300/204807
    $rss = simplexml_load_file('https://tweakers.net/feeds/mixed.xml');
    foreach ($rss->channel->item as $item) {
        $title = $item->title;
        $publication_date = $item->pubDate;
        if (strpos($title, ".Actie - December Prijzen") === 0 && isToday($publication_date)) {
            return $item->link;
        }
    }
}
