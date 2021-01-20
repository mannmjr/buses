<pre><?php

require "init.php";

$TAPI = new TransportAPI($credentials);

var_dump( $TAPI->getNearbyStops(53.759568, -1.656417) );

// var_dump( $TAPI->getDepartures() );