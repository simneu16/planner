<?php
$servername = "mariadb114.r1.websupport.sk";
$username = "J89tCmgP";
$password = "J89tCmgP€";
$dbname = "3jAKSbta";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Neúspšne: " . mysqli_connect_error());
  }
?>