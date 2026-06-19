<?php
/* ==========================================================================
   [includes/db.php]
   รองรับทั้ง Local (AppServ/XAMPP) และ Railway (production)
   - ถ้ารันบน Railway: อ่านค่าจาก Environment Variables ที่ Railway สร้างให้
     อัตโนมัติเมื่อเชื่อม MySQL plugin (MYSQLHOST, MYSQLPORT, MYSQLUSER,
     MYSQLPASSWORD, MYSQLDATABASE)
   - ถ้ารันบนเครื่อง local: ใช้ค่า default ด้านล่าง (AppServ root, no password)
   ========================================================================== */

// อ่านจาก Environment Variable ก่อน ถ้าไม่มีค่อย fallback เป็น local
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');           // local: ใส่รหัสผ่าน root AppServ ถ้ามี
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'bkk_welfare_matcher');
define('DB_CHARSET', 'utf8mb4');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        );
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'DB Error: ' . $e->getMessage()));
            exit;
        }
    }
    return $pdo;
}
