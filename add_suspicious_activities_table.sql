-- สร้างตารางบันทึกการกระทำที่น่าสงสัย/ทุจริต
-- รันไฟล์นี้ใน phpMyAdmin หรือ MySQL Client

USE eexam_db;

-- ตารางบันทึกการกระทำที่น่าสงสัย/ทุจริต
CREATE TABLE IF NOT EXISTS suspicious_activities (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    student_exam_id INT,
    student_id INT,
    activity_type ENUM('left_page', 'copy_attempt', 'right_click', 'paste_attempt', 'tab_switch', 'other') NOT NULL,
    description TEXT,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_exam_id) REFERENCES student_exams(student_exam_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    INDEX idx_student_exam (student_exam_id),
    INDEX idx_student (student_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- แสดงข้อความสำเร็จ
SELECT 'ตารางถูกสร้างเรียบร้อยแล้ว' AS status;
