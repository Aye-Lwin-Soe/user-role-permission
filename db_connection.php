<?php
$servername = "mysql-host"; //localhost
$username = "root";
$password = "root";
$dbname = "user_role_permission";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
