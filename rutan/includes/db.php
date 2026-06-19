<?php
/* [includes/db.php] - PHP 5.6 compatible */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '12345678');        // ใส่รหัสผ่าน root AppServ ของคุณ
define('DB_NAME', 'bkk_welfare_matcher');
define('DB_CHARSET', 'utf8mb4');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
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
