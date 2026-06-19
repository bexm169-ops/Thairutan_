# BKK Welfare Matcher — คู่มือติดตั้ง

## วิธีติดตั้ง

### 1. วางโฟลเดอร์
คัดลอกโฟลเดอร์ `bkk_welfare_matcher` ทั้งหมดไปไว้ที่:
```
C:\AppServ\www\bkk_welfare_matcher\
```

### 2. นำเข้าฐานข้อมูล
1. เปิด phpMyAdmin: http://localhost/phpMyAdmin/
2. สร้างฐานข้อมูลใหม่ชื่อ `bkk_welfare_matcher` (Collation: utf8mb4_unicode_ci)
3. คลิก Import → เลือกไฟล์ `bkk_welfare_matcher.sql` → Go

### 3. ตั้งค่าการเชื่อมต่อฐานข้อมูล
แก้ไขไฟล์ `includes/db.php`:
```php
define('DB_USER', 'root');   // ชื่อ user AppServ ของคุณ
define('DB_PASS', '');       // รหัสผ่านที่ตั้งตอนติดตั้ง AppServ
```

### 4. แก้ Password ผู้ใช้ทดสอบ
ข้อมูลตัวอย่างใน SQL ใช้ password_hash ที่เป็น placeholder ใช้งานจริงไม่ได้
ต้องรันไฟล์นี้เพื่อสร้าง hash ใหม่:
```
http://localhost/bkk_welfare_matcher/setup_test_passwords.php
```
แล้วลบไฟล์นั้นทิ้งหลังใช้งาน

### 5. เปิดเว็บ
```
http://localhost/bkk_welfare_matcher/
```

## โครงสร้างไฟล์
```
bkk_welfare_matcher/
├── index.php          ← หน้าแรก
├── login.php          ← เข้าสู่ระบบ
├── register.php       ← ลงทะเบียน
├── logout.php         ← ออกจากระบบ
├── welfare.php        ← สวัสดิการทั้งหมด
├── my-rights.php      ← สิทธิ์ของฉัน (ต้องล็อกอิน)
├── rights-detail.php  ← รายละเอียดสิทธิ์
├── about.php          ← เกี่ยวกับเรา
├── api/
│   ├── get_stats.php         ← สถิติหน้าแรก
│   ├── get_welfare.php       ← รายการสวัสดิการ
│   ├── get_welfare_detail.php← รายละเอียดสวัสดิการ
│   └── get_my_rights.php     ← สิทธิ์ของผู้ใช้
├── includes/
│   ├── db.php         ← การเชื่อมต่อ DB (แก้ password ที่นี่)
│   ├── auth.php       ← ฟังก์ชัน Session/Login
│   └── header.php     ← Header ที่ใช้ร่วมกันทุกหน้า
├── file_css/          ← ไฟล์ CSS แยกตามหน้า
├── js/
│   └── main.js        ← JavaScript ดึง API
└── bkk_welfare_matcher.sql ← โครงสร้างฐานข้อมูล
```
