<?php
$categoryStmt = dd_q("SELECT * FROM category ORDER BY c_id DESC");
$categories = $categoryStmt ? $categoryStmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<style>
    .category-admin {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .admin-surface {
        background: rgba(10, 13, 32, 0.85);
        border: 1px solid var(--admin-border, rgba(255,255,255,0.08));
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 30px 60px rgba(3, 4, 10, 0.45);
    }

    .category-layout {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .admin-headline {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .admin-headline h2 {
        margin: 0;
        font-size: 1.8rem;
    }

    .category-form .form-control,
    .category-form textarea {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #f8f9ff;
    }

    .category-form textarea {
        min-height: 120px;
    }

    .form-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .table-dark th {
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.85rem;
    }
</style>

<div class="category-admin">
    <div class="admin-surface">
        <div class="admin-headline">
            <div>
                <p>ร้านค้า</p>
                <h2>จัดการหมวดหมู่</h2>
                <p class="text-muted mb-0">สร้างหมวดหมู่ใหม่เพื่อจัดระเบียบสินค้า</p>
            </div>
            <button class="btn btn-light" id="categoryReset"><i class="fa-duotone fa-plus"></i> สร้างหมวดหมู่</button>
        </div>
    </div>

    <div class="category-layout">
        <div class="admin-surface category-form">
            <p class="text-muted mb-2">ฟอร์มหมวดหมู่</p>
            <h4 class="mb-3" id="categoryFormTitle">เพิ่มหมวดหมู่ใหม่</h4>
            <form id="categoryForm">
                <input type="hidden" id="category_mode" value="create">
                <input type="hidden" id="category_id">
                <div class="mb-3">
                    <label class="form-label" for="category_name">ชื่อหมวดหมู่ *</label>
                    <input type="text" class="form-control" id="category_name" placeholder="เช่น Roblox" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="category_image">ลิงก์รูปภาพ *</label>
                    <input type="url" class="form-control" id="category_image" placeholder="https://" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="category_description">รายละเอียด</label>
                    <textarea class="form-control" id="category_description" placeholder="อธิบายสั้น ๆ เกี่ยวกับหมวดหมู่"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="categorySubmit"><i class="fa-duotone fa-floppy-disk"></i> บันทึกหมวดหมู่</button>
                    <button type="button" class="btn btn-outline-light d-none" id="categoryCancel"><i class="fa-duotone fa-rotate-left"></i> ยกเลิกการแก้ไข</button>
                </div>
            </form>
        </div>
        <div class="admin-surface">
            <table id="categoryTable" class="table table-dark table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อหมวดหมู่</th>
                        <th>รายละเอียด</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) : ?>
                        <tr>
                            <td><?= (int) $category['c_id']; ?></td>
                            <td class="text-start">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= e($category['img']); ?>" alt="<?= e($category['c_name']); ?>" style="width:60px;height:60px;border-radius:16px;object-fit:cover;">
                                    <div>
                                        <strong><?= e($category['c_name']); ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-start"><?= nl2br(e($category['des'])); ?></td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <button class="btn btn-outline-warning btn-sm" onclick="editCategory(<?= (int) $category['c_id']; ?>)"><i class="fa-duotone fa-pen"></i> แก้ไข</button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteCategory(<?= (int) $category['c_id']; ?>, '<?= e($category['c_name']); ?>')"><i class="fa-duotone fa-trash"></i> ลบ</button>
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
    const categoryModeInput = document.getElementById('category_mode');
    const categoryIdInput = document.getElementById('category_id');
    const categoryCancel = document.getElementById('categoryCancel');
    const categorySubmit = document.getElementById('categorySubmit');
    const categoryFormTitle = document.getElementById('categoryFormTitle');

    function resetCategoryForm() {
        document.getElementById('categoryForm').reset();
        categoryModeInput.value = 'create';
        categoryIdInput.value = '';
        categoryCancel.classList.add('d-none');
        categorySubmit.innerHTML = '<i class="fa-duotone fa-floppy-disk"></i> บันทึกหมวดหมู่';
        categoryFormTitle.textContent = 'เพิ่มหมวดหมู่ใหม่';
    }

    document.getElementById('categoryReset').addEventListener('click', resetCategoryForm);
    categoryCancel.addEventListener('click', resetCategoryForm);

    $(document).ready(function() {
        $('#categoryTable').DataTable();
        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            const mode = categoryModeInput.value;
            const endpoint = mode === 'update' ? 'system/backend/category_update.php' : 'system/backend/category_insert.php';
            const formData = new FormData();
            formData.append('c_name', $('#category_name').val());
            formData.append('img', $('#category_image').val());
            formData.append('des', $('#category_description').val());
            if (mode === 'update') {
                formData.append('c_id', categoryIdInput.value);
            }
            categorySubmit.setAttribute('disabled', 'disabled');
            categorySubmit.textContent = 'กำลังบันทึก...';
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
                categorySubmit.removeAttribute('disabled');
                categorySubmit.innerHTML = mode === 'update' ? 'บันทึกการแก้ไข' : '<i class="fa-duotone fa-floppy-disk"></i> บันทึกหมวดหมู่';
            });
        });
    });

    function editCategory(id) {
        const formData = new FormData();
        formData.append('c_id', id);
        $.ajax({
            type: 'POST',
            url: 'system/backend/call/category_detail.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            $('#category_name').val(res.c_name);
            $('#category_image').val(res.img);
            $('#category_description').val(res.des);
            categoryModeInput.value = 'update';
            categoryIdInput.value = id;
            categoryCancel.classList.remove('d-none');
            categorySubmit.textContent = 'บันทึกการแก้ไข';
            categoryFormTitle.textContent = 'แก้ไขหมวดหมู่ #' + id;
            window.scrollTo({ top: document.querySelector('.category-form').offsetTop - 80, behavior: 'smooth' });
        }).fail(function(jqXHR) {
            const res = jqXHR.responseJSON || {};
            Swal.fire('เกิดข้อผิดพลาด', res.message || 'ไม่พบข้อมูล', 'error');
        });
    }

    function deleteCategory(id, name) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `ลบหมวดหมู่ ${name} จะกระทบกับสินค้าที่เชื่อมอยู่`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then(result => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('c_id', id);
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/category_del.php',
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
