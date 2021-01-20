<?php

require "../init.php";

$lats = explode(",", $_GET["lats"]);
$lons = explode(",", $_GET["lons"]);

$TAPI = new TransportAPI($credentials);

echo json_encode($TAPI->getBoundingBoxStops($lats, $lons) );