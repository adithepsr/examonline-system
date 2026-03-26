-- ฐานข้อมูล E-Exam System
-- สร้างโดย: อ.อดิเทพ ศรีมันตะ (อ.มนต์)
-- ปรับปรุงล่าสุด: 26 กุมภาพันธ์ 2569

-- CREATE DATABASE IF NOT EXISTS testbank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE testbank;

-- ตาราง Admin
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    admin_role ENUM('admin', 'teacher') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางห้องเรียน
CREATE TABLE IF NOT EXISTS classrooms (
    classroom_id INT PRIMARY KEY AUTO_INCREMENT,
    classroom_name VARCHAR(100) NOT NULL,
    grade_level VARCHAR(50),
    teacher_name VARCHAR(100),
    staff_name VARCHAR(100),
    academic_year VARCHAR(20),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(admin_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางนักเรียน
CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    student_code VARCHAR(20) UNIQUE NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    classroom_id INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(classroom_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES admins(admin_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางชุดข้อสอบ
CREATE TABLE IF NOT EXISTS exam_sets (
    exam_set_id INT PRIMARY KEY AUTO_INCREMENT,
    exam_title VARCHAR(200) NOT NULL,
    exam_description TEXT,
    subject VARCHAR(100),
    total_questions INT DEFAULT 0,
    total_score DECIMAL(10,2) DEFAULT 0,
    duration_minutes INT DEFAULT 60,
    exam_date DATE,
    exam_start_time TIME,
    exam_end_time TIME,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(admin_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางความสัมพันธ์ชุดข้อสอบกับห้องเรียน
CREATE TABLE IF NOT EXISTS exam_classroom_access (
    access_id INT PRIMARY KEY AUTO_INCREMENT,
    exam_set_id INT,
    classroom_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_set_id) REFERENCES exam_sets(exam_set_id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(classroom_id) ON DELETE CASCADE,
    UNIQUE KEY unique_access (exam_set_id, classroom_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางข้อสอบ
CREATE TABLE IF NOT EXISTS questions (
    question_id INT PRIMARY KEY AUTO_INCREMENT,
    exam_set_id INT,
    question_type ENUM('multiple_choice', 'subjective') DEFAULT 'multiple_choice',
    question_text TEXT NOT NULL,
    question_image VARCHAR(255),
    choice_a VARCHAR(500),
    choice_b VARCHAR(500),
    choice_c VARCHAR(500),
    choice_d VARCHAR(500),
    correct_answer ENUM('A', 'B', 'C', 'D'),
    points DECIMAL(10,2) DEFAULT 1.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_set_id) REFERENCES exam_sets(exam_set_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางการสอบของนักเรียน
CREATE TABLE IF NOT EXISTS student_exams (
    student_exam_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    exam_set_id INT,
    exam_variant ENUM('A', 'B', 'C', 'D') DEFAULT 'A',
    start_time DATETIME,
    submit_time DATETIME,
    total_score DECIMAL(10,2) DEFAULT 0,
    percentage DECIMAL(5,2) DEFAULT 0,
    status ENUM('in_progress', 'submitted', 'reset') DEFAULT 'in_progress',
    left_page_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (exam_set_id) REFERENCES exam_sets(exam_set_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางคำตอบของนักเรียน
CREATE TABLE IF NOT EXISTS student_answers (
    answer_id INT PRIMARY KEY AUTO_INCREMENT,
    student_exam_id INT,
    question_id INT,
    student_answer TEXT,
    is_correct TINYINT(1) DEFAULT 0,
    points_earned DECIMAL(10,2) DEFAULT 0,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_exam_id) REFERENCES student_exams(student_exam_id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE,
    UNIQUE KEY unique_answer (student_exam_id, question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางการตั้งค่าระบบ
CREATE TABLE IF NOT EXISTS system_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางสถิติการสอบ
CREATE TABLE IF NOT EXISTS exam_statistics (
    stat_id INT PRIMARY KEY AUTO_INCREMENT,
    exam_set_id INT,
    classroom_id INT,
    total_students INT DEFAULT 0,
    mean_score DECIMAL(10,2) DEFAULT 0,
    sd_score DECIMAL(10,2) DEFAULT 0,
    max_score DECIMAL(10,2) DEFAULT 0,
    min_score DECIMAL(10,2) DEFAULT 0,
    p_value DECIMAL(10,4) DEFAULT 0,
    r_value DECIMAL(10,4) DEFAULT 0,
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_set_id) REFERENCES exam_sets(exam_set_id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(classroom_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- เพิ่มข้อมูล Admin เริ่มต้น (username: admin, password: admin123)
INSERT INTO admins (username, password, full_name, email, admin_role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 'admin@eexam.com', 'admin');

-- เพิ่มการตั้งค่าระบบเริ่มต้น
INSERT INTO system_settings (setting_key, setting_value) VALUES 
('system_name', 'ระบบ E-Exam | การสอบออนไลน์'),
('allow_exam', '1'),
('current_exam_date', NULL),
('auto_submit_on_time', '1');
