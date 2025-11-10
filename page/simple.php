<?php
$carouselStmt = dd_q("SELECT * FROM carousel");
$carouselItems = $carouselStmt ? $carouselStmt->fetchAll(PDO::FETCH_ASSOC) : [];

$annText = trim((string) ($config['ann'] ?? ''));
if ($annText === '') {
    $annText = 'ระบบจัดส่งโค้ดอัตโนมัติ 24 ชั่วโมง · โปรโมชันใหม่พร้อมให้สั่งซื้อแล้ว.';
}

$memberCount = (int) (dd_q("SELECT COUNT(*) FROM users")->fetchColumn() ?? 0) + (int) ($static['m_count'] ?? 0);
$categoryCount = (int) (dd_q("SELECT COUNT(*) FROM category")->fetchColumn() ?? 0) + (int) ($static['c_count'] ?? 0);
$productCount = (int) (dd_q("SELECT COUNT(*) FROM box_product")->fetchColumn() ?? 0) + (int) ($static['p_count'] ?? 0);
$orderCount = (int) (dd_q("SELECT COUNT(*) FROM boxlog")->fetchColumn() ?? 0) + (int) ($static['b_count'] ?? 0);
$stockCount = (int) (dd_q("SELECT COUNT(*) FROM box_stock")->fetchColumn() ?? 0) + (int) ($static['s_count'] ?? 0);

$statCards = [
    ['label' => 'สมาชิกทั้งหมด', 'meta' => 'บัญชีที่กำลังใช้งาน', 'value' => $memberCount],
    ['label' => 'สต็อกพร้อมขาย', 'meta' => 'สินค้าพร้อมส่งมอบ', 'value' => $stockCount],
    ['label' => 'หมวดหมู่', 'meta' => 'หมวดหมู่ที่คัดสรร', 'value' => $categoryCount],
    ['label' => 'คำสั่งซื้อที่สำเร็จ', 'meta' => 'ส่งมอบอัตโนมัติ', 'value' => $orderCount],
    ['label' => 'ไอเท็มไม่ซ้ำ', 'meta' => 'ตรวจสอบโดยทีมงาน', 'value' => $productCount],
];

$categoryStmt = dd_q("SELECT * FROM category ORDER BY RAND() LIMIT 6");
$categories = $categoryStmt ? $categoryStmt->fetchAll(PDO::FETCH_ASSOC) : [];

$curatedCategories = [];
$crecom = dd_q("SELECT * FROM crecom WHERE recom_1 != 0 AND recom_2 != 0 LIMIT 1");
if ($crecom && $crecom->rowCount() === 1) {
    $data = $crecom->fetch(PDO::FETCH_ASSOC);
    for ($i = 1; $i <= 2; $i++) {
        $key = 'recom_' . $i;
        if (!empty($data[$key])) {
            $cat = dd_q("SELECT * FROM category WHERE c_id = ? LIMIT 1", [$data[$key]]);
            if ($cat && $cat->rowCount()) {
                $curatedCategories[] = $cat->fetch(PDO::FETCH_ASSOC);
            }
        }
    }
}
if (empty($curatedCategories) && !empty($categories)) {
    $curatedCategories = array_slice($categories, 0, min(2, count($categories)));
}

$featuredProducts = [];
$recom = dd_q("SELECT * FROM recom WHERE recom_1 != 0 AND recom_2 != 0 AND recom_3 != 0 AND recom_4 != 0 LIMIT 1");
if ($recom && $recom->rowCount()) {
    $data = $recom->fetch(PDO::FETCH_ASSOC);
    for ($i = 1; $i <= 6; $i++) {
        $key = 'recom_' . $i;
        if (!empty($data[$key])) {
            $product = dd_q("SELECT * FROM box_product WHERE id = ? LIMIT 1", [$data[$key]]);
            if ($product && $product->rowCount()) {
                $row = $product->fetch(PDO::FETCH_ASSOC);
                $stock = dd_q("SELECT COUNT(*) FROM box_stock WHERE p_id = ?", [$row['id']]);
                $row['stock_count'] = (int) ($stock->fetchColumn() ?? 0);
                $featuredProducts[] = $row;
            }
        }
    }
}
if (empty($featuredProducts)) {
    $fallbackProductStmt = dd_q("SELECT * FROM box_product ORDER BY RAND() LIMIT 6");
    while ($fallbackProductStmt && ($row = $fallbackProductStmt->fetch(PDO::FETCH_ASSOC))) {
        $stock = dd_q("SELECT COUNT(*) FROM box_stock WHERE p_id = ?", [$row['id']]);
        $row['stock_count'] = (int) ($stock->fetchColumn() ?? 0);
        $featuredProducts[] = $row;
    }
}

