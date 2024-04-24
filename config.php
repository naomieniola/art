<?php
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "login_signup";

$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

if (!$conn) {
    die("Connection failed. Database not connecting: " . mysqli_connect_error());
}

