<?php
$activeCategory = isset($_GET['category']) ? trim($_GET['category']) : '';
$allCategoriesStmt = dd_q("SELECT * FROM category ORDER BY c_name ASC");
$allCategories = $allCategoriesStmt ? $allCategoriesStmt->fetchAll(PDO::FETCH_ASSOC) : [];

if ($activeCategory !== '') {
    $productStmt = dd_q("SELECT * FROM box_product WHERE c_type = ? ORDER BY id DESC", [$activeCategory]);
    $products = $productStmt ? $productStmt->fetchAll(PDO::FETCH_ASSOC) : [];
} else {
    $categoryStmt = dd_q("SELECT * FROM category ORDER BY RAND()");
    $categories = $categoryStmt ? $categoryStmt->fetchAll(PDO::FETCH_ASSOC) : [];
}
?>
<style>
    .category-card img {
        height: 200px;
        object-fit: cover;
    }
</style>

<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">ร้านค้า</span>
        <h1><?= $activeCategory ? 'หมวดหมู่: ' . e($activeCategory) : 'เลือกซื้อสินค้าทั้งหมด'; ?></h1>
        <p><?= $activeCategory ? 'รวมสินค้าทั้งหมดภายใต้หมวดหมู่ที่เลือก' : 'เลือกหมวดหมู่ที่สนใจหรือเข้าสู่หน้าร้านเพื่อสุ่มกล่องได้ทันที'; ?></p>
    </div>
    <div class="actions">
        <a href="?page=shop" class="ghost-btn<?= $activeCategory === '' ? ' is-active' : ''; ?>">ดูทุกหมวด</a>
        <a href="?page=topup" class="cta-btn">เติมเงิน</a>
    </div>
</section>

<?php if (!empty($allCategories)) : ?>
    <section class="page-card" data-aos="fade-up" data-aos-delay="100">
        <div class="chip-group">
            <?php foreach ($allCategories as $category) : ?>
                <a class="chip <?= $activeCategory === $category['c_name'] ? 'is-active' : ''; ?>"
                    href="?page=shop&category=<?= urlencode($category['c_name']); ?>">
                    <?= e($category['c_name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if ($activeCategory === '') : ?>
    <section class="page-card" data-aos="fade-up" data-aos-delay="150">
        <div class="section-heading">
            <div>
                <p class="eyebrow">หมวดหมู่สุ่มยอดนิยม</p>
                <h2>เริ่มจากหมวดที่คุณสนใจ</h2>
            </div>
        </div>
        <div class="product-grid mt-4">
            <?php if (!empty($categories)) : ?>
                <?php foreach ($categories as $category) : ?>
                    <article class="product-card category-card">
                        <img src="<?= e($category['img']); ?>" alt="<?= e($category['c_name']); ?>">
                        <div class="content">
                            <p class="badge">หมวดคัดพิเศษ</p>
                            <h3><?= e($category['c_name']); ?></h3>
                            <p class="text-muted"><?= e(mb_strimwidth($category['des'], 0, 110, '...')); ?></p>
                            <a class="ghost-btn" href="?page=shop&category=<?= urlencode($category['c_name']); ?>">ดูหมวดนี้</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-state w-100">
                    <h3>ยังไม่มีหมวดหมู่</h3>
                    <p>สร้างหมวดหมู่จากหลังบ้านเพื่อนำมาแสดงที่นี่</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php else : ?>
    <section class="page-card" data-aos="fade-up" data-aos-delay="150">
        <div class="section-heading">
            <div>
                <p class="eyebrow">หมวดหมู่</p>
                <h2><?= e($activeCategory); ?></h2>
            </div>
            <a class="ghost-btn" href="?page=shop">ล้างตัวกรอง</a>
        </div>
        <div class="product-grid mt-4">
            <?php if (!empty($products)) : ?>
                <?php foreach ($products as $row) : ?>
                    <?php
                    $stockStmt = dd_q("SELECT COUNT(*) FROM box_stock WHERE p_id = ?", [$row['id']]);
                    $count = (int) ($stockStmt->fetchColumn() ?? 0);
                    ?>
                    <article class="product-card">
                        <img src="<?= e($row['img']); ?>" alt="<?= e($row['name']); ?>">
                        <div class="content">
                            <h3><?= e($row['name']); ?></h3>
                            <p class="text-muted"><?= e(mb_strimwidth($row['des'], 0, 130, '...')); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">ราคา</small>
                                    <strong>฿<?= format_number($row['price']); ?></strong>
                                </div>
                                <?php if ($count > 0) : ?>
                                    <span class="pill">สต็อก: <?= format_number($count); ?></span>
                                <?php else : ?>
                                    <span class="pill status-warning">หมดสต็อก</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <a class="cta-btn w-100 text-center" href="?page=buy&id=<?= $row['id']; ?>">สั่งซื้อ</a>
                                <button class="ghost-btn w-100" onclick="detail(<?= (int) $row['id']; ?>)">รายละเอียด</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-state w-100">
                    <h3>ยังไม่มีสินค้าภายใต้หมวดหมู่นี้</h3>
                    <p>เพิ่มสินค้าใหม่หรือเลือกหมวดอื่น</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>
