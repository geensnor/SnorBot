<?php

function getDaysSince($date)
{
    return floor((time() - strtotime($date)) / (60 * 60 * 24));
}

echo getDaysSince("11-11-2012");
