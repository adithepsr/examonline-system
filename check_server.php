<?php
/**
 * Script for diagnosing E-Exam Server Environment
 * Upload this file to your hosting server (same level as index.php) and run it.
 */

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>E-Exam Server Diagnostic</h1>";
echo "<hr>";

// 1. PHP Version
echo "<h3>1. PHP Version</h3>";
echo "PHP Version: " . phpversion() . " <br>";
if (version_compare(phpversion(), '7.4', '<')) {
    echo "<span style='color:red;'>[WARNING] PHP version is older than 7.4. Recommended upgrade to 7.4 or 8.x</span><br>";
} else {
    echo "<span style='color:green;'>[OK] PHP version is sufficient.</span><br>";
}

// 2. MySQLi Extension
echo "<h3>2. MySQLi Extension</h3>";
if (extension_loaded('mysqli')) {
    echo "<span style='color:green;'>[OK] MySQLi extension is loaded.</span><br>";
} else {
    echo "<span style='color:red;'>[CRITICAL] MySQLi extension is NOT loaded. Database connection will fail.</span><br>";
}

// 3. Database Connection Check
echo "<h3>3. Database Connection (from config/database.php)</h3>";
$config_path = 'config/database.php';

if (file_exists($config_path)) {
    echo "Found config/database.php...<br>";
    
    // Read file content manually to parse constants without including (to avoid immediate die)
    $config_content = file_get_contents($config_path);
    
    // Try to extract constants using simple regex (fallback if include fails)
    preg_match("/define\('DB_HOST',\s*'([^']*)'\)/", $config_content, $m_host);
    preg_match("/define\('DB_USER',\s*'([^']*)'\)/", $config_content, $m_user);
    preg_match("/define\('DB_PASS',\s*'([^']*)'\)/", $config_content, $m_pass);
    preg_match("/define\('DB_NAME',\s*'([^']*)'\)/", $config_content, $m_name);
    
    $host = isset($m_host[1]) ? $m_host[1] : 'unknown';
    $user = isset($m_user[1]) ? $m_user[1] : 'unknown';
    $pass = isset($m_pass[1]) ? $m_pass[1] : '';
    $name = isset($m_name[1]) ? $m_name[1] : 'unknown';
    
    echo "Configured Host: " . $host . "<br>";
    echo "Configured User: " . $user . "<br>";
    echo "Configured DB Name: " . $name . "<br>";
    
    if ($host == 'localhost' && $user == 'root' && $pass == '') {
        echo "<br><span style='color:orange;'>[WARNING] It looks like you are using default localhost settings (root with no password). 
        This usually DOES NOT work on web hosting. You must create a database and user on your hosting panel and update config/database.php</span><br>";
    }
    
    echo "<br><b>Attempting Connection...</b><br>";
    try {
        @$conn = new mysqli($host, $user, $pass, $name);
        
        if ($conn->connect_error) {
            echo "<span style='color:red;'>[ERROR] Connection Failed: " . $conn->connect_error . "</span><br>";
            echo "Please check your hostname, username, password, and database name.<br>";
        } else {
            echo "<span style='color:green;'>[OK] Successfully connected to database!</span><br>";
            $conn->set_charset("utf8mb4");
            
            // 4. Check Tables
            echo "<h3>4. Table Check</h3>";
            $tables = ['admin_users', 'exam_sets', 'questions', 'students', 'student_answers', 'student_exams'];
            $missing_tables = [];
            
            foreach ($tables as $table) {
                $check = $conn->query("SHOW TABLES LIKE '$table'");
                if ($check->num_rows == 0) {
                    $missing_tables[] = $table;
                    echo "<span style='color:red;'>[ERROR] Table '$table' NOT found.</span><br>";
                } else {
                    echo "<span style='color:green;'>[OK] Table '$table' exists.</span><br>";
                    
                    // Specific check for questions table column
                    if ($table == 'questions') {
                         $col_check = $conn->query("SHOW COLUMNS FROM questions LIKE 'question_type'");
                         if ($col_check->num_rows > 0) {
                             echo "<span style='color:green;'>-- [OK] Column 'question_type' exists in 'questions'.</span><br>";
                         } else {
                             echo "<span style='color:red;'>-- [ERROR] Column 'question_type' MISSING in 'questions'. Please run the fix script or re-import database.</span><br>";
                         }
                    }
                }
            }
            
            if (count($missing_tables) > 0) {
                 echo "<br><span style='color:red;'>[CRITICAL] Some tables are missing. Please import your SQL database file (eexam_db.sql) to your hosting database using phpMyAdmin.</span><br>";
            }
        }
    } catch (Exception $e) {
        echo "<span style='color:red;'>[ERROR] Exception: " . $e->getMessage() . "</span><br>";
    }
    
} else {
    echo "<span style='color:red;'>[ERROR] config/database.php NOT found. Please upload it.</span><br>";
}

echo "<hr>";
echo "<p>NOTE: Delete this file (check_server.php) after troubleshooting for security.</p>";
?>
