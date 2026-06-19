<?php
/* ==========================================================================
   [setup_test_passwords.php]
   รันครั้งเดียวเพื่อสร้าง bcrypt hash ที่ถูกต้องสำหรับผู้ใช้ทดสอบ
   *** ลบไฟล์นี้ทิ้งหลังจากรันแล้ว ***
   ========================================================================== */
require_once 'includes/db.php';

$testUsers = array(
    array('national_id' => '1100100000001', 'laser_code' => 'TH1-1234567-89'),
    array('national_id' => '1100100000002', 'laser_code' => 'TH2-9876543-21'),
    array('national_id' => '1100100000003', 'laser_code' => 'TH3-5551234-56'),
);

$pdo = getDB();
$updated = 0;
foreach ($testUsers as $u) {
    $hash = password_hash($u['laser_code'], PASSWORD_BCRYPT, array('cost' => 12));
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, laser_code = ? WHERE national_id = ?");
    $stmt->execute(array($hash, $u['laser_code'], $u['national_id']));
    $updated += $stmt->rowCount();
}

echo "<h2 style='font-family:sans-serif'>อัปเดตรหัสผ่านสำเร็จ: " . $updated . " บัญชี</h2>";
echo "<p style='font-family:sans-serif'>ข้อมูลทดสอบ:</p><ul style='font-family:monospace'>";
foreach ($testUsers as $u) {
    echo "<li>เลขบัตร: <b>" . $u['national_id'] . "</b> | Laser Code: <b>" . $u['laser_code'] . "</b></li>";
}
echo "</ul>";
echo "<p style='color:red;font-family:sans-serif'><b>กรุณาลบไฟล์นี้ออกหลังจากรันแล้ว!</b></p>";
echo "<p style='font-family:sans-serif'><a href='login.php'>ไปหน้าล็อกอิน</a></p>";
