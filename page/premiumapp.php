<?php
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api_app_premium.byshop.me/api/product',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
]);
$response = curl_exec($curl);
curl_close($curl);
$apps = json_decode($response ?? '[]');
?>

<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">Premium apps</span>
        <h1>Official subscriptions delivered instantly.</h1>
        <p>Pick a plan, pay with your wallet, and receive the credentials directly in your dashboard.</p>
    </div>
</section>

<section class="page-card" data-aos="fade-up" data-aos-delay="100">
    <div class="product-grid">
    <?php if (!empty($apps)) : ?>
        <?php foreach ($apps as $app) : ?>
            <article class="product-card">
                <img src="<?= e($app->img ?? ''); ?>" alt="<?= e($app->name ?? ''); ?>">
                <div class="content">
                    <p class="badge">Stock <?= number_format((int) ($app->stock ?? 0)); ?></p>
                    <h3><?= e($app->name ?? ''); ?></h3>
                    <p class="text-muted">Status: <?= e($app->status ?? 'N/A'); ?></p>
                    <strong>฿<?= number_format(($app->price ?? 0) + $byshop_cost, 2); ?></strong>
                    <?php if (($app->stock ?? 0) > 0) : ?>
                        <a class="cta-btn mt-2 text-center" href="?page=buyapp&amp;id=<?= $app->id; ?>">Purchase</a>
                    <?php else : ?>
                        <button class="ghost-btn mt-2" disabled>Sold out</button>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="empty-state">
            <h3>No premium apps available</h3>
            <p>Try reloading or check again later.</p>
        </div>
    <?php endif; ?>
    </div>
</section>
