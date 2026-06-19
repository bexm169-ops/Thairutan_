<?php
/* [welfare.php] - PHP 5.6 compatible */
require_once 'includes/db.php';
require_once 'includes/auth.php';

$pdo        = getDB();
$category   = trim(isset($_GET['category']) ? $_GET['category'] : 'all');
$search     = trim(isset($_GET['search'])   ? $_GET['search']   : '');
$searchLike = '%' . $search . '%';

if ($category === 'all') {
    $stmt = $pdo->prepare("SELECT id, title, category, category_label, benefit_value FROM welfare_programs WHERE is_active = 1 AND title LIKE ? ORDER BY category, id");
    $stmt->execute(array($searchLike));
} else {
    $stmt = $pdo->prepare("SELECT id, title, category, category_label, benefit_value FROM welfare_programs WHERE is_active = 1 AND category = ? AND title LIKE ? ORDER BY id");
    $stmt->execute(array($category, $searchLike));
}
$welfares = $stmt->fetchAll();

$catColors = array(
    'elderly'   => 'rgba(255,178,0,0.15)',
    'child'     => 'rgba(104,211,145,0.15)',
    'health'    => 'rgba(49,130,206,0.2)',
    'disabled'  => 'rgba(183,148,246,0.15)',
    'education' => 'rgba(252,182,159,0.15)',
    'other'     => 'rgba(255,255,255,0.08)',
);

$categories = array(
    'all'      => 'ทั้งหมด',
    'elderly'  => 'ผู้สูงอายุ',
    'child'    => 'เด็กแรกเกิด',
    'health'   => 'สุขภาพ',
    'disabled' => 'คนพิการ',
);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สวัสดิการทั้งหมด - BKK Welfare Matcher</title>
    <link rel="stylesheet" href="file_css/global.css">
    <link rel="stylesheet" href="file_css/welfare.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="welfare-page">
        <section class="welfare-header">
            <h1 class="page-title">โครงการและสวัสดิการทั้งหมด</h1>
            <p class="page-subtitle">ค้นหาและทำความเข้าใจเงื่อนไขของแต่ละโครงการจากกรุงเทพมหานคร</p>
            <form method="GET" action="welfare.php" class="search-box">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <input type="text" name="search" id="searchInput"
                       placeholder="พิมพ์ชื่อสวัสดิการที่ต้องการค้นหา..."
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-search">ค้นหา</button>
            </form>
            <div class="filter-tags">
                <?php foreach ($categories as $key => $label): ?>
                <a href="welfare.php?category=<?php echo $key; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                   class="tag <?php echo $category === $key ? 'active' : ''; ?>">
                    <?php echo $label; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="welfare-grid-section">
            <div class="grid-container">
                <?php if (empty($welfares)): ?>
                    <p style="text-align:center;color:#718096;width:100%;padding:40px 0">ไม่พบข้อมูลสวัสดิการ</p>
                <?php else: ?>
                    <?php foreach ($welfares as $w): ?>
                    <?php $bg = isset($catColors[$w['category']]) ? $catColors[$w['category']] : $catColors['other']; ?>
                    <div class="welfare-card">
                        <span class="badge" style="background:<?php echo $bg; ?>">
                            <?php echo htmlspecialchars($w['category_label']); ?>
                        </span>
                        <h3 class="card-title"><?php echo htmlspecialchars($w['title']); ?></h3>
                        <div class="card-footer">
                            <span class="card-value">
                                <?php echo htmlspecialchars(!empty($w['benefit_value']) ? $w['benefit_value'] : 'ดูรายละเอียด'); ?>
                            </span>
                            <a href="rights-detail.php?id=<?php echo (int)$w['id']; ?>" class="link-readmore">อ่านเงื่อนไข &#8594;</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer class="main-footer">
        <div class="footer-container"><p>&copy; 2026 BKK Welfare Matcher. All Rights Reserved.</p></div>
    </footer>
    <script src="js/main.js"></script>
</body>
</html>
