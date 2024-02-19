<?php
$servername = "localhost";//put your server name here
$username = "root";//put your username here
$password = "";//put your password here
$database = "minus2";//put your database name here


$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
