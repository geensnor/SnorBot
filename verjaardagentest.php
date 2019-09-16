<?php
include("cl_verjaardagen.php");
include("config.php");
$v = new verjaardag;

echo $v->checkKomendeDagen();

echo "<br><Br>".$v->getVerjaardagTekst();


?>