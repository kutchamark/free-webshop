<?php echo file_get_contents("https://byshop.me/buy/otp_auto/script_smsotp.php"); ?>
<?php
$curl = curl_init();
$data = [
    'keyapi' => $byshop_key,
    'username_customer' => $user["id"],
];
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://byshop.me/api/history-all',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
]);
$response = curl_exec($curl);
curl_close($curl);
$apps = json_decode($response ?? '[]');
?>

<div class="table-card">
    <table id="apps-table" class="table align-middle text-white">
        <thead>
            <tr>
                <th>Application</th>
                <th>Credentials</th>
                <th>Support</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($apps)) : ?>
                <?php foreach ($apps as $app) : ?>
                    <tr>
                        <td><?= e($app->name ?? ''); ?></td>
                        <td>
                            <button class="ghost-btn w-100" data-app-info data-email="<?= e($app->email ?? ''); ?>" data-password="<?= e($app->password ?? ''); ?>">
                                View credentials
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="ghost-btn" data-report="https://report_product.byshop.me/api/report/?OrderID=<?= $app->id; ?>">
                                Report issue
                            </button>
                        </td>
                        <td><?= e($app->time ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="appCredentialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">App credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Email:</strong> <span id="cred-email"></span></p>
                <p><strong>Password:</strong> <span id="cred-pass"></span></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="appReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report this license</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="reportFrame" src="" width="100%" height="450" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#apps-table').DataTable();
    });

    $(document).on('click', '[data-app-info]', function() {
        $('#cred-email').text($(this).data('email'));
        $('#cred-pass').text($(this).data('password'));
        new bootstrap.Modal(document.getElementById('appCredentialModal')).show();
    });

    $(document).on('click', '[data-report]', function() {
        $('#reportFrame').attr('src', $(this).data('report'));
        new bootstrap.Modal(document.getElementById('appReportModal')).show();
    });
</script>
