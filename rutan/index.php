<?php
require_once 'includes/auth.php';
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BKK Welfare Matcher - ระบบเช็คสิทธิ์สวัสดิการ กทม.</title>
    <link rel="stylesheet" href="file_css/global.css">
    <link rel="stylesheet" href="file_css/index.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="home-page">
        <section class="hero-section">
            <div class="ui-middle-container">
                <h1 class="hero-title">สิทธิ์ที่คุณ<span style="color: #ffb200;">มีอยู่แล้ว</span>แต่ยังไม่เคยได้รับ</h1>
                <p class="hero-description">ค้นหาและตรวจสอบสิทธิ์สวัสดิการของคุณได้ง่ายๆ ในที่เดียว</p>
                <div class="hero-buttons">
                    <a href="my-rights.php" class="btn-primary">เริ่มต้นเช็คสิทธิ์ของคุณ</a>
                    <a href="welfare.php" class="btn-secondary">ดูสวัสดิการทั้งหมด</a>
                </div>
            </div>
        </section>
        
        </main>

    <footer class="main-footer">
        <div class="footer-container"><p>&copy; 2026 BKK Welfare Matcher. All Rights Reserved.</p></div>
    </footer>
    <script src="js/main.js"></script>
</body>
</html>