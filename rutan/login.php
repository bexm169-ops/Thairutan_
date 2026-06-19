<?php
/* [login.php] - PHP 5.6 compatible */
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: my-rights.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $national_id = trim(isset($_POST['national_id']) ? $_POST['national_id'] : '');
    $laser_code  = trim(isset($_POST['laser_code'])  ? $_POST['laser_code']  : '');
    $ip          = $_SERVER['REMOTE_ADDR'];
    $ua          = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    if (strlen($national_id) !== 13 || !ctype_digit($national_id)) {
        $error = 'กรุณากรอกเลขบัตรประชาชน 13 หลักให้ถูกต้อง';
    } elseif (empty($laser_code)) {
        $error = 'กรุณากรอกเลขหลังบัตร (Laser Code)';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE national_id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute(array($national_id));
        $user = $stmt->fetch();

        if (!$user) {
            $pdo->prepare("INSERT INTO login_logs (national_id_tried,ip_address,user_agent,status) VALUES (?,?,?,'fail_not_found')")
                ->execute(array($national_id, $ip, $ua));
            $error = 'ไม่พบบัญชีผู้ใช้นี้ในระบบ';
        } elseif (!password_verify($laser_code, $user['password_hash'])) {
            $pdo->prepare("INSERT INTO login_logs (user_id,national_id_tried,ip_address,user_agent,status) VALUES (?,?,?,?,'fail_password')")
                ->execute(array($user['id'], $national_id, $ip, $ua));
            $error = 'เลขหลังบัตร (Laser Code) ไม่ถูกต้อง';
        } else {
            $pdo->prepare("INSERT INTO login_logs (user_id,national_id_tried,ip_address,user_agent,status) VALUES (?,?,?,?,'success')")
                ->execute(array($user['id'], $national_id, $ip, $ua));
            session_regenerate_id(true);
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name']  = $user['last_name'];
            $_SESSION['national_id']= $user['national_id'];
            $_SESSION['is_verified']= $user['is_verified'];
            header('Location: my-rights.php');
            exit;
        }
    }
}
$val_national_id = htmlspecialchars(isset($_POST['national_id']) ? $_POST['national_id'] : '');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - BKK Welfare Matcher</title>
    <link rel="stylesheet" href="file_css/global.css">
    <link rel="stylesheet" href="file_css/login.css">
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo-section">
                <div class="logo-icon">ช</div>
                <div class="logo-text">
                    <span class="logo-title">เช็คสิทธิ์ กทม.</span>
                    <span class="logo-subtitle">BKK Welfare Matcher</span>
                </div>
            </div>
        </div>
    </header>
    <main class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <h1 class="auth-title">เข้าสู่ระบบ</h1>
                <p class="auth-subtitle">ใช้เลขบัตรประชาชนและเลขหลังบัตร (Laser Code)</p>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php" class="auth-form">
                    <div class="form-group">
                        <label for="national_id">เลขบัตรประชาชน 13 หลัก</label>
                        <input type="text" id="national_id" name="national_id" maxlength="13"
                               placeholder="x-xxxx-xxxxx-xx-x" required value="<?php echo $val_national_id; ?>">
                    </div>
                    <div class="form-group">
                        <label for="laser_code">เลขหลังบัตร (Laser Code)</label>
                        <input type="text" id="laser_code" name="laser_code" maxlength="14"
                               placeholder="TH1-1234567-89" required>
                        <small class="form-hint">ตัวอักษรและตัวเลขด้านหลังบัตรประชาชน</small>
                    </div>
                    <button type="submit" class="btn-primary btn-full">เข้าสู่ระบบ</button>
                </form>
                <div class="auth-divider"><span>หรือ</span></div>
                <p class="auth-footer-text">ยังไม่มีบัญชี? <a href="register.php" class="link-gold">ลงทะเบียนที่นี่</a></p>
                <p class="auth-footer-text"><a href="index.php" class="link-muted">← กลับหน้าแรก</a></p>
            </div>
        </div>
    </main>
    <footer class="main-footer">
        <div class="footer-container"><p>&copy; 2026 BKK Welfare Matcher. All Rights Reserved.</p></div>
    </footer>
</body>
</html>
