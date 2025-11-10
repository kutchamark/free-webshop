<?php
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api_app_premium.byshop.me/api/product?id=' . urlencode($_GET['id'] ?? ''),
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
$packages = json_decode($response ?? '[]');
?>

<?php if (!empty($packages)) : ?>
    <?php foreach ($packages as $app) : ?>
        <section class="page-card" data-aos="fade-up">
            <div class="layout-grid">
                <div>
                    <img src="<?= e($app->img ?? ''); ?>" alt="<?= e($app->name ?? ''); ?>" class="w-100 rounded">
                </div>
                <div>
                    <p class="eyebrow">Premium app</p>
                    <h2><?= e($app->name ?? ''); ?></h2>
                    <div class="mt-3 text-muted">
                        <?= $app->product_info ?? ''; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                        <span class="pill">Stock <?= number_format((int) ($app->stock ?? 0)); ?></span>
                        <strong>฿<?= number_format(($app->price ?? 0) + $byshop_cost, 2); ?></strong>
                    </div>
                    <?php if (($app->stock ?? 0) >= 1) : ?>
                        <button class="cta-btn w-100 mt-3" data-buy-app data-id="<?= $app->id; ?>" data-name="<?= e($app->name ?? ''); ?>" data-price="<?= ($app->price ?? 0) + $byshop_cost; ?>">
                            Purchase license
                        </button>
                    <?php else : ?>
                        <button class="ghost-btn w-100 mt-3" disabled>Sold out</button>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
<?php else : ?>
    <section class="empty-state">
        <h3>Unable to load this app</h3>
        <p>Please refresh or return to the marketplace.</p>
    </section>
<?php endif; ?>

<script>
    $(document).on('click', '[data-buy-app]', function() {
        const $btn = $(this);
        const name = $btn.data('name');
        const price = Number($btn.data('price') || 0).toFixed(2);
        const formData = new FormData();
        formData.append('id', $btn.data('id'));

        Swal.fire({
            title: 'Confirm purchase?',
            text: name + ' · ฿' + price,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0ea5e9',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, continue'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'system/buyapp.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                }).done(function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'License delivered',
                        text: res.message,
                        footer: '<a href="?page=profile&subpage=myapp">Open my apps</a>'
                    });
                }).fail(function(jqXHR) {
                    const res = jqXHR.responseJSON || {};
                    Swal.fire({
                        icon: 'error',
                        title: 'Purchase failed',
                        text: res.message || 'Please try again later.'
                    });
                });
            }
        });
    });
</script>
