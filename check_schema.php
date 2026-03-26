<?php
require_once 'config/database.php';

echo "--- STUDENTS TABLE ---\n";
$res = $conn->query('DESCRIBE students');
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . " | " . $row['Type'] . "\n";
    }
}

echo "\n--- ADMINS TABLE ---\n";
$res2 = $conn->query('DESCRIBE admins');
if ($res2) {
    while($row2 = $res2->fetch_assoc()) {
        echo $row2['Field'] . " | " . $row2['Type'] . "\n";
    }
}
?>
