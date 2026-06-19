<?php
/* [rights-detail.php] - PHP 5.6 compatible */
require_once 'includes/db.php';
require_once 'includes/auth.php';

$user      = getCurrentUser();
$id        = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
$program   = null;
$criteria  = array();
$documents = array();

if ($id > 0) {
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM welfare_programs WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmt->execute(array($id));
    $program = $stmt->fetch();

    if ($program) {
        $c = $pdo->prepare("SELECT criteria_text FROM welfare_criteria WHERE program_id = ? ORDER BY sort_order");
        $c->execute(array($id));
        $criteria = $c->fetchAll(PDO::FETCH_COLUMN);

        $d = $pdo->prepare("SELECT document_text FROM welfare_documents WHERE program_id = ? ORDER BY sort_order");
        $d->execute(array($id));
        $documents = $d->fetchAll(PDO::FETCH_COLUMN);
    }
}
$page_title = $program ? htmlspecialchars($program['title']) : 'รายละเอียดสวัสดิการ';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - BKK Welfare Matcher</title>
    <link rel="stylesheet" href="file_css/global.css">
    <link rel="stylesheet" href="file_css/rights-detail.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="detail-page">
        <div class="detail-container">
            <a href="javascript:history.back()" class="back-link">&laquo; ย้อนกลับ</a>

            <?php if (!$id): ?>
            <div class="detail-header-card">
                <h1 class="project-title">ไม่ระบุ ID สวัสดิการ</h1>
                <p class="project-subtitle">กรุณาเข้าถึงหน้านี้ผ่านการ์ดสวัสดิการ</p>
            </div>

            <?php elseif (!$program): ?>
            <div class="detail-header-card">
                <h1 class="project-title">ไม่พบข้อมูลสวัสดิการ</h1>
                <p class="project-subtitle">ID: <?php echo $id; ?> ไม่มีในระบบหรือถูกปิดใช้งาน</p>
            </div>

            <?php else: ?>
            <div class="detail-header-card">
                <span class="badge"><?php echo htmlspecialchars($program['category_label']); ?></span>
                <h1 class="project-title"><?php echo htmlspecialchars($program['title']); ?></h1>
                <p class="project-subtitle"><?php echo htmlspecialchars(!empty($program['agency']) ? $program['agency'] : ''); ?></p>
            </div>

            <div class="detail-content-layout">
                <div class="content-left">
                    <section class="info-block">
                        <h2>รายละเอียดโครงการ</h2>
                        <p><?php echo htmlspecialchars(!empty($program['description']) ? $program['description'] : 'ยังไม่มีรายละเอียดเพิ่มเติมในระบบ'); ?></p>
                    </section>

                    <section class="info-block">
                        <h2>คุณสมบัติผู้มีสิทธิ์รับสวัสดิการ</h2>
                        <?php if (!empty($criteria)): ?>
                        <ul class="check-list">
                            <?php foreach ($criteria as $c): ?>
                                <li><?php echo htmlspecialchars($c); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                            <p style="color:#718096">ยังไม่มีข้อมูลคุณสมบัติ</p>
                        <?php endif; ?>
                    </section>

                    <section class="info-block">
                        <h2>เอกสารที่ต้องเตรียม</h2>
                        <?php if (!empty($documents)): ?>
                        <ul class="check-list">
                            <?php foreach ($documents as $d): ?>
                                <li><?php echo htmlspecialchars($d); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                            <p style="color:#718096">ยังไม่มีข้อมูลเอกสาร</p>
                        <?php endif; ?>
                    </section>
                </div>

                <div class="content-right">
                    <div class="action-sidebar">
                        <div class="status-bar">
                            <span class="dot"></span>
                            <?php if ($user): ?>
                                สถานะ: คุณสามารถยื่นเรื่องได้
                            <?php else: ?>
                                <a href="login.php" style="color:var(--accent-green)">เข้าสู่ระบบ</a>เพื่อตรวจสอบสิทธิ์
                            <?php endif; ?>
                        </div>
                        <div class="benefit-highlight">
                            <h3><?php echo htmlspecialchars(!empty($program['benefit_value']) ? $program['benefit_value'] : '---'); ?></h3>
                            <p><?php echo htmlspecialchars(!empty($program['benefit_note']) ? $program['benefit_note'] : ''); ?></p>
                        </div>
                        <div class="action-form">
                            <p>ต้องการดำเนินการยื่นคำร้องหรือบันทึกโครงการนี้หรือไม่?</p>
                            <?php if ($user): ?>
                                <button class="btn-primary-full">เริ่มขั้นตอนยื่นคำร้อง</button>
                                <button class="btn-secondary-full">บันทึกไว้ก่อน</button>
                            <?php else: ?>
                                <a href="login.php" class="btn-primary-full">เข้าสู่ระบบเพื่อยื่นคำร้อง</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </main>
    <footer class="main-footer">
        <div class="footer-container"><p>&copy; 2026 BKK Welfare Matcher. All Rights Reserved.</p></div>
    </footer>
    <script src="js/main.js"></script>
</body>
</html>
