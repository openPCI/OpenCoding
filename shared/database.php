<?php
global $secretdir;
$database=trim(file_get_contents($secretdir.".htdatabase"));
$db=explode(",",$database);
$mysqli = new mysqli($db[0],$db[1],$db[2],$db[3]);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}
