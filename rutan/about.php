<?php 
require_once 'includes/db.php'; 
require_once 'includes/auth.php'; 

$message = '';
$status_class = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_appeal'])) {
    $national_id = isset($_POST['national_id']) ? trim($_POST['national_id']) : '';
    $full_name   = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $topic       = isset($_POST['topic']) ? trim($_POST['topic']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    $file_path = '';
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['evidence']['tmp_name'], $target_file)) {
            $file_path = $target_file;
        }
    }

    if (empty($national_id) || empty($full_name) || empty($topic)) {
        $message = "กรุณากรอกข้อมูลที่มีเครื่องหมาย * ให้ครบถ้วน";
        $status_class = "error";
    } else {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("INSERT INTO appeals (national_id, full_name, topic, description, file_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(array($national_id, $full_name, $topic, $description, $file_path));
            
            $message = "ส่งข้อมูลแจ้งสิทธิ์ตกหล่นเรียบร้อยแล้ว เจ้าหน้าที่จะทำการตรวจสอบโดยเร็วที่สุด";
            $status_class = "success";
        } catch (Exception $e) {
            $message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
            $status_class = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกี่ยวกับเรา - BKK Welfare Matcher</title>
    <link rel="stylesheet" href="file_css/global.css">
    <link rel="stylesheet" href="file_css/about.css">
    
    <style>
        #force-beautiful-form {
            background: linear-gradient(145deg, #111a2e, #1a243d);
            border: 1px solid rgba(255, 178, 0, 0.3);
            border-radius: 12px;
            padding: 40px;
            margin: 40px auto;
            max-width: 700px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            font-family: sans-serif;
        }
        #force-beautiful-form h2 {
            color: #ffffff;
            text-align: center;
            font-size: 24px;
            margin-bottom: 5px;
        }
        #force-beautiful-form .sub-desc {
            color: #a0aec0;
            text-align: center;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .my-input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 18px;
            text-align: left;
        }
        .my-input-group label {
            color: #e2e8f0;
            font-size: 14px;
            font-weight: 600;
        }
        .my-input-group label span {
            color: #ffb200; /* ดอกจันสีทอง */
        }
        .my-input-group input[type="text"],
        .my-input-group select,
        .my-input-group textarea {
            width: 100%;
            background-color: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
        }
        .my-input-group input[type="text"]:focus,
        .my-input-group select:focus,
        .my-input-group textarea:focus {
            border-color: #ffb200;
            box-shadow: 0 0 8px rgba(255, 178, 0, 0.3);
            background-color: #1e293b;
        }
        .my-input-group input[type="file"] {
            color: #ffffff;
            padding: 10px 0;
        }
        .my-input-group small {
            color: #a0aec0;
            font-size: 13px;
            margin-top: -5px;
        }
        .btn-gold-submit {
            background: linear-gradient(90deg, #ffb200, #ffca43);
            color: #000000;
            font-weight: bold;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            padding: 15px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-gold-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 178, 0, 0.4);
        }
        .alert-box {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }
        .alert-success { background: rgba(72, 187, 120, 0.2); border: 1px solid #48bb78; color: #9ae6b4; }
        .alert-error { background: rgba(229, 62, 62, 0.2); border: 1px solid #e53e3e; color: #feb2b2; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="about-page">
        <div class="about-container">
            <section class="about-hero">
                <div class="hero-badge">เกี่ยวกับโครงการ</div>
                <h1 class="hero-title">ลดความเหลื่อมล้ำ<br>ด้วยเทคโนโลยีที่เข้าถึงง่าย</h1>
                <p class="hero-desc"><strong>BKK Welfare Matcher</strong> พัฒนาขึ้นมาเพื่อแก้ไขปัญหาการพลาดโอกาสรับสวัสดิการรัฐ เราเชื่อว่าประชาชนชาวกรุงเทพมหานครทุกคน ควรได้รับสิทธิ์และการดูแลอย่างทั่วถึงโดยไม่มีอุปสรรคด้านข้อมูลข่าวสาร</p>
            </section>
            
            <section class="mission-section">
                <div class="grid-two-cols">
                    <div class="mission-card">
                        <h2 class="sub-title">วิสัยทัศน์ของเรา</h2>
                        <p class="section-text">เป็นศูนย์กลางในการคัดกรอง ยืนยัน และให้ข้อมูลด้านสวัสดิการสังคมของกรุงเทพมหานครที่มีความแม่นยำ โปร่งใส และใช้งานง่ายที่สุด</p>
                    </div>
                    <div class="mission-card-highlight">
                        <h2 class="sub-title-highlight">ทำไมต้อง BKK Welfare Matcher?</h2>
                        <p class="section-text-highlight">บ่อยครั้งที่สวัสดิการดีๆ ถูกมองข้ามไปเพียงเพราะกระบวนการตรวจสอบที่ซับซ้อน ระบบของเราช่วยตัดขั้นตอนเหล่านั้นออกไป</p>
                    </div>
                </div>
            </section>
            
            <section class="features-section">
                <h2 class="section-center-title">สิ่งที่เราตั้งใจส่งมอบ</h2>
                <div class="features-grid">
                    <div class="feature-box"><h3>ระบบคัดกรองอัจฉริยะ</h3><p>วิเคราะห์ข้อมูลส่วนบุคคลและประเมินสิทธิ์สวัสดิการที่เหมาะสมโดยอัตโนมัติ</p></div>
                    <div class="feature-box"><h3>รวบรวมข้อมูลครบวงจร</h3><p>สวัสดิการเด็กแรกเกิด ผู้สูงอายุ ผู้พิการ หรือทุนการศึกษา รวมไว้ในที่เดียว</p></div>
                    <div class="feature-box"><h3>ปลอดภัยและเป็นส่วนตัว</h3><p>ข้อมูลของคุณจะถูกนำมาใช้เพื่อการคำนวณสิทธิ์อย่างปลอดภัยสูงสุด</p></div>
                </div>
            </section>

            <div id="force-beautiful-form">
                <h2>แจ้งสิทธิ์ตกหล่น / ยื่นเอกสารเพิ่มเติม</h2>
                <p class="sub-desc">หากท่านไม่พบข้อมูลสิทธิ์สวัสดิการของท่านในระบบ หรือต้องการยื่นข้อมูลตรวจสอบเพิ่ม กรุณากรอกแบบฟอร์มด้านล่าง</p>
                
                <?php if ($message): ?>
                    <div class="alert-box alert-<?php echo $status_class; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form action="about.php#force-beautiful-form" method="POST" enctype="multipart/form-data">
                    <div class="my-input-group">
                        <label for="national_id">เลขบัตรประชาชน 13 หลัก <span>*</span></label>
                        <input type="text" id="national_id" name="national_id" maxlength="13" required placeholder="กรอกเลขบัตรประชาชน 13 หลัก">
                    </div>
                    
                    <div class="my-input-group">
                        <label for="full_name">ชื่อ-นามสกุล <span>*</span></label>
                        <input type="text" id="full_name" name="full_name" required placeholder="กรอกชื่อและนามสกุลจริง">
                    </div>
                    
                    <div class="my-input-group">
                        <label for="topic">สวัสดิการที่ต้องการแจ้งตกหล่น <span>*</span></label>
                        <select id="topic" name="topic" required>
                            <option value="">-- เลือกประเภทสวัสดิการ --</option>
                            <option value="เงินอุดหนุนเด็กแรกเกิด">เงินอุดหนุนเด็กแรกเกิด</option>
                            <option value="เบี้ยยังชีพผู้สูงอายุ">เบี้ยยังชีพผู้สูงอายุ</option>
                            <option value="เบี้ยความพิการ">เบี้ยความพิการ</option>
                            <option value="ทุนการศึกษา">ทุนการศึกษา กทม.</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>

                    <div class="my-input-group">
                        <label for="description">รายละเอียดเพิ่มเติม</label>
                        <textarea id="description" name="description" rows="4" placeholder="โปรดระบุรายละเอียดเช่น ปีที่เริ่มตกหล่น หรือข้อมูลอื่นๆ ที่ช่วยให้เจ้าหน้าที่ตรวจสอบได้เร็วขึ้น..."></textarea>
                    </div>

                    <div class="my-input-group">
                        <label for="evidence">แนบเอกสารหลักฐาน (ถ้ามี)</label>
                        <input type="file" id="evidence" name="evidence" accept=".jpg,.jpeg,.png,.pdf">
                        <small>รองรับไฟล์ JPG, PNG, PDF ขนาดไม่เกิน 5MB</small>
                    </div>

                    <button type="submit" name="submit_appeal" class="btn-gold-submit">ส่งข้อมูลแจ้งตกหล่น</button>
                </form>
            </div>
            
        </div>
    </main>
    
    <footer class="main-footer">
        <div class="footer-container"><p>&copy; 2026 BKK Welfare Matcher. All Rights Reserved.</p></div>
    </footer>
    <script src="js/main.js"></script>
</body>
</html>