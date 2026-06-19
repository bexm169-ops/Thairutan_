<?php
/* [register.php] - PHP 5.6 compatible */
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: my-rights.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $national_id = trim(isset($_POST['national_id']) ? $_POST['national_id'] : '');
    $laser_code  = trim(isset($_POST['laser_code'])  ? $_POST['laser_code']  : '');
    $first_name  = trim(isset($_POST['first_name'])  ? $_POST['first_name']  : '');
    $last_name   = trim(isset($_POST['last_name'])   ? $_POST['last_name']   : '');
    $birthdate   = trim(isset($_POST['birthdate'])   ? $_POST['birthdate']   : '');
    $gender      = trim(isset($_POST['gender'])      ? $_POST['gender']      : 'other');
    $phone       = trim(isset($_POST['phone'])       ? $_POST['phone']       : '');
    $district    = trim(isset($_POST['district'])    ? $_POST['district']    : '');

    if (strlen($national_id) !== 13 || !ctype_digit($national_id)) {
        $error = 'เลขบัตรประชาชนต้องเป็นตัวเลข 13 หลัก';
    } elseif (empty($laser_code) || strlen($laser_code) < 10) {
        $error = 'กรุณากรอกเลขหลังบัตร (Laser Code) ให้ครบถ้วน';
    } elseif (empty($first_name) || empty($last_name)) {
        $error = 'กรุณากรอกชื่อและนามสกุล';
    } elseif (empty($birthdate)) {
        $error = 'กรุณากรอกวันเดือนปีเกิด';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE national_id = ? LIMIT 1");
        $stmt->execute(array($national_id));
        if ($stmt->fetch()) {
            $error = 'เลขบัตรประชาชนนี้มีในระบบแล้ว กรุณาเข้าสู่ระบบ';
        } else {
            $hash = password_hash($laser_code, PASSWORD_BCRYPT, array('cost' => 12));
            $ins  = $pdo->prepare("INSERT INTO users (national_id, laser_code, password_hash, first_name, last_name, birthdate, gender, phone, district) VALUES (?,?,?,?,?,?,?,?,?)");
            $ins->execute(array($national_id, $laser_code, $hash, $first_name, $last_name, $birthdate, $gender, $phone, $district));
            $success = 'ลงทะเบียนสำเร็จ! กรุณา<a href="login.php" class="link-gold">เข้าสู่ระบบ</a>';
        }
    }
}

$districts = array('พระนคร','ดุสิต','หนองจอก','บางรัก','บางเขน','ลาดกระบัง','ยานนาวา','สัมพันธวงศ์','พระโขนง','มีนบุรี','ลาดพร้าว','วังทองหลาง','คลองสาน','ตลิ่งชัน','บางกอกน้อย','บึงกุ่ม','สาทร','บางซื่อ','จตุจักร','บางกอกใหญ่','ห้วยขวาง','ดอนเมือง','ราษฎร์บูรณะ','หลักสี่','คันนายาว','สะพานสูง','สวนหลวง','บางนา','ทวีวัฒนา','ทุ่งครุ','บางบอน','คลองเตย','บึงกุ่ม','บางแค');
$districts = array_unique($districts);
sort($districts);

$p = $_POST;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน - BKK Welfare Matcher</title>
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
        <div class="auth-container" style="max-width:600px">
            <div class="auth-card">
                <h1 class="auth-title">ลงทะเบียนใหม่</h1>
                <p class="auth-subtitle">สร้างบัญชีเพื่อตรวจสอบสิทธิ์สวัสดิการของคุณ</p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <form method="POST" action="register.php" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>ชื่อ *</label>
                            <input type="text" name="first_name" required value="<?php echo htmlspecialchars(isset($p['first_name']) ? $p['first_name'] : ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>นามสกุล *</label>
                            <input type="text" name="last_name" required value="<?php echo htmlspecialchars(isset($p['last_name']) ? $p['last_name'] : ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>เลขบัตรประชาชน 13 หลัก *</label>
                        <input type="text" name="national_id" maxlength="13" placeholder="x-xxxx-xxxxx-xx-x" required value="<?php echo htmlspecialchars(isset($p['national_id']) ? $p['national_id'] : ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>เลขหลังบัตร Laser Code *</label>
                        <input type="text" name="laser_code" placeholder="TH1-1234567-89" required>
                        <small class="form-hint">ใช้เป็นรหัสผ่านสำหรับเข้าสู่ระบบ เก็บไว้เป็นความลับ</small>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>วันเดือนปีเกิด *</label>
                            <input type="date" name="birthdate" required value="<?php echo htmlspecialchars(isset($p['birthdate']) ? $p['birthdate'] : ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>เพศ</label>
                            <select name="gender">
                                <option value="male"   <?php echo (isset($p['gender']) && $p['gender']==='male')   ? 'selected' : ''; ?>>ชาย</option>
                                <option value="female" <?php echo (isset($p['gender']) && $p['gender']==='female') ? 'selected' : ''; ?>>หญิง</option>
                                <option value="other"  <?php echo (!isset($p['gender']) || $p['gender']==='other') ? 'selected' : ''; ?>>อื่น ๆ</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>เบอร์โทรศัพท์</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars(isset($p['phone']) ? $p['phone'] : ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>เขตที่อยู่ใน กทม.</label>
                            <select name="district">
                                <option value="">-- เลือกเขต --</option>
                                <?php foreach ($districts as $d): ?>
                                <option value="<?php echo htmlspecialchars($d); ?>"
                                    <?php echo (isset($p['district']) && $p['district']===$d) ? 'selected' : ''; ?>>
                                    เขต<?php echo htmlspecialchars($d); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary btn-full">สร้างบัญชี</button>
                </form>
                <?php endif; ?>

                <div class="auth-divider"><span>มีบัญชีแล้ว?</span></div>
                <p class="auth-footer-text"><a href="login.php" class="link-gold">เข้าสู่ระบบ</a></p>
            </div>
        </div>
    </main>
    <footer class="main-footer">
        <div class="footer-container"><p>&copy; 2026 BKK Welfare Matcher. All Rights Reserved.</p></div>
    </footer>
</body>
</html>
