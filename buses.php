<?php

require("init.php");

$TAPI = new TransportAPI($credentials);

echo json_encode($TAPI->getDepartures());