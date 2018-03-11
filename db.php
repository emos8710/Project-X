<?php

if (count(get_included_files()) == 1)
    exit("Access restricted.");

$hostname = "localhost";
$username = "admin";
$password = "iamincontrolofthis";
$dbname = "upstrain";
$mysqli = $link = mysqli_connect($hostname, $username, $password, $dbname);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . mysqli_connect_error() . PHP_EOL . "<br>";
    exit;
}

mysqli_select_db($mysqli, $dbname);
?>