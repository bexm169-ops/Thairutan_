<?php
/* [api/get_my_rights.php] - PHP 5.6 compatible */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$base = dirname(__DIR__);
require_once $base . '/includes/db.php';
require_once $base . '/includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(array('success' => false, 'message' => 'กรุณาเข้าสู่ระบบ'));
    exit;
}

$pdo    = getDB();
$userId = (int)$_SESSION['user_id'];

$uStmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$uStmt->execute(array($userId));
$userData = $uStmt->fetch();

$stmt = $pdo->prepare("
    SELECT ur.status, ur.program_id,
           wp.title, wp.category_label, wp.benefit_value, wp.description
    FROM user_rights ur
    JOIN welfare_programs wp ON ur.program_id = wp.id
    WHERE ur.user_id = ?
    ORDER BY ur.status DESC, ur.created_at DESC
");
$stmt->execute(array($userId));
$rights = $stmt->fetchAll();

$active  = array();
$pending = array();
foreach ($rights as $r) {
    if ($r['status'] === 'active')  $active[]  = $r;
    if ($r['status'] === 'pending') $pending[] = $r;
}

$total = 0;
foreach ($active as $r) {
    if (preg_match('/฿([\d,]+)/', $r['benefit_value'], $m)) {
        $total += (int)str_replace(',', '', $m[1]);
    }
}
$totalStr = $total > 0 ? '฿' . number_format($total) . '/เดือน' : 'ดูรายละเอียด';

echo json_encode(array(
    'success'     => true,
    'user'        => array(
        'id'          => $userData['id'],
        'first_name'  => $userData['first_name'],
        'last_name'   => $userData['last_name'],
        'national_id' => $userData['national_id'],
        'is_verified' => $userData['is_verified'],
    ),
    'active'      => array_values($active),
    'pending'     => array_values($pending),
    'total_value' => $totalStr,
));
