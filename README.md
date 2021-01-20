# Description

A simple app to display 'departures board' style signage for a selected UK bus stop.

# Getting started

- Sign up for an account at https://developer.transportapi.com/ and create a new application.
> N.B. The limit is 1000 requests per day per Application ID, but (until they notice?) you can get round this by creating several 'Applications'.

- Copy and paste your credentials into `credentials.php`

- Browsing to `index.html` should display the departures board.

# Selecting a bus stop

TODO: Add a way for the user to select a bus stop from a map.

In the meantime, the bus stop (ATCO) code is stored in `data/selectedStop.txt`

You can find codes by uncommenting the `var_dump( $TAPI->getNearbyStops(53.759568, -1.656417) );` line in `test.php` and plugging in your own latitude and longitude.