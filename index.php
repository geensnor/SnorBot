<?php

include 'config.php';

echo "SnorBot!<br><br>Voor meer informatie:<br><a href='https://github.com/geensnor/SnorBot'>https://github.com/geensnor/SnorBot</a><br><br>".getenv('environment');

    function getWeekNumberToday() {
        // Get the current date
        $currentDate = date('Y-m-d');
    
        // Convert the current date to a Unix timestamp
        $timestamp = strtotime($currentDate);
    
        // Use the date function to get the week number
        $weekNumber = date('W', $timestamp);
    
        // Return the week number
        return $weekNumber;
    }
    
    // Get and display the week number of today
    $weekNumberToday = getWeekNumberToday();
    echo "Het is vandaag week $weekNumberToday";
    