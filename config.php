<?php
// Database Connection (config.php)
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'inventory_teacher2';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
