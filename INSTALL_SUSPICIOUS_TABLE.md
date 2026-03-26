# วิธีการติดตั้งตารางบันทึกการกระทำที่น่าสงสัย

## วิธีที่ 1: ใช้ phpMyAdmin (แนะนำ)

1. เปิด phpMyAdmin (http://localhost/phpmyadmin)
2. เลือกฐานข้อมูล `eexam_db`
3. คลิกแท็บ "SQL"
4. คัดลอกโค้ดด้านล่างแล้ววาง:

```sql
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
```

5. คลิก "Go" หรือ "ดำเนินการ"
6. รอจนเห็นข้อความ "Query OK" หรือ "สำเร็จ"

---

## วิธีที่ 2: ใช้ Command Line

```bash
# เปิด Command Prompt หรือ Terminal
cd C:\xampp\mysql\bin

# รันคำสั่ง
mysql -u root -p eexam_db < C:\xampp\htdocs\exam\add_suspicious_activities_table.sql
```

---

## วิธีที่ 3: Import ไฟล์ SQL

1. เปิด phpMyAdmin
2. เลือกฐานข้อมูล `eexam_db`
3. คลิกแท็บ "Import"
4. คลิก "Choose File"
5. เลือกไฟล์ `add_suspicious_activities_table.sql`
6. คลิก "Go"

---

## ตรวจสอบว่าสร้างสำเร็จ

รันคำสั่ง SQL นี้:

```sql
SHOW TABLES LIKE 'suspicious_activities';
```

ถ้าเห็นตาราง `suspicious_activities` แสดงว่าสำเร็จ

---

## โครงสร้างตาราง

| ฟิลด์ | ชนิด | คำอธิบาย |
|-------|------|----------|
| activity_id | INT | รหัสการกระทำ (Auto Increment) |
| student_exam_id | INT | รหัสการสอบ |
| student_id | INT | รหัสนักเรียน |
| activity_type | ENUM | ประเภทการกระทำ |
| description | TEXT | รายละเอียด |
| severity | ENUM | ระดับความรุนแรง |
| ip_address | VARCHAR(45) | IP Address |
| user_agent | TEXT | Browser/Device |
| created_at | TIMESTAMP | เวลาที่บันทึก |

---

## ประเภทการกระทำ (activity_type)

- `left_page` - ออกจากหน้าจอ
- `copy_attempt` - พยายามคัดลอก
- `right_click` - คลิกขวา
- `paste_attempt` - พยายามวาง
- `tab_switch` - สลับแท็บ
- `other` - อื่นๆ

---

## ระดับความรุนแรง (severity)

- `critical` - วิกฤต (ออกจากหน้าจอ)
- `high` - สูง (คัดลอก)
- `medium` - ปานกลาง (คลิกขวา, วาง)
- `low` - ต่ำ (อื่นๆ)

---

## หลังจากสร้างตารางแล้ว

1. Refresh หน้า `admin/suspicious_activities.php`
2. ควรเห็นหน้ารายงานปกติ (ยังไม่มีข้อมูล)
3. ทดสอบโดยให้นักเรียนเข้าสอบและทำการกระทำที่น่าสงสัย
4. ข้อมูลจะถูกบันทึกอัตโนมัติ

---

**สร้างโดย:** AI Assistant  
**วันที่:** 28 ธันวาคม 2568  
**เวลา:** 15:41 น.
