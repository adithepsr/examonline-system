<?php
require_once('config/database.php');

$new_settings = [
    'school_name' => 'ชื่อโรงเรียนของคุณ',
    'school_copyright' => 'สงวนลิขสิทธิ์โดยชื่อโรงเรียนของคุณ',
    'school_logo' => ''
];

foreach ($new_settings as $key => $value) {
    $check = $conn->query("SELECT setting_id FROM system_settings WHERE setting_key = '$key'");
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->bind_param("ss", $key, $value);
        $stmt->execute();
        echo "Added $key<br>";
    } else {
        echo "$key already exists<br>";
    }
}
echo "Done.";
?>
