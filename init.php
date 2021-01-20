<?php

require "classes/TransportAPI.class.php";

if (file_exists("credentials.mm.php")) {
	require "credentials.mm.php";
} else {
	require "credentials.php";
}

// Check cached credIndex is less than the number of stored credentials
$credIndex = (int) file_get_contents("data/credIndex.txt");
if ($credIndex >= count($credentials)) {
	file_put_contents("data/credIndex.txt", 0);
}