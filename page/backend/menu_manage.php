<?php
date_default_timezone_set("Asia/Bangkok");
$today = date('Y-m-d 00:00:00');

$orderTodayStmt = dd_q("SELECT COUNT(*) FROM boxlog WHERE date >= ?", [$today]);
$orderToday = (int) ($orderTodayStmt ? $orderTodayStmt->fetchColumn() : 0);

$topupTodayStmt = dd_q("SELECT SUM(amount) FROM topup_his WHERE date >= ?", [$today]);
$topupToday = (float) ($topupTodayStmt ? $topupTodayStmt->fetchColumn() : 0);

$productTotal = (int) (dd_q("SELECT COUNT(*) FROM box_product")->fetchColumn() ?? 0);
$userTotal = (int) (dd_q("SELECT COUNT(*) FROM users")->fetchColumn() ?? 0);

$currentPage = $_GET['page'] ?? 'backend';

$adminNav = [
    'ภาพรวม' => [
        ['page' => 'backend', 'label' => 'แดชบอร์ด'],
        ['page' => 'website', 'label' => 'ตั้งค่าเว็บไซต์'],
        ['page' => 'carousel_manage', 'label' => 'จัดการแบนเนอร์'],
    ],
    'ร้านค้า' => [
        ['page' => 'product_manage', 'label' => 'จัดการสินค้า'],
        ['page' => 'category_manage', 'label' => 'จัดการหมวดหมู่'],
        ['page' => 'recom_manage', 'label' => 'แนะนำสินค้า'],
        ['page' => 'crecom_manage', 'label' => 'หมวดเด่น'],
        ['page' => 'game_manage', 'label' => 'จัดการมินิเกม'],
    ],
    'การเงิน' => [
        ['page' => 'slip_manage', 'label' => 'ตรวจสลิปโอน'],
        ['page' => 'code_manage', 'label' => 'รหัสเติมเงิน'],
        ['page' => 'backend_buy_history', 'label' => 'ประวัติการสั่งซื้อ'],
        ['page' => 'backend_topup_history', 'label' => 'ประวัติเติมเงิน'],
    ],
    'API / แอป' => [
        ['page' => 'apibyshop', 'label' => 'ตั้งค่า ByShop'],
        ['page' => 'apibyshop_his', 'label' => 'ประวัติ ByShop'],
    ],
];
?>

<link rel="stylesheet" href="system/css/admin.css">

<div class="backend-root">
    <div class="backend-hero">
        <div class="brand-card">
            <img src="<?= e($config['logo']); ?>" alt="logo">
            <div>
                <span>#<?= e($config['name']); ?></span>
                <h3><?= e($config['name']); ?></h3>
                <small><?= e($config['des']); ?></small>
            </div>
        </div>
        <div class="summary-card">
            <small>คำสั่งซื้อวันนี้</small>
            <strong><?= number_format($orderToday); ?></strong>
        </div>
        <div class="summary-card">
            <small>ยอดเติมวันนี้</small>
            <strong>฿<?= number_format($topupToday, 2); ?></strong>
        </div>
        <div class="summary-card">
            <small>จำนวนสินค้า</small>
            <strong><?= number_format($productTotal); ?></strong>
        </div>
        <div class="summary-card">
            <small>สมาชิกทั้งหมด</small>
            <strong><?= number_format($userTotal); ?></strong>
        </div>
    </div>

    <nav class="backend-nav">
        <?php foreach ($adminNav as $group => $links) : ?>
            <div class="backend-section-group">
                <div class="backend-section-title"><?= e($group); ?></div>
                <ul>
                    <?php foreach ($links as $link) : ?>
                        <li>
                            <a class="<?= $currentPage === $link['page'] ? 'is-active' : ''; ?>" href="?page=<?= $link['page']; ?>">
                                <?= e($link['label']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </nav>

    <div class="backend-content">
        <?php
        if (admin($user) && isset($_GET['page']) && $_GET['page'] == "backend") {
            require_once('page/backend/dashboard.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "user_edit") {
            require_once('page/backend/user_edit.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "category_manage") {
            require_once('page/backend/category.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "product_manage") {
            require_once('page/backend/product.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "stock_manage" && $_GET['id'] != "") {
            require_once('page/backend/stock.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "code_manage") {
            require_once('page/backend/code_manage.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "slip_manage") {
            require_once('page/backend/slip_manage.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "backend_buy_history") {
            require_once('page/backend/buy_history.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "backend_topup_history") {
            require_once('page/backend/topup_history.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "website") {
            require_once('page/backend/website.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "carousel_manage") {
            require_once('page/backend/carousel_manage.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "recom_manage") {
            require_once('page/backend/recom_manage.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "crecom_manage") {
            require_once('page/backend/crecom_manage.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "game_manage") {
            require_once('page/backend/game_manage.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "apibyshop") {
            require_once('page/backend/apibyshop.php');
        } elseif (admin($user) && isset($_GET['page']) && $_GET['page'] == "apibyshop_his") {
            require_once('page/backend/apibyshop_his.php');
        }
        ?>
    </div>
</div>
