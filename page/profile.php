<?php
$subpage = $_GET['subpage'] ?? 'cpass';
$tabs = [
    'cpass' => 'ความปลอดภัย',
    'buyhis' => 'คำสั่งซื้อ',
    'topuphis' => 'เติมเงิน',
    'myapp' => 'แอปของฉัน'
];
?>

<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">พื้นที่สมาชิก</span>
        <h1><?= e($user['username']); ?></h1>
        <p>ยอดเงิน: ฿<?= format_number($user['point'], 2); ?> · ใช้ไปทั้งหมด: ฿<?= format_number($user['total'], 2); ?></p>
    </div>
</section>

<section class="page-card" data-aos="fade-up" data-aos-delay="100">
    <div class="chip-group mb-4">
        <?php foreach ($tabs as $slug => $label) : ?>
            <a class="chip <?= $subpage === $slug ? 'is-active' : ''; ?>" href="?page=profile&subpage=<?= $slug; ?>">
                <?= e($label); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div>
        <?php
        if ($subpage === 'buyhis') {
            require_once('page/buyhis.php');
        } elseif ($subpage === 'topuphis') {
            require_once('page/topuphis.php');
        } elseif ($subpage === 'myapp') {
            require_once('page/myapp.php');
        } else {
            require_once('page/cpass.php');
        }
        ?>
    </div>
</section>
