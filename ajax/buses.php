<?php

require(dirname(__FILE__) . "/../init.php");

$TAPI = new TransportAPI($credentials);

echo json_encode($TAPI->getDepartures($_GET["atcocode"]));