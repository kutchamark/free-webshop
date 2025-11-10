<?php
$q = dd_q("SELECT * FROM topup_his WHERE uid = ? ORDER BY id DESC", [$_SESSION['id']]);
?>
<div class="table-card">
    <table id="topup-table" class="table align-middle text-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $q->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr>
                    <td><?= e($row['id']); ?></td>
                    <td>฿<?= format_number($row['amount'], 2); ?></td>
                    <td><?= e($row['date']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#topup-table').DataTable();
    });
</script>
