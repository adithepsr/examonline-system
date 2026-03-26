<?php
require_once('config/database.php');

// Add role column to admins
$sql = "SHOW COLUMNS FROM admins LIKE 'role'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE admins ADD COLUMN role ENUM('admin', 'teacher') DEFAULT 'teacher' AFTER email";
    if ($conn->query($sql)) {
        echo "Added 'role' column to admins.<br>";
        // Set ID 1 to admin
        $conn->query("UPDATE admins SET role = 'admin' WHERE admin_id = 1");
        echo "Updated admin_id 1 to role 'admin'.<br>";
    } else {
        echo "Error adding column role: " . $conn->error . "<br>";
    }
}

// Add created_by to exam_sets
$sql = "SHOW COLUMNS FROM exam_sets LIKE 'created_by'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE exam_sets ADD COLUMN created_by INT NULL AFTER is_active";
    if ($conn->query($sql)) {
        echo "Added 'created_by' column to exam_sets.<br>";
        // Update existing sets to be owned by admin 1
        $conn->query("UPDATE exam_sets SET created_by = 1 WHERE created_by IS NULL");
    } else {
        echo "Error adding column created_by: " . $conn->error . "<br>";
    }
}

echo "Database schema update completed.";
?>
