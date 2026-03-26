<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'testbank';

$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
} else {
    echo "Connection successful!";
}
?>
