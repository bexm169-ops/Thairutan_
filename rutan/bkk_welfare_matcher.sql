-- phpMyAdmin SQL Dump
-- version 4.6.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 18, 2026 at 11:59 AM
-- Server version: 5.7.12-log
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bkk_welfare_matcher`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `program_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='โครงการที่ผู้ใช้บุ๊กมาร์กไว้';

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = login ล้มเหลว',
  `national_id_tried` char(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รองรับ IPv6',
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `status` enum('success','fail_password','fail_not_found','locked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fail_not_found',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='บันทึกการเข้าสู่ระบบทุกครั้ง';

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `national_id` char(13) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'เลขบัตรประชาชน 13 หลัก',
  `laser_code` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'เลขหลังบัตร เช่น TH1-1234567-89',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'bcrypt hash ของ laser_code ตอน register',
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthdate` date NOT NULL COMMENT 'วันเดือนปีเกิด',
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT 'other',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เขตใน กทม.',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=ยังไม่ยืนยัน, 1=ยืนยันแล้ว',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ผู้ใช้งานระบบ / ผู้รับสวัสดิการ';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `national_id`, `laser_code`, `password_hash`, `first_name`, `last_name`, `birthdate`, `gender`, `phone`, `email`, `address`, `district`, `is_verified`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '1100100000001', 'TH1-1234567-', '$2y$12$exampleHashForSomchaiXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'สมชาย', 'ใจดี', '1959-03-15', 'male', '081-000-0001', NULL, NULL, 'ลาดกระบัง', 1, 1, '2026-06-18 11:56:18', '2026-06-18 11:56:18'),
(2, '1100100000002', 'TH2-9876543-', '$2y$12$exampleHashForSomyingXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'สมหญิง', 'รักไทย', '1985-07-22', 'female', '082-000-0002', NULL, NULL, 'บางรัก', 1, 1, '2026-06-18 11:56:18', '2026-06-18 11:56:18'),
(3, '1100100000003', 'TH3-5551234-', '$2y$12$exampleHashForSomsakXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'สมศักดิ์', 'มีสุข', '2001-11-30', 'male', '083-000-0003', NULL, NULL, 'พระนคร', 0, 1, '2026-06-18 11:56:18', '2026-06-18 11:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `user_rights`
--

CREATE TABLE `user_rights` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `program_id` int(10) UNSIGNED NOT NULL,
  `status` enum('active','pending','rejected','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'active=ใช้งานอยู่, pending=รอลงทะเบียน',
  `registered_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='สิทธิ์สวัสดิการของผู้ใช้แต่ละคน';

--
-- Dumping data for table `user_rights`
--

INSERT INTO `user_rights` (`id`, `user_id`, `program_id`, `status`, `registered_at`, `approved_at`, `note`, `created_at`) VALUES
(1, 1, 1, 'active', '2024-01-10 09:00:00', '2024-01-15 10:30:00', NULL, '2026-06-18 11:56:18'),
(2, 1, 15, 'active', '2024-02-01 09:00:00', '2024-02-05 14:00:00', NULL, '2026-06-18 11:56:18'),
(3, 1, 2, 'pending', NULL, NULL, NULL, '2026-06-18 11:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `welfare_criteria`
--

CREATE TABLE `welfare_criteria` (
  `id` int(10) UNSIGNED NOT NULL,
  `program_id` int(10) UNSIGNED NOT NULL,
  `criteria_text` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ข้อความเงื่อนไข 1 ข้อ',
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='เงื่อนไขคุณสมบัติผู้มีสิทธิ์';

--
-- Dumping data for table `welfare_criteria`
--

INSERT INTO `welfare_criteria` (`id`, `program_id`, `criteria_text`, `sort_order`) VALUES
(1, 1, 'มีอายุ 60 ปีบริบูรณ์ขึ้นไป', 1),
(2, 1, 'มีชื่ออยู่ในทะเบียนบ้านในเขตกรุงเทพมหานคร', 2),
(3, 1, 'ไม่เป็นผู้รับเงินบำนาญหรือสวัสดิการอื่นจากรัฐ', 3),
(4, 2, 'ผู้ที่มีภาวะพึ่งพิง (ติดบ้าน หรือ ติดเตียง)', 1),
(5, 2, 'ผู้ที่มีภาวะปัญหากลั้นปัสสาวะหรืออุจจาระไม่ได้', 2),
(6, 2, 'มีชื่ออยู่ในทะเบียนบ้านในเขตกรุงเทพมหานคร', 3),
(7, 6, 'เด็กแรกเกิดอายุ 0 – 6 ปี', 1),
(8, 6, 'ครอบครัวมีรายได้เฉลี่ยไม่เกิน 100,000 บาท/คน/ปี', 2),
(9, 6, 'มีชื่ออยู่ในทะเบียนบ้านในเขตกรุงเทพมหานคร', 3);

-- --------------------------------------------------------

--
-- Table structure for table `welfare_documents`
--

CREATE TABLE `welfare_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `program_id` int(10) UNSIGNED NOT NULL,
  `document_text` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อ/คำอธิบายเอกสาร',
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='เอกสารที่ต้องเตรียมต่อโครงการ';

--
-- Dumping data for table `welfare_documents`
--

INSERT INTO `welfare_documents` (`id`, `program_id`, `document_text`, `sort_order`) VALUES
(1, 1, 'บัตรประจำตัวประชาชน', 1),
(2, 1, 'สำเนาทะเบียนบ้าน', 2),
(3, 1, 'สมุดบัญชีธนาคาร (หน้าแรก)', 3),
(4, 2, 'สำเนาบัตรประจำตัวประชาชนของผู้ป่วย', 1),
(5, 2, 'สำเนาทะเบียนบ้านของผู้ป่วย', 2),
(6, 2, 'ใบรับรองแพทย์ หรือ ใบประเมินความสามารถ (ADL)', 3),
(7, 6, 'สูติบัตรของเด็ก', 1),
(8, 6, 'บัตรประชาชนของผู้ปกครอง', 2),
(9, 6, 'สมุดบัญชีธนาคารของผู้ปกครอง', 3),
(10, 6, 'หนังสือรับรองรายได้ของครอบครัว', 4);

-- --------------------------------------------------------

--
-- Table structure for table `welfare_programs`
--

CREATE TABLE `welfare_programs` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อโครงการ',
  `category` enum('elderly','child','health','disabled','education','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `category_label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ป้ายหมวดหมู่ภาษาไทย',
  `agency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'หน่วยงานที่รับผิดชอบ',
  `description` text COLLATE utf8mb4_unicode_ci,
  `benefit_value` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'มูลค่า เช่น ฿600/เดือน',
  `benefit_note` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='รายการโครงการสวัสดิการทั้งหมด';

--
-- Dumping data for table `welfare_programs`
--

INSERT INTO `welfare_programs` (`id`, `title`, `category`, `category_label`, `agency`, `description`, `benefit_value`, `benefit_note`, `is_active`, `created_at`) VALUES
(1, 'เบี้ยยังชีพผู้สูงอายุ', 'elderly', 'ผู้สูงอายุ', 'กทม. / กรมส่งเสริมการปกครองท้องถิ่น', NULL, '฿600 – ฿1,000 / เดือน', NULL, 1, '2026-06-18 11:56:18'),
(2, 'ผ้าอ้อมผู้ใหญ่ / แผ่นรองซับ', 'elderly', 'ผู้สูงอายุ', 'สปสช. ร่วมกับ กทม.', NULL, 'ฟรี ไม่เกิน 3 ชิ้น / วัน', NULL, 1, '2026-06-18 11:56:18'),
(3, 'บริการดูแลผู้สูงอายุที่บ้าน (Home Care)', 'elderly', 'ผู้สูงอายุ', 'กองการพยาบาล กทม.', NULL, 'ฟรี', NULL, 1, '2026-06-18 11:56:18'),
(4, 'กายอุปกรณ์สำหรับผู้สูงอายุ', 'elderly', 'ผู้สูงอายุ', 'สปสช.', NULL, 'ตามประเภทอุปกรณ์', NULL, 1, '2026-06-18 11:56:18'),
(5, 'ศูนย์ดูแลผู้สูงอายุกลางวัน (Day Care)', 'elderly', 'ผู้สูงอายุ', 'กทม.', NULL, 'ฟรี / อัตราพิเศษ', NULL, 1, '2026-06-18 11:56:18'),
(6, 'เงินอุดหนุนเด็กแรกเกิด', 'child', 'เด็กแรกเกิด', 'กรมกิจการเด็กและเยาวชน', NULL, '฿600 / เดือน', NULL, 1, '2026-06-18 11:56:18'),
(7, 'สมุดสุขภาพแม่และเด็ก', 'child', 'เด็กแรกเกิด', 'กระทรวงสาธารณสุข', NULL, 'ฟรี', NULL, 1, '2026-06-18 11:56:18'),
(8, 'วัคซีนพื้นฐานเด็กแรกเกิด', 'child', 'เด็กแรกเกิด', 'กทม. / สำนักอนามัย', NULL, 'ฟรี', NULL, 1, '2026-06-18 11:56:18'),
(9, 'นมโรงเรียน / อาหารกลางวันฟรี', 'child', 'เด็กแรกเกิด', 'กทม. / กระทรวงศึกษาธิการ', NULL, 'ฟรี', NULL, 1, '2026-06-18 11:56:18'),
(10, 'ตรวจสุขภาพฟรี 14 รายการ', 'health', 'สุขภาพ', 'ศูนย์บริการสาธารณสุข กทม.', NULL, 'ฟรี', NULL, 1, '2026-06-18 11:56:18'),
(11, 'บัตรทอง (30 บาทรักษาทุกโรค)', 'health', 'สุขภาพ', 'สปสช.', NULL, '฿30 ต่อครั้ง (บางกรณีฟรี)', NULL, 1, '2026-06-18 11:56:18'),
(12, 'ยาและเวชภัณฑ์ฟรีสำหรับผู้มีรายได้น้อย', 'health', 'สุขภาพ', 'สำนักอนามัย กทม.', NULL, 'ฟรี', NULL, 1, '2026-06-18 11:56:18'),
(13, 'โครงการฟันเทียมพระราชทาน', 'health', 'สุขภาพ', 'ทันตแพทยสภา / สปสช.', NULL, 'ฟรี', NULL, 1, '2026-06-18 11:56:18'),
(14, 'กายภาพบำบัดชุมชน', 'health', 'สุขภาพ', 'โรงพยาบาลในสังกัด กทม.', NULL, 'ฟรี', NULL, 1, '2026-06-18 11:56:18'),
(15, 'เบี้ยความพิการ', 'disabled', 'คนพิการ', 'กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ', NULL, '฿800 / เดือน', NULL, 1, '2026-06-18 11:56:18'),
(16, 'สิทธิลดหย่อนค่าโดยสาร BTS / MRT', 'disabled', 'คนพิการ', 'กทม. / รฟม.', NULL, 'ลด 50%', NULL, 1, '2026-06-18 11:56:18'),
(17, 'กายอุปกรณ์คนพิการ (ขาเทียม / รถเข็น)', 'disabled', 'คนพิการ', 'สปสช. / กรมการแพทย์', NULL, 'ฟรี ตามเงื่อนไข', NULL, 1, '2026-06-18 11:56:18'),
(18, 'กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ', 'disabled', 'คนพิการ', 'กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ', NULL, 'กู้ยืมดอกเบี้ย 0%', NULL, 1, '2026-06-18 11:56:18'),
(19, 'ทุนการศึกษาเด็กยากจน กทม.', 'education', 'ทุนการศึกษา', 'สำนักการศึกษา กทม.', NULL, NULL, NULL, 1, '2026-06-18 11:56:18'),
(20, 'มาตรการช่วยเหลือค่าน้ำ-ไฟ ผู้มีรายได้น้อย', 'other', 'อื่น ๆ', 'การประปา / การไฟฟ้า', NULL, NULL, NULL, 1, '2026-06-18 11:56:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bookmark` (`user_id`,`program_id`),
  ADD KEY `fk_bm_program` (`program_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_user` (`user_id`),
  ADD KEY `idx_log_ip` (`ip_address`),
  ADD KEY `idx_log_created` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_national_id` (`national_id`),
  ADD KEY `idx_birthdate` (`birthdate`);

--
-- Indexes for table `user_rights`
--
ALTER TABLE `user_rights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_program` (`user_id`,`program_id`),
  ADD KEY `fk_ur_program` (`program_id`);

--
-- Indexes for table `welfare_criteria`
--
ALTER TABLE `welfare_criteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_criteria_program` (`program_id`);

--
-- Indexes for table `welfare_documents`
--
ALTER TABLE `welfare_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doc_program` (`program_id`);

--
-- Indexes for table `welfare_programs`
--
ALTER TABLE `welfare_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_active` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_rights`
--
ALTER TABLE `user_rights`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `welfare_criteria`
--
ALTER TABLE `welfare_criteria`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `welfare_documents`
--
ALTER TABLE `welfare_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `welfare_programs`
--
ALTER TABLE `welfare_programs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `fk_bm_program` FOREIGN KEY (`program_id`) REFERENCES `welfare_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_rights`
--
ALTER TABLE `user_rights`
  ADD CONSTRAINT `fk_ur_program` FOREIGN KEY (`program_id`) REFERENCES `welfare_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `welfare_criteria`
--
ALTER TABLE `welfare_criteria`
  ADD CONSTRAINT `fk_criteria_program` FOREIGN KEY (`program_id`) REFERENCES `welfare_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `welfare_documents`
--
ALTER TABLE `welfare_documents`
  ADD CONSTRAINT `fk_doc_program` FOREIGN KEY (`program_id`) REFERENCES `welfare_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Table structure for table `appeals`
-- (เพิ่มเข้ามาเพื่อรองรับฟอร์ม "แจ้งสิทธิ์ตกหล่น" ในหน้า about.php)
--

CREATE TABLE `appeals` (
  `id` int(10) UNSIGNED NOT NULL,
  `national_id` char(13) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','reviewing','resolved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='คำร้องแจ้งสิทธิ์สวัสดิการตกหล่น';

ALTER TABLE `appeals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_national_id` (`national_id`),
  ADD KEY `idx_status` (`status`);

ALTER TABLE `appeals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
