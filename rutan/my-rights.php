<?php
/* ==========================================================================
   [my-rights.php] - หน้าสิทธิ์ของฉัน
   ========================================================================== */
require_once 'includes/db.php';
require_once 'includes/auth.php';

$user = getCurrentUser();

$active  = array();
$pending = array();
$userData = null;
$totalValue = 0;

if ($user) {
    $pdo = getDB();

    // ดึงข้อมูล user 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute(array($user['id']));
    $userData = $stmt->fetch();

    // ดึงสิทธิ์ทั้งหมด
    $stmt = $pdo->prepare("
        SELECT ur.status, ur.program_id,
               wp.title, wp.category_label, wp.benefit_value, wp.description
        FROM user_rights ur
        JOIN welfare_programs wp ON ur.program_id = wp.id
        WHERE ur.user_id = ?
        ORDER BY FIELD(ur.status,'active','pending','rejected','expired'), ur.created_at DESC
    ");
    $stmt->execute(array($user['id']));
    $rights = $stmt->fetchAll();

    foreach ($rights as $r) {
        if ($r['status'] === 'active')  $active[]  = $r;
        if ($r['status'] === 'pending') $pending[] = $r;
    }

    // คำนวณมูลค่ารวม
    foreach ($active as $r) {
        if (preg_match('/฿([\d,]+)/', $r['benefit_value'], $m)) {
            $totalValue += (int)str_replace(',', '', $m[1]);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สิทธิ์ของฉัน - BKK Welfare Matcher</title>
    <link rel="stylesheet" href="file_css/global.css">
    <link rel="stylesheet" href="file_css/my-rights.css">
    
    <style>
        /* ==========================================================================
           1. CSS สำหรับหน้า Dashboard (เมื่อล็อกอินแล้ว)
           ========================================================================== */
        .dashboard-container { padding: 30px 20px; display: flex; flex-direction: column; gap: 30px; }

        .user-profile-box {
            background-color: var(--bg-surface, #1e293b); padding: 20px; border-radius: var(--radius-md, 8px);
            display: flex; flex-direction: column; gap: 15px; border: 1px solid var(--border-light, #334155);
        }
        .profile-info { display: flex; align-items: center; gap: 15px; }
        .profile-avatar {
            width: 50px; height: 50px; background: var(--accent-gold, #ffb200); color: var(--bg-base, #111);
            border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold;
        }
        .user-name { font-size: 18px; font-weight: 600; margin: 0; color: #fff;}
        .user-id { font-size: 13px; color: var(--text-muted, #94a3b8); margin: 5px 0 0 0; }
        .status-verified { display: inline-block; padding: 4px 12px; background: rgba(104, 211, 145, 0.1); border: 1px solid var(--accent-green, #68d391); color: var(--accent-green, #68d391); border-radius: 20px; font-size: 12px; }
        .status-unverified { display: inline-block; padding: 4px 12px; background: rgba(255, 107, 107, 0.1); border: 1px solid #ff6b6b; color: #ff6b6b; border-radius: 20px; font-size: 12px; }

        .my-stats-row { display: flex; flex-direction: column; gap: 15px; }
        .my-stat-card { background-color: var(--bg-surface, #1e293b); padding: 20px; border-radius: var(--radius-md, 8px); text-align: center; border: 1px solid var(--border-light, #334155); }
        .stat-num { font-size: 24px; font-weight: 700; color: var(--text-main, #fff); margin-bottom: 5px;}
        .stat-num.highlight { color: var(--accent-gold, #ffb200); }
        .stat-text { font-size: 12px; color: var(--text-muted, #94a3b8); }

        .list-title { font-size: 16px; margin-bottom: 15px; padding-left: 10px; border-left: 3px solid var(--accent-gold, #ffb200); color: #fff;}
        .active-rights-container, .pending-rights-container { display: flex; flex-direction: column; gap: 15px; }
        .list-item { background: var(--bg-surface, #1e293b); border: 1px solid var(--border-light, #334155); padding: 20px; border-radius: var(--radius-sm, 6px); }
        .highlight-item { border-color: rgba(255, 178, 0, 0.5); background-color: rgba(255, 178, 0, 0.03); }

        .item-category { font-size: 11px; color: var(--text-muted, #94a3b8); }
        .item-name { font-size: 16px; margin: 5px 0; font-weight: 600; color: #fff;}
        .item-desc { font-size: 13px; color: var(--text-muted, #94a3b8); line-height: 1.5; margin-bottom: 10px;}
        .item-value { color: var(--accent-gold, #ffb200); font-weight: 600; font-size: 14px; }
        .item-status.active { color: var(--accent-green, #68d391); font-size: 12px; display: block; margin-top: 10px;}
        .item-action-row { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; border-top: 1px solid var(--border-light, #334155); padding-top: 15px; }
        .btn-register-action { background: var(--accent-gold, #ffb200); color: #000; padding: 8px 16px; text-decoration: none; font-size: 13px; border-radius: var(--radius-sm, 6px); font-weight: 600; display: inline-block; text-align: center; transition: background 0.2s;}
        .btn-register-action:hover { background: #e5a000; }

        /* ==========================================================================
           2. CSS สำหรับหน้าก่อนล็อกอิน (ดีไซน์กระจกฝ้าที่ผมทำให้)
           ========================================================================== */
        .unauth-wrapper {
            display: flex; justify-content: center; align-items: center; padding: 60px 20px; min-height: 60vh; font-family: sans-serif;
        }
        .unauth-card {
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.8), rgba(30, 41, 59, 0.6));
            border: 1px solid rgba(255, 178, 0, 0.2); border-radius: 20px; padding: 50px 40px;
            max-width: 550px; text-align: center; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5); backdrop-filter: blur(12px);
        }
        .unauth-icon-wrapper { font-size: 56px; margin-bottom: 20px; line-height: 1; filter: drop-shadow(0 0 10px rgba(255, 178, 0, 0.4)); }
        .unauth-title { color: #ffffff; font-size: 26px; font-weight: 700; margin-bottom: 15px; letter-spacing: 0.5px; }
        .unauth-desc { color: #94a3b8; font-size: 15px; line-height: 1.6; margin-bottom: 35px; }
        .unauth-buttons { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
        .btn-gold-login {
            background: linear-gradient(90deg, #ffb200, #ffca43); color: #0f172a; padding: 14px 35px;
            border-radius: 8px; font-size: 16px; font-weight: 700; text-decoration: none; transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 178, 0, 0.2); border: none; cursor: pointer;
        }
        .btn-gold-login:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(255, 178, 0, 0.4); }
        .btn-outline-register {
            background: transparent; color: #c084fc; border: 2px solid #c084fc; padding: 12px 35px;
            border-radius: 8px; font-size: 16px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; cursor: pointer;
        }
        .btn-outline-register:hover { background: rgba(192, 132, 252, 0.1); transform: translateY(-3px); box-shadow: 0 8px 25px rgba(192, 132, 252, 0.2); }

        /* Desktop View Dashboard */
        @media (min-width: 768px) {
            .dashboard-container { max-width: 1000px; margin: 0 auto; padding: 50px 20px; }
            .user-profile-box { flex-direction: row; justify-content: space-between; align-items: center; padding: 30px; }
            .my-stats-row { flex-direction: row; }
            .my-stat-card { flex: 1; }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="dashboard-page">
        <div class="dashboard-container">

            <?php if (!$user || !$userData): ?>
            <div class="unauth-wrapper">
                <div class="unauth-card">
                    <div class="unauth-icon-wrapper"></div>
                    <h2 class="unauth-title">เข้าสู่ระบบเพื่อดูสิทธิ์ของคุณ</h2>
                    <p class="unauth-desc">ระบบจะทำการประมวลผลและจับคู่สวัสดิการของกรุงเทพมหานคร<br>ที่เหมาะสมกับข้อมูลของคุณโดยอัตโนมัติ</p>
                    <div class="unauth-buttons">
                        <a href="login.php" class="btn-gold-login">เข้าสู่ระบบ</a>
                        <a href="register.php" class="btn-outline-register">ลงทะเบียนใหม่</a>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <section class="user-profile-box">
                <div class="profile-info">
                    <div class="profile-avatar">
                        <?php echo mb_substr($userData['first_name'], 0, 1, 'UTF-8'); ?>
                    </div>
                    <div class="profile-details">
                        <h2 class="user-name">
                            <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?>
                        </h2>
                        <p class="user-id">
                            เลขประจำตัวประชาชน: 
                            <?php echo htmlspecialchars(
                                substr($userData['national_id'], 0, 1) . '-xxxx-xxxxx-' .
                                substr($userData['national_id'], -2) . '-x'
                            ); ?>
                        </p>
                    </div>
                </div>
                <div class="profile-status">
                    <?php if ($userData['is_verified']): ?>
                        <span class="status-verified">✓ ยืนยันตัวตนแล้ว</span>
                    <?php else: ?>
                        <span class="status-unverified">⚠ ยังไม่ได้ยืนยันตัวตน</span>
                    <?php endif; ?>
                </div>
            </section>

            <section class="my-stats-row">
                <div class="my-stat-card">
                    <div class="stat-num"><?php echo count($active); ?></div>
                    <div class="stat-text">สิทธิ์ที่ได้รับอยู่</div>
                </div>
                <div class="my-stat-card">
                    <div class="stat-num"><?php echo count($pending); ?></div>
                    <div class="stat-text">สิทธิ์ที่แนะนำให้ลงทะเบียน</div>
                </div>
                <div class="my-stat-card">
                    <div class="stat-num highlight">
                        <?php echo $totalValue > 0 ? '฿' . number_format($totalValue) : 'ดูรายละเอียด'; ?>
                    </div>
                    <div class="stat-text">มูลค่าสิทธิ์ที่ได้รับ/เดือน</div>
                </div>
            </section>

            <section class="rights-list-section">
                <h3 class="list-title">สวัสดิการที่ใช้งานอยู่</h3>
                <div class="active-rights-container">
                    <?php if (empty($active)): ?>
                        <p style="color:#94a3b8;padding:15px 0">ยังไม่มีสวัสดิการที่ใช้งานอยู่</p>
                    <?php else: ?>
                        <?php foreach ($active as $r): ?>
                        <div class="list-item">
                            <span class="item-category"><?php echo htmlspecialchars($r['category_label']); ?></span>
                            <h4 class="item-name"><?php echo htmlspecialchars($r['title']); ?></h4>
                            <?php if ($r['description']): ?>
                                <p class="item-desc"><?php echo htmlspecialchars($r['description']); ?></p>
                            <?php endif; ?>
                            <p class="item-value"><?php echo htmlspecialchars(isset($r['benefit_value']) ? $r['benefit_value'] : ''); ?></p>
                            <span class="item-status active">● กำลังใช้งาน</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="rights-list-section">
                <h3 class="list-title">สวัสดิการที่คุณมีสิทธิ์ (รอลงทะเบียน)</h3>
                <div class="pending-rights-container">
                    <?php if (empty($pending)): ?>
                        <p style="color:#94a3b8;padding:15px 0">ไม่มีสวัสดิการที่รอลงทะเบียน</p>
                    <?php else: ?>
                        <?php foreach ($pending as $r): ?>
                        <div class="list-item highlight-item">
                            <span class="item-category"><?php echo htmlspecialchars($r['category_label']); ?></span>
                            <h4 class="item-name"><?php echo htmlspecialchars($r['title']); ?></h4>
                            <div class="item-action-row">
                                <span class="item-value"><?php echo htmlspecialchars(!empty($r['benefit_value']) ? $r['benefit_value'] : 'ดูรายละเอียด'); ?></span>
                                <a href="rights-detail.php?id=<?php echo (int)$r['program_id']; ?>" class="btn-register-action">
                                    ลงทะเบียนรับสิทธิ์
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <?php endif; ?>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-container"><p>&copy; 2026 BKK Welfare Matcher. All Rights Reserved.</p></div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>