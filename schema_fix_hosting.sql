-- ฐานข้อมูล E-Exam System (Full Schema Fix)
-- รวมการแก้ไขทั้งหมด (table admin_users และ column question_type)

-- 1. Create admins/admin_users table (using correct name based on code usage)
-- ในโค้ดมีการใช้ทั้ง admins และ admin_users แต่ดูเหมือนจะมีการเปลี่ยนชื่อตาราง
-- เพื่อความชัวร์ สร้างทั้ง 2 ตาราง หรือเช็คจาก error ที่แจ้ง 'admin_users' NOT found
-- ดังนั้นเราจะสร้าง admin_users ให้เหมือน admins

CREATE TABLE IF NOT EXISTS admin_users (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'teacher', 'staff') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- เพิ่ม admin default ถ้ายังไม่มี
INSERT IGNORE INTO admin_users (username, password, full_name, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 'admin@eexam.com', 'admin');
-- password: password

-- Copy data from admins if exists (optional backup)
-- INSERT IGNORE INTO admin_users SELECT * FROM admins;

-- 2. Update questions table schema
CREATE TABLE IF NOT EXISTS questions (
    question_id INT PRIMARY KEY AUTO_INCREMENT,
    exam_set_id INT,
    question_text TEXT NOT NULL,
    question_image VARCHAR(255),
    choice_a VARCHAR(500),
    choice_b VARCHAR(500),
    choice_c VARCHAR(500),
    choice_d VARCHAR(500),
    correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    points DECIMAL(5,2) DEFAULT 1.00,
    question_type ENUM('multiple_choice', 'subjective') DEFAULT 'multiple_choice', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ถ้าตารางมีอยู่แล้ว ให้เพิ่ม column
DELIMITER //
CREATE PROCEDURE AddQuestionTypeColumn()
BEGIN
    IF NOT EXISTS (
        SELECT * 
        FROM information_schema.COLUMNS 
        WHERE TABLE_NAME = 'questions' 
        AND COLUMN_NAME = 'question_type'
    ) THEN
        ALTER TABLE questions ADD COLUMN question_type ENUM('multiple_choice', 'subjective') DEFAULT 'multiple_choice';
    END IF;
END //
DELIMITER ;
CALL AddQuestionTypeColumn();
DROP PROCEDURE AddQuestionTypeColumn;

-- 3. ตารางอื่นๆ (Other Tables) Ensure they exist

CREATE TABLE IF NOT EXISTS classrooms (
    classroom_id INT PRIMARY KEY AUTO_INCREMENT,
    classroom_name VARCHAR(100) NOT NULL,
    grade_level VARCHAR(50),
    teacher_name VARCHAR(100),
    staff_name VARCHAR(100),
    academic_year VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    student_code VARCHAR(20) UNIQUE NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    classroom_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(classroom_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exam_sets (
    exam_set_id INT PRIMARY KEY AUTO_INCREMENT,
    exam_title VARCHAR(200) NOT NULL,
    exam_description TEXT,
    subject VARCHAR(100),
    total_questions INT DEFAULT 0,
    total_score DECIMAL(5,2) DEFAULT 0,
    duration_minutes INT DEFAULT 60,
    exam_date DATE,
    exam_start_time TIME,
    exam_end_time TIME,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS student_exams (
    student_exam_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    exam_set_id INT,
    exam_variant ENUM('A', 'B', 'C', 'D') NOT NULL,
    start_time DATETIME,
    submit_time DATETIME,
    total_score DECIMAL(5,2) DEFAULT 0,
    percentage DECIMAL(5,2) DEFAULT 0,
    status ENUM('in_progress', 'submitted', 'reset') DEFAULT 'in_progress',
    left_page_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (exam_set_id) REFERENCES exam_sets(exam_set_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS student_answers (
    answer_id INT PRIMARY KEY AUTO_INCREMENT,
    student_exam_id INT,
    question_id INT,
    student_answer TEXT, -- Changed from ENUM to TEXT to support subjective answers
    is_correct TINYINT(1) DEFAULT 0,
    points_earned DECIMAL(5,2) DEFAULT 0,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_exam_id) REFERENCES student_exams(student_exam_id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fix student_answer column type if it's still ENUM
DELIMITER //
CREATE PROCEDURE FixStudentAnswerColumn()
BEGIN
    DECLARE col_type VARCHAR(255);
    SELECT COLUMN_TYPE INTO col_type
    FROM information_schema.COLUMNS 
    WHERE TABLE_NAME = 'student_answers' 
    AND COLUMN_NAME = 'student_answer';
    
    IF col_type LIKE 'enum%' THEN
        ALTER TABLE student_answers MODIFY COLUMN student_answer TEXT;
    END IF;
END //
DELIMITER ;
CALL FixStudentAnswerColumn();
DROP PROCEDURE FixStudentAnswerColumn;

CREATE TABLE IF NOT EXISTS system_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO system_settings (setting_key, setting_value) VALUES 
('system_name', 'ระบบ E-Exam | การสอบออนไลน์'),
('allow_exam', '1'),
('current_exam_date', NULL),
('auto_submit_on_time', '1');

CREATE TABLE IF NOT EXISTS suspicious_activities (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    student_exam_id INT,
    student_id INT,
    activity_type ENUM('left_page', 'copy_attempt', 'right_click', 'paste_attempt', 'tab_switch', 'other', 'print_screen') NOT NULL,
    description TEXT,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_exam_id) REFERENCES student_exams(student_exam_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
