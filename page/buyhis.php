<?php
$q = dd_q("SELECT * FROM boxlog WHERE uid = ? ORDER BY id DESC", [$_SESSION['id']]);
?>
<div class="table-card">
    <table id="orders-table" class="table align-middle text-white">
        <thead>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Item</th>
                <th>Price</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php while ($row = $q->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr>
                    <td><?= number_format($i++); ?></td>
                    <td><?= e($row['category']); ?></td>
                    <td><?= e($row['prize_name']); ?></td>
                    <td>฿<?= format_number($row['price']); ?></td>
                    <td><?= e($row['date']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#orders-table').DataTable();
    });
</script>
