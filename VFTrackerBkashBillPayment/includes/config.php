<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
  $db = new PDO("mysql:host=$servername;dbname=vftracke_track_uat", $username, $password);
  //set the PDO error mode to exception
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

define('APP_NAME', "VFTracker Bkash Bill Payment")

?>