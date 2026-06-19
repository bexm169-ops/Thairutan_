<?php
/* [api/get_welfare_detail.php] - PHP 5.6 compatible */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$base = dirname(__DIR__);
require_once $base . '/includes/db.php';

$id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
if (!$id) {
    echo json_encode(array('success' => false, 'message' => 'ไม่ระบุ ID'));
    exit;
}

$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM welfare_programs WHERE id = ? AND is_active = 1 LIMIT 1");
$stmt->execute(array($id));
$data = $stmt->fetch();

if (!$data) {
    echo json_encode(array('success' => false, 'message' => 'ไม่พบข้อมูล'));
    exit;
}

$crit = $pdo->prepare("SELECT criteria_text FROM welfare_criteria WHERE program_id = ? ORDER BY sort_order");
$crit->execute(array($id));
$data['criteria'] = $crit->fetchAll(PDO::FETCH_COLUMN);

$docs = $pdo->prepare("SELECT document_text FROM welfare_documents WHERE program_id = ? ORDER BY sort_order");
$docs->execute(array($id));
$data['documents'] = $docs->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(array('success' => true, 'data' => $data));