$newArrivalsStmt = dd_q("SELECT * FROM box_product ORDER BY id DESC LIMIT 4");
$newArrivals = $newArrivalsStmt ? $newArrivalsStmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<style>
    .category-card img {
        height: 200px;
        object-fit: cover;
    }

    .hero-carousel img {
        border-radius: var(--radius-md);
    }

    .support-links a {
        color: var(--brand-secondary);
        font-weight: 600;
    }
</style>

<section class="hero" data-aos="fade-up">
    <div class="hero-panel">
        <p class="eyebrow">ร้านค้ากล่องสุ่มที่เชื่อถือได้ตั้งแต่ <?= date('Y', strtotime('-2 year')); ?></p>
        <h1>ครบทุกความต้องการสำหรับทุกกล่องสุ่มพรีเมียม</h1>
        <p><?= e($config['des']); ?></p>
        <div class="hero-meta">
            <div class="mini-metric">
                <small>สมาชิก</small>
                <strong><?= format_number($memberCount); ?></strong>
            </div>
            <div class="mini-metric">
                <small>สต็อกพร้อมขาย</small>
                <strong><?= format_number($stockCount); ?></strong>
            </div>
            <div class="mini-metric">
                <small>คำสั่งซื้อ</small>
                <strong><?= format_number($orderCount); ?></strong>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2 flex-wrap">
            <a class="cta-btn" href="?page=shop">เข้าสู่หน้าร้าน</a>
            <a class="ghost-btn" href="?page=topup">เติมเงิน</a>
        </div>
    </div>
    <div class="hero-panel">
        <?php if (!empty($carouselItems)) : ?>
            <div id="heroShowcase" class="carousel slide hero-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php $active = true; ?>
                    <?php foreach ($carouselItems as $item) : ?>
                        <div class="carousel-item <?= $active ? 'active' : ''; ?>">
                            <img src="<?= e($item['link']); ?>" alt="Spotlight">
                        </div>
                        <?php $active = false; ?>
                    <?php endforeach; ?>
                </div>
                <?php if (count($carouselItems) > 1) : ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroShowcase" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroShowcase" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="empty-state">
                <h3>ส่วนโปรโมชันกำลังจะมาถึง</h3>
                <p>อัปโหลดแบนเนอร์จากหลังบ้านเพื่อโปรโมตแคมเปญได้ทันที</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="announcement-marquee" data-aos="fade-up" data-aos-delay="100">
    <span class="badge">อัปเดตล่าสุด</span>
    <marquee><?= e($annText); ?></marquee>
</section>

