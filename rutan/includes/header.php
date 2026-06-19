<?php
/* ==========================================================================
   [includes/header.php] - Header ที่ใช้ร่วมกันทุกหน้า
   ========================================================================== */
if (session_status() === PHP_SESSION_NONE) session_start();
$user = isset($user) ? $user : getCurrentUser();
$currentFile = basename($_SERVER['PHP_SELF']);
?>
<header class="main-header">
    <div class="header-container">
        <div class="logo-section">
            <div class="logo-icon" id="logo-avatar">
                <?php echo $user ? mb_substr($user['first_name'], 0, 1, 'UTF-8') : 'ช'; ?>
            </div>
            <div class="logo-text">
                <span class="logo-title">ไทยรู้ทัน</span>
                <span class="logo-subtitle">Thairutan</span>
            </div>
        </div>
        <button class="menu-toggle" id="menu-open"><span></span><span></span><span></span></button>
        <nav class="nav-menu" id="nav-menu">
            <button class="menu-close" id="menu-close">&times;</button>
            <ul class="nav-links">
                <li><a href="index.php" class="<?php echo $currentFile === 'index.php' ? 'active' : ''; ?>">หน้าแรก</a></li>
                <li><a href="welfare.php" class="<?php echo $currentFile === 'welfare.php' ? 'active' : ''; ?>">สวัสดิการทั้งหมด</a></li>
                <li><a href="my-rights.php" class="<?php echo $currentFile === 'my-rights.php' ? 'active' : ''; ?>">สิทธิ์ของฉัน</a></li>
                <li><a href="rights-detail.php" class="<?php echo $currentFile === 'rights-detail.php' ? 'active' : ''; ?>">รายละเอียดสิทธิ์</a></li>
                <li><a href="about.php" class="<?php echo $currentFile === 'about.php' ? 'active' : ''; ?>">เกี่ยวกับเรา</a></li>
            </ul>
            <div class="auth-actions" id="auth-actions-container">
                <?php if ($user): ?>
                    <span class="user-greeting">สวัสดี, <?php echo htmlspecialchars($user['first_name']); ?></span>
                    <a href="logout.php" class="btn-login">ออกจากระบบ</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login" id="btn-auth">เข้าสู่ระบบ</a>
                <?php endif; ?>
                <a href="my-rights.php" class="btn-check-now">เช็คสิทธิ์เลย</a>
            </div>
        </nav>
    </div>
</header>
