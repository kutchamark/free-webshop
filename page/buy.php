<?php
$id = isset($_GET['id']) ? trim($_GET['id']) : '';
if ($id === '') {
    echo '<section class="empty-state"><h3>Product not found</h3><p>Return to the <a href="?page=shop">shop</a> to continue browsing.</p></section>';
    return;
}

$pdshop = dd_q("SELECT * FROM box_product WHERE id = ? LIMIT 1", [$id]);
if (!$pdshop || $pdshop->rowCount() === 0) {
    echo '<section class="empty-state"><h3>Product not found</h3><p>Return to the <a href="?page=shop">shop</a> to continue browsing.</p></section>';
    return;
}

$row = $pdshop->fetch(PDO::FETCH_ASSOC);
$stockStmt = dd_q("SELECT COUNT(*) FROM box_stock WHERE p_id = ?", [$row['id']]);
$stockCount = (int) ($stockStmt->fetchColumn() ?? 0);
?>

<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">Box detail</span>
        <h1><?= e($row['name']); ?></h1>
        <p class="mb-0">Category: <strong><?= e($row['c_type']); ?></strong></p>
    </div>
    <div class="actions">
        <div class="page-card" style="min-width: 260px;">
            <small class="text-muted">Price</small>
            <h2 class="mb-2">฿<?= format_number($row['price']); ?></h2>
            <?php if ($stockCount > 0) : ?>
                <p class="badge-soft mb-3">Stock available · <?= format_number($stockCount); ?> left</p>
                <button class="cta-btn w-100" onclick="tobuy(<?= (int) $row['id']; ?>, '<?= e($row['name']); ?>', '<?= $stockCount; ?>', 'Instant delivery available');">Buy now</button>
            <?php else : ?>
                <p class="badge-soft status-warning mb-3">Out of stock</p>
                <button class="ghost-btn w-100" disabled>Coming soon</button>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="layout-grid" data-aos="fade-up" data-aos-delay="100">
    <div class="app-panel">
        <img src="<?= e($row['img']); ?>" alt="<?= e($row['name']); ?>" class="w-100 rounded mb-3">
        <div class="stacked-card">
            <p class="badge-dot">Automatic delivery</p>
            <p class="m-0">New credentials appear in your order history immediately after payment.</p>
        </div>
    </div>
    <div class="app-panel">
        <p class="eyebrow">What you receive</p>
        <h3>Drop overview</h3>
        <p><?= nl2br(e($row['des'])); ?></p>
        <div class="grid-2 mt-3">
            <div class="stacked-card">
                <small class="text-muted">Category</small>
                <strong><?= e($row['c_type']); ?></strong>
            </div>
            <div class="stacked-card">
                <small class="text-muted">Current stock</small>
                <strong><?= format_number($stockCount); ?></strong>
            </div>
        </div>
    </div>
</section>

<section class="split-card" data-aos="fade-up" data-aos-delay="150">
    <div>
        <p class="eyebrow">Need to know</p>
        <ul class="step-flow">
            <li>Items are single-use and removed from stock immediately after your purchase.</li>
            <li>Save the order number to review the credentials again inside your profile.</li>
            <li>Each drop is inspected manually before it is listed.</li>
        </ul>
    </div>
    <div>
        <p class="eyebrow">Still have questions?</p>
        <p>Chat with support before placing your order or browse the help center.</p>
        <div class="d-flex gap-2 flex-wrap mt-2">
            <a class="ghost-btn" href="<?= e($config['contact']); ?>" target="_blank" rel="noopener">Contact support</a>
            <a class="ghost-btn" href="?page=question">FAQ</a>
        </div>
    </div>
</section>