<section class="page-card" data-aos="fade-up" data-aos-delay="150">
    <div class="section-heading">
        <div>
            <p class="eyebrow">ภาพรวมระบบ</p>
            <h2>ข้อมูลสำคัญแบบเรียลไทม์</h2>
        </div>
        <a class="ghost-btn" href="?page=shop">ดูสินค้าทั้งหมด</a>
    </div>
    <div class="metrics-grid mt-4">
        <?php foreach ($statCards as $stat) : ?>
            <div class="metric-card">
                <small><?= e($stat['meta']); ?></small>
                <strong data-count-to="<?= (int) $stat['value']; ?>">0</strong>
                <p class="text-muted m-0"><?= e($stat['label']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php if (!empty($curatedCategories)) : ?>
    <section class="layout-grid" data-aos="fade-up" data-aos-delay="200">
        <?php foreach ($curatedCategories as $category) : ?>
            <article class="page-card" style="background-image: linear-gradient(145deg, rgba(5,8,20,0.9), rgba(5,8,20,0.6)), url('<?= e($category['img']); ?>'); background-size: cover; background-position: center;">
                <div class="glass-panel" style="background: rgba(3,6,17,0.75);">
                    <p class="eyebrow">หมวดหมู่เด่น</p>
                    <h3><?= e($category['c_name']); ?></h3>
                    <p><?= e($category['des']); ?></p>
                    <a class="cta-btn" href="?page=shop&category=<?= urlencode($category['c_name']); ?>">ดูหมวดนี้</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<section class="page-card" data-aos="fade-up" data-aos-delay="250">
    <div class="section-heading">
        <div>
            <p class="eyebrow">สินค้าที่คัดสรร</p>
            <h2>กล่องเด่นประจำวัน</h2>
        </div>
        <a class="ghost-btn" href="?page=shop">ดูทั้งหมด</a>
    </div>
    <div class="product-grid mt-4">
        <?php if (!empty($featuredProducts)) : ?>
            <?php foreach ($featuredProducts as $product) : ?>
                <article class="product-card">
                    <img src="<?= e($product['img']); ?>" alt="<?= e($product['name']); ?>">
                    <div class="content">
                        <div>
                            <p class="badge"><?= e($product['c_type']); ?></p>
                            <h3><?= e($product['name']); ?></h3>
                            <p class="text-muted"><?= e(mb_strimwidth($product['des'], 0, 120, '...')); ?></p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">เริ่มต้น</small>
                                <strong>฿<?= format_number($product['price']); ?></strong>
                            </div>
                            <?php if ((int) $product['stock_count'] > 0) : ?>
                                <span class="pill">สต็อก: <?= format_number($product['stock_count']); ?></span>
                            <?php else : ?>
                                <span class="pill status-warning">หมดสต็อก</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="?page=buy&id=<?= $product['id']; ?>" class="cta-btn w-100 text-center">ซื้อทันที</a>
                            <button class="ghost-btn w-100" onclick="detail(<?= (int) $product['id']; ?>)">รายละเอียด</button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="empty-state w-100">
                <p>ยังไม่มีสินค้าที่เลือกมาแสดง โปรดเพิ่มจากหน้าจัดการ</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($categories)) : ?>
    <section class="page-card" data-aos="fade-up" data-aos-delay="300">
        <div class="section-heading">
            <div>
                <p class="eyebrow">เลือกซื้อแบบหมวดหมู่</p>
                <h2>หมวดหมู่ทั้งหมด</h2>
            </div>
        </div>
        <div class="product-grid mt-4">
            <?php foreach ($categories as $category) : ?>
                <article class="product-card category-card">
                    <img src="<?= e($category['img']); ?>" alt="<?= e($category['c_name']); ?>">
                    <div class="content">
                        <h3><?= e($category['c_name']); ?></h3>
                        <p class="text-muted"><?= e(mb_strimwidth($category['des'], 0, 110, '...')); ?></p>
                        <a class="ghost-btn" href="?page=shop&category=<?= urlencode($category['c_name']); ?>">ดูหมวดนี้</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php else : ?>
    <section class="page-card" data-aos="fade-up" data-aos-delay="300">
        <div class="empty-state">
            <h3>ยังไม่มีหมวดหมู่</h3>
            <p>สร้างหมวดหมู่จากหลังบ้านเพื่อนำมาแสดงที่นี่</p>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($newArrivals)) : ?>
    <section class="page-card" data-aos="fade-up" data-aos-delay="350">
        <div class="section-heading">
            <div>
                <p class="eyebrow">สินค้าเข้าใหม่</p>
                <h2>ของเข้าใหม่</h2>
            </div>
        </div>
        <div class="product-grid mt-4">
            <?php foreach ($newArrivals as $item) : ?>
                <article class="product-card">
                    <img src="<?= e($item['img']); ?>" alt="<?= e($item['name']); ?>">
                    <div class="content">
                        <h3><?= e($item['name']); ?></h3>
                        <p class="text-muted"><?= e(mb_strimwidth($item['des'], 0, 100, '...')); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>฿<?= format_number($item['price']); ?></strong>
                            <a href="?page=buy&id=<?= $item['id']; ?>" class="ghost-btn">ดูสินค้า</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="split-card" data-aos="fade-up" data-aos-delay="400">
    <div>
        <p class="eyebrow">ใช้งานอย่างไร</p>
        <h3>ทำตาม 3 ขั้นตอนง่าย ๆ</h3>
        <ul class="step-flow mt-3">
            <li>1. เติมเงินกระเป๋าด้วยช่องทางที่ปลอดภัย</li>
            <li>2. เลือกกล่องที่ต้องการและยืนยันการซื้อ</li>
            <li>3. รับข้อมูลสินค้าทันทีภายในหน้านี้</li>
        </ul>
    </div>
    <div class="support-links">
        <p class="eyebrow">ต้องการความช่วยเหลือ?</p>
        <h3>ทีมงานพร้อมดูแลคุณ</h3>
        <p>คุยกับทีมงานหรืออ่านคำถามที่พบบ่อยเพื่อรับคำตอบในไม่กี่นาที</p>
        <div class="d-flex gap-2 flex-wrap mt-3">
            <a class="cta-btn" href="<?= e($config['contact']); ?>" target="_blank" rel="noopener">ติดต่อทีมงาน</a>
            <a class="ghost-btn" href="?page=question">ดูคำถามที่พบบ่อย</a>
        </div>
    </div>
</section>

<section class="page-card text-center" data-aos="fade-up" data-aos-delay="450">
    <p class="eyebrow">พร้อมเริ่มหรือยัง?</p>
    <h2>เริ่มเรื่องราวใหม่ในไม่กี่วินาที</h2>
    <p>ระบบอัตโนมัติจะซิงก์สินค้า ยอดเงิน และประวัติของคุณในทุกอุปกรณ์</p>
    <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
        <a class="cta-btn" href="?page=shop">เริ่มช้อป</a>
        <a class="ghost-btn" href="?page=profile">ไปยังบัญชีของฉัน</a>
    </div>
</section>

<script src="system/js/countup.js"></script>
