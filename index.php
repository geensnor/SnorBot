<?php

include 'config.php';

echo "SnorBot!<br>Voor meer informatie:<br><a href='https://github.com/geensnor/SnorBot'>https://github.com/geensnor/SnorBot</a><br><br>Omgeving: ".getenv('environment');


$serverStatus = json_decode(file_get_contents('https://api.telegram.org/bot'.getenv('telegramId').'/getWebhookInfo'));


if ($serverStatus->result->last_error_date) {
    echo" <br><br>Er gaat iets niet lekker";
    echo "<br>Error tijd: ".date('Y-m-d H:i:s', $serverStatus->result->last_error_date);
    echo "<br>Error van server: ".$serverStatus->result->last_error_message;
    echo "<br>Wachtrij: ".$serverStatus->result->pending_update_count;
}
