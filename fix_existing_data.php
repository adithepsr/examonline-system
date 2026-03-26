<?php
/**
 * สคริปต์สำหรับแก้ไขข้อมูลเก่าที่ถูก htmlspecialchars ครอบไว้ในฐานข้อมูล
 * ให้กลับมาเป็นเครื่องหมายปกติเพื่อให้ระบบใหม่แสดงผลได้ถูกต้อง
 */
require_once('config/database.php');

header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['admin_id'])) {
    die("กรุณาเข้าสู่ระบบในฐานะ Admin ก่อนรันไฟล์นี้");
}

echo "<h2>🔧 ระบบแก้ไขข้อมูลเครื่องหมายพิเศษ (Data Recovery)</h2>";
echo "<p>กำลังตรวจสอบข้อมูลในตาราง questions และ student_answers...</p>";

$tables_columns = [
    'questions' => ['question_text', 'choice_a', 'choice_b', 'choice_c', 'choice_d'],
    'student_answers' => ['student_answer']
];

$total_fixed = 0;

foreach ($tables_columns as $table => $columns) {
    $id_col = ($table == 'questions') ? 'question_id' : 'answer_id';
    
    foreach ($columns as $column) {
        $query = "SELECT $id_col, $column FROM $table WHERE $column LIKE '%&%'";
        $res = $conn->query($query);
        
        while ($row = $res->fetch_assoc()) {
            $old_val = $row[$column];
            $new_val = htmlspecialchars_decode($old_val, ENT_QUOTES);
            
            if ($old_val !== $new_val) {
                $stmt = $conn->prepare("UPDATE $table SET $column = ? WHERE $id_col = ?");
                $stmt->bind_param("si", $new_val, $row[$id_col]);
                if ($stmt->execute()) {
                    echo "<div style='color: green;'>✅ อัปเดต $table [$id_col: {$row[$id_col]}] คอลัมน์ $column สำเร็จ</div>";
                    $total_fixed++;
                }
            }
        }
    }
}

echo "<hr>";
echo "<h3>🏁 ประมวลผลเสร็จสิ้น!</h3>";
echo "<p>แก้ไขข้อมูลไปทั้งหมด <strong>$total_fixed</strong> รายการ</p>";
echo "<p style='color: red;'>⚠️ สำคัญ: โปรดลบไฟล์นี้ออกจากเซิร์ฟเวอร์หลังจากใช้งานเสร็จสิ้น</p>";
echo "<a href='admin/questions.php' style='display: inline-block; padding: 10px 20px; background: #F26522; color: white; text-decoration: none; border-radius: 5px;'>กลับหน้าจัดการข้อสอบ</a>";
?>
