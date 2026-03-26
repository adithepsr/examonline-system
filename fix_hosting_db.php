<?php
/**
 * Database Fix Script for Hosting
 * Upload this file to your website root (where index.php is) and open it in your browser.
 * e.g., yourdomain.com/fix_hosting_db.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

echo "<h1>Auto-Fix Database on Hosting</h1>";

// 1. Fix 'questions' table - Add 'question_type' column
echo "<h3>1. Checking 'questions' table...</h3>";
$check_col = $conn->query("SHOW COLUMNS FROM questions LIKE 'question_type'");
if ($check_col->num_rows == 0) {
    echo "Column 'question_type' missing. Adding it...<br>";
    $sql = "ALTER TABLE questions ADD COLUMN question_type ENUM('multiple_choice', 'subjective') DEFAULT 'multiple_choice'";
    if ($conn->query($sql)) {
        echo "<span style='color:green'>Success: Added 'question_type' column.</span><br>";
    } else {
        echo "<span style='color:red'>Error: " . $conn->error . "</span><br>";
    }
} else {
    echo "<span style='color:green'>Column 'question_type' already exists.</span><br>";
}

// 2. Fix 'student_answers' table - Change 'student_answer' to TEXT
echo "<h3>2. Checking 'student_answers' table...</h3>";
// Check if it's ENUM or TEXT. We can just try to modify it to TEXT.
$sql = "ALTER TABLE student_answers MODIFY COLUMN student_answer TEXT";
if ($conn->query($sql)) {
    echo "<span style='color:green'>Success: Updated 'student_answer' column to TEXT (to support subjective answers).</span><br>";
} else {
    echo "<span style='color:red'>Error: " . $conn->error . "</span><br>";
}

// 3. Create 'admin_users' if missing (referencing previous error)
echo "<h3>3. Checking 'admin_users' table...</h3>";
$check_table = $conn->query("SHOW TABLES LIKE 'admin_users'");
if ($check_table->num_rows == 0) {
    echo "Table 'admin_users' missing. Checking 'admins' table...<br>";
    $check_admins = $conn->query("SHOW TABLES LIKE 'admins'");
    
    if ($check_admins->num_rows > 0) {
        echo "Table 'admins' found. Duplicating as 'admin_users' (renaming strategy)...<br>";
        // Create admin_users from admins structure
        $sql = "CREATE TABLE admin_users LIKE admins";
        if ($conn->query($sql)) {
            $conn->query("INSERT INTO admin_users SELECT * FROM admins");
             // Add role column to admin_users if not exists
             $conn->query("ALTER TABLE admin_users ADD COLUMN role ENUM('admin', 'teacher', 'staff') DEFAULT 'admin'");
            echo "<span style='color:green'>Success: Created 'admin_users' from 'admins'.</span><br>";
        } else {
             echo "<span style='color:red'>Error creating table: " . $conn->error . "</span><br>";
        }
    } else {
        // Create fresh
        $sql = "CREATE TABLE IF NOT EXISTS admin_users (
            admin_id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            role ENUM('admin', 'teacher', 'staff') DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($sql)) {
             echo "<span style='color:green'>Success: Created 'admin_users' table.</span><br>";
             // Add default admin
             $pass = password_hash('admin123', PASSWORD_DEFAULT);
             $conn->query("INSERT IGNORE INTO admin_users (username, password, full_name, role) VALUES ('admin', '$pass', 'Admin', 'admin')");
        } else {
             echo "<span style='color:red'>Error: " . $conn->error . "</span><br>";
        }
    }
} else {
    echo "<span style='color:green'>Table 'admin_users' already exists.</span><br>";
}

echo "<hr>";
echo "<h3>Done!</h3>";
echo "You can now try submitting the exam again.<br>";
echo "<strong style='color:red'>Security Warning: Please delete this file (fix_hosting_db.php) from your server after use.</strong>";
?>
