<?php
$categoryStmt = dd_q("SELECT c_id, c_name FROM category ORDER BY c_name ASC");
$categories = $categoryStmt ? $categoryStmt->fetchAll(PDO::FETCH_ASSOC) : [];

$productStmt = dd_q("SELECT p.*, (SELECT COUNT(*) FROM box_stock WHERE p_id = p.id) AS stock_total FROM box_product p ORDER BY p.id DESC");
$products = $productStmt ? $productStmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<style>
    .product-admin {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .admin-surface {
        background: rgba(10, 13, 32, 0.85);
        border: 1px solid var(--admin-border, rgba(255, 255, 255, 0.08));
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 30px 60px rgba(3, 4, 10, 0.45);
    }

    .admin-headline {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
    }

    .admin-headline h2 {
        margin: 0;
        font-size: 1.8rem;
    }

    .admin-headline p {
        margin: 0.25rem 0 0;
        color: var(--admin-muted, #a9b1d6);
    }

    .product-layout {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .product-form .form-label {
        font-weight: 600;
        color: var(--admin-muted, #9ba3c6);
    }

    .product-form input,
    .product-form textarea,
    .product-form select {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #f8f9ff;
    }

    .product-form textarea {
        min-height: 120px;
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .table-container {
        overflow-x: auto;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: rgba(255, 255, 255, 0.08);
        padding: 0.2rem 0.75rem;
        border-radius: 999px;
        font-size: 0.8rem;
    }

    .table-dark th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .btn-chip {
        border-radius: 999px;
        padding: 0.35rem 0.9rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: inherit;
        background: transparent;
    }

    @media (max-width: 640px) {
        .product-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="product-admin">
    <div class="admin-surface">
        <div class="admin-headline">
            <div>
                <p>ร้านค้า</p>
                <h2>จัดการสินค้า</h2>
                <p>เพิ่ม ปรับราคา และดูสต็อกสินค้าได้จากหน้าจอนี้</p>
            </div>
            <div>
                <button class="btn btn-light" id="productReset"><i class="fa-duotone fa-plus"></i> เพิ่มสินค้าใหม่</button>
            </div>
        </div>
    </div>

    <div class="product-layout">
        <div class="admin-surface product-form">
            <p class="text-muted mb-2">ฟอร์มสินค้า</p>
            <h4 class="mb-3" id="productFormTitle">สร้างสินค้าใหม่</h4>
            <form id="productForm">
                <input type="hidden" id="product_mode" value="create">
                <input type="hidden" id="product_id">
                <div class="mb-3">
                    <label class="form-label" for="product_name">ชื่อสินค้า *</label>
                    <input type="text" class="form-control" id="product_name" placeholder="เช่น กล่องสุ่มสุดคุ้ม" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="product_price">ราคาสินค้า *</label>
                        <input type="number" min="0" class="form-control" id="product_price" value="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="product_type">ประเภทสินค้า *</label>
                        <select class="form-select" id="product_type" required>
                            <option value="1">สุ่มรางวัล</option>
                            <option value="0">สินค้าปกติ</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="product_category">หมวดหมู่ *</label>
                    <select class="form-select" id="product_category" required>
                        <option value="">เลือกหมวดหมู่</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= e($category['c_name']); ?>"><?= e($category['c_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="product_image">ลิงก์รูปภาพ *</label>
                    <input type="url" class="form-control" id="product_image" placeholder="https://" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="product_description">รายละเอียด *</label>
                    <textarea class="form-control" id="product_description" placeholder="อธิบายสิ่งที่ลูกค้าจะได้รับ" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="productSubmit"><i class="fa-duotone fa-floppy-disk"></i> บันทึกสินค้า</button>
                    <button type="button" class="btn btn-outline-light d-none" id="productCancel"><i class="fa-duotone fa-rotate-left"></i> ยกเลิกการแก้ไข</button>
                </div>
            </form>
        </div>

        <div class="admin-surface table-container">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <p class="text-muted mb-0">รายการทั้งหมด</p>
                    <h5 class="m-0"><?= count($products); ?> ชิ้น</h5>
                </div>
                <div class="badge-soft">อัปเดตล่าสุด <?= date('d/m/Y H:i'); ?></div>
            </div>
            <table id="productTable" class="table table-dark table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>สินค้า</th>
                        <th>ราคา</th>
                        <th>หมวดหมู่</th>
                        <th>ประเภท</th>
                        <th>สต็อก</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $row) : ?>
                        <tr>
                            <td><?= (int) $row['id']; ?></td>
                            <td class="text-start">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= e($row['img']); ?>" alt="<?= e($row['name']); ?>" style="width:60px;height:60px;border-radius:16px;object-fit:cover;">
                                    <div>
                                        <strong><?= e($row['name']); ?></strong>
                                        <p class="text-muted small mb-0"><?= nl2br(e($row['des'])); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>฿<?= number_format((float) $row['price']); ?></td>
                            <td><?= e($row['c_type']); ?></td>
                            <td><?= $row['type'] == '1' ? 'สุ่มรางวัล' : 'สินค้าปกติ'; ?></td>
                            <td><?= number_format((int) $row['stock_total']); ?></td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <button class="btn btn-outline-warning btn-sm" onclick="editProduct(<?= (int) $row['id']; ?>)"><i class="fa-duotone fa-pen"></i> แก้ไข</button>
                                    <a class="btn btn-outline-info btn-sm" href="?page=stock_manage&id=<?= (int) $row['id']; ?>"><i class="fa-duotone fa-box"></i> สต็อก</a>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteProduct(<?= (int) $row['id']; ?>, '<?= e($row['name']); ?>')"><i class="fa-duotone fa-trash"></i> ลบ</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const productForm = document.getElementById('productForm');
    const productModeInput = document.getElementById('product_mode');
    const productIdInput = document.getElementById('product_id');
    const productCancel = document.getElementById('productCancel');
    const productSubmit = document.getElementById('productSubmit');
    const productFormTitle = document.getElementById('productFormTitle');

    function resetProductForm() {
        productForm.reset();
        productModeInput.value = 'create';
        productIdInput.value = '';
        productSubmit.innerHTML = '<i class="fa-duotone fa-floppy-disk"></i> บันทึกสินค้า';
        productCancel.classList.add('d-none');
        productFormTitle.textContent = 'สร้างสินค้าใหม่';
        document.getElementById('product_price').value = 0;
    }

    document.getElementById('productReset').addEventListener('click', resetProductForm);
    productCancel.addEventListener('click', resetProductForm);

    $(document).ready(function() {
        $('#productTable').DataTable();

        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            const mode = productModeInput.value;
            const endpoint = mode === 'update' ? 'system/backend/product_update.php' : 'system/backend/product_insert.php';
            const formData = new FormData();
            formData.append('name', $('#product_name').val());
            formData.append('price', $('#product_price').val());
            formData.append('type', $('#product_type').val());
            formData.append('c_type', $('#product_category').val());
            formData.append('img', $('#product_image').val());
            formData.append('des', $('#product_description').val());
            if (mode === 'update') {
                formData.append('id', productIdInput.value);
            }
            productSubmit.setAttribute('disabled', 'disabled');
            productSubmit.textContent = 'กำลังบันทึก...';
            $.ajax({
                type: 'POST',
                url: endpoint,
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(res) {
                Swal.fire('สำเร็จ', res.message, 'success').then(function() {
                    window.location.reload();
                });
            }).fail(function(jqXHR) {
                const res = jqXHR.responseJSON || {};
                Swal.fire('เกิดข้อผิดพลาด', res.message || 'ลองใหม่อีกครั้ง', 'error');
            }).always(function() {
                productSubmit.removeAttribute('disabled');
                productSubmit.textContent = mode === 'update' ? 'บันทึกการแก้ไข' : 'บันทึกสินค้า';
            });
        });
    });

    function editProduct(id) {
        const formData = new FormData();
        formData.append('id', id);
        $.ajax({
            type: 'POST',
            url: 'system/backend/call/product_detail.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            $('#product_name').val(res.name);
            $('#product_price').val(res.price);
            $('#product_type').val(res.type);
            $('#product_category').val(res.c_type);
            $('#product_image').val(res.img);
            $('#product_description').val(res.des);
            productModeInput.value = 'update';
            productIdInput.value = id;
            productSubmit.innerHTML = '<i class="fa-duotone fa-floppy-disk"></i> บันทึกการแก้ไข';
            productCancel.classList.remove('d-none');
            productFormTitle.textContent = 'แก้ไขสินค้า #' + id;
            window.scrollTo({ top: document.querySelector('.product-form').offsetTop - 80, behavior: 'smooth' });
        }).fail(function(jqXHR) {
            const res = jqXHR.responseJSON || {};
            Swal.fire('เกิดข้อผิดพลาด', res.message || 'ไม่พบข้อมูลสินค้า', 'error');
        });
    }

    function deleteProduct(id, name) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `ลบสินค้า ${name} และสต็อกทั้งหมด`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then(result => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/product_del.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                }).done(function(res) {
                    Swal.fire('ลบแล้ว', res.message, 'success').then(function() {
                        window.location.reload();
                    });
                }).fail(function(jqXHR) {
                    const res = jqXHR.responseJSON || {};
                    Swal.fire('เกิดข้อผิดพลาด', res.message || 'ไม่สามารถลบได้', 'error');
                });
            }
        });
    }
</script>
