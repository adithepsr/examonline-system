<?php
/**
 * Debug Script - ตรวจสอบข้อมูลชุดข้อสอบและสถานะการสอบของนักเรียน
 */

session_start();
require_once('config/database.php');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['student_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

$student_id = $_SESSION['student_id'];

// ดึงข้อมูลนักเรียน
$query = "SELECT s.*, c.classroom_name 
          FROM students s 
          LEFT JOIN classrooms c ON s.classroom_id = c.classroom_id 
          WHERE s.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

echo "<h2>ข้อมูลนักเรียน</h2>";
echo "<pre>";
print_r($student);
echo "</pre>";

// ดึงชุดข้อสอบทั้งหมดที่เปิดใช้งาน
echo "<h2>ชุดข้อสอบทั้งหมดที่เปิดใช้งาน (is_active = 1)</h2>";
$query = "SELECT * FROM exam_sets WHERE is_active = 1 ORDER BY created_at DESC";
$result = $conn->query($query);
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo "\n---\n";
}
echo "</pre>";

// ดึงประวัติการสอบของนักเรียน
echo "<h2>ประวัติการสอบของนักเรียน</h2>";
$query = "SELECT se.*, es.exam_title 
          FROM student_exams se 
          JOIN exam_sets es ON se.exam_set_id = es.exam_set_id 
          WHERE se.student_id = ? 
          ORDER BY se.start_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo "\n---\n";
}
echo "</pre>";

// ทดสอบ Query แบบที่ใช้ใน exam.php (มี classroom_access)
echo "<h2>ทดสอบ Query แบบที่ใช้ใน exam.php (มี exam_classroom_access)</h2>";
try {
    $query = "SELECT es.* FROM exam_sets es
              JOIN exam_classroom_access eca ON es.exam_set_id = eca.exam_set_id
              WHERE es.is_active = 1 
                  AND eca.classroom_id = ?
                  AND NOT EXISTS (
                      SELECT 1 FROM student_exams se 
                      WHERE se.exam_set_id = es.exam_set_id 
                          AND se.student_id = ? 
                          AND se.status = 'submitted'
                  )
              ORDER BY es.created_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $student['classroom_id'], $student_id);
    $stmt->execute();
    $exam_set = $stmt->get_result()->fetch_assoc();
    
    echo "<pre>";
    if ($exam_set) {
        echo "✅ พบชุดข้อสอบ:\n";
        print_r($exam_set);
    } else {
        echo "❌ ไม่พบชุดข้อสอบ (อาจไม่มี exam_classroom_access หรือไม่ตรงเงื่อนไข)\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "<pre>❌ Error: " . $e->getMessage() . "</pre>";
}

// ทดสอบ Query แบบ Fallback (ไม่เช็ค classroom)
echo "<h2>ทดสอบ Query แบบ Fallback (ไม่เช็ค classroom)</h2>";
$query = "SELECT es.* FROM exam_sets es
          WHERE es.is_active = 1 
              AND NOT EXISTS (
                  SELECT 1 FROM student_exams se 
                  WHERE se.exam_set_id = es.exam_set_id 
                      AND se.student_id = ? 
                      AND se.status = 'submitted'
              )
          ORDER BY es.created_at DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$exam_set = $stmt->get_result()->fetch_assoc();

echo "<pre>";
if ($exam_set) {
    echo "✅ พบชุดข้อสอบ:\n";
    print_r($exam_set);
} else {
    echo "❌ ไม่พบชุดข้อสอบ (นักเรียนอาจสอบทุกชุดที่เปิดใช้งานแล้ว)\n";
}
echo "</pre>";

// ตรวจสอบตาราง exam_classroom_access
echo "<h2>ตรวจสอบตาราง exam_classroom_access</h2>";
$query = "SELECT eca.*, es.exam_title, c.classroom_name 
          FROM exam_classroom_access eca
          JOIN exam_sets es ON eca.exam_set_id = es.exam_set_id
          LEFT JOIN classrooms c ON eca.classroom_id = c.classroom_id
          WHERE es.is_active = 1
          ORDER BY eca.exam_set_id DESC";
$result = $conn->query($query);
echo "<pre>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
        echo "\n---\n";
    }
} else {
    echo "❌ ไม่มีข้อมูลใน exam_classroom_access\n";
}
echo "</pre>";
?>
