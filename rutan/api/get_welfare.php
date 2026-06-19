<?php
/* [api/get_welfare.php] - PHP 5.6 compatible */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$base = dirname(__DIR__);
require_once $base . '/includes/db.php';

$pdo      = getDB();
$category = trim(isset($_GET['category']) ? $_GET['category'] : 'all');
$search   = '%' . trim(isset($_GET['search']) ? $_GET['search'] : '') . '%';

if ($category === 'all') {
    $stmt = $pdo->prepare("SELECT id, title, category, category_label, benefit_value FROM welfare_programs WHERE is_active = 1 AND title LIKE ? ORDER BY category, id");
    $stmt->execute(array($search));
} else {
    $stmt = $pdo->prepare("SELECT id, title, category, category_label, benefit_value FROM welfare_programs WHERE is_active = 1 AND category = ? AND title LIKE ? ORDER BY id");
    $stmt->execute(array($category, $search));
}

$data = $stmt->fetchAll();
echo json_encode(array('success' => true, 'data' => $data));
