<?php

require dirname(__FILE__) . "/classes/TransportAPI.class.php";

if (file_exists(dirname(__FILE__) . "/credentials.mm.php")) {
	require dirname(__FILE__) . "/credentials.mm.php";
} else {
	require dirname(__FILE__) . "/credentials.php";
}

// Check cached credIndex is less than the number of stored credentials
$credIndex = (int) file_get_contents(dirname(__FILE__) . "/data/credIndex.txt");
if ($credIndex >= count($credentials)) {
	file_put_contents(dirname(__FILE__) . "/data/credIndex.txt", 0);
}