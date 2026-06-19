<?php
/* [api/get_stats.php] - PHP 5.6 compatible */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$base = dirname(__DIR__);
require_once $base . '/includes/db.php';

$pdo   = getDB();
$total = (int)$pdo->query("SELECT COUNT(*) FROM welfare_programs WHERE is_active = 1")->fetchColumn();

echo json_encode(array(
    'success'       => true,
    'total_welfare' => $total,
    'avg_time'      => '2 นาที',
    'avg_benefit'   => '฿3,200+',
));
