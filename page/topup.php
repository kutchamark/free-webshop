<?php
$bank = dd_q("SELECT * FROM bank WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">เติมเครดิต</span>
        <h1>เติมเงินเข้ากระเป๋า</h1>
        <p>ยอดคงเหลือปัจจุบัน · ฿<?= format_number($user['point'], 2); ?></p>
    </div>
</section>

<section class="layout-grid" data-aos="fade-up" data-aos-delay="100">
    <div class="app-panel">
        <p class="eyebrow">TrueMoney Gift</p>
        <h3>ระบบเติมอัตโนมัติ</h3>
        <p>ส่งกิฟต์ให้ <strong><?= e($config['wallet']); ?></strong> แล้ววางลิงก์ด้านล่างเพื่อรับเครดิตทันที</p>
        <div class="form-field mb-3">
            <label for="link">ลิงก์กิฟต์</label>
            <input type="text" id="link" class="form-control" placeholder="https://gift.truemoney.com/campaign/...">
        </div>
        <button id="topup_btn" class="cta-btn">ส่งลิงก์เติมเงิน</button>
        <p class="text-muted mt-3">ระบบจะตรวจสอบและเติมเครดิตให้โดยอัตโนมัติ</p>
    </div>
    <div class="app-panel">
        <p class="eyebrow">สลิปโอนเงิน</p>
        <h3>อัปโหลดสลิป</h3>
        <?php if ($bank) : ?>
            <p>โอนเข้าบัญชี <strong><?= e($bank['fname'] . ' ' . $bank['lname']); ?></strong> · <?= e($bank['accnum']); ?></p>
        <?php endif; ?>
        <div class="form-field mb-3">
            <label for="upload">รูปสลิป (PNG/JPG)</label>
            <input type="file" id="upload" class="form-control" accept="image/png,image/jpeg">
        </div>
        <p class="text-muted">ระบบจะสแกน QR PromptPay จากสลิปของคุณให้อัตโนมัติ</p>
    </div>
</section>

<section class="page-card" data-aos="fade-up" data-aos-delay="200">
    <div class="grid-2">
        <div>
            <p class="eyebrow">คำแนะนำ</p>
            <ul class="step-flow">
                <li>ลิงก์กิฟต์ TrueMoney หมดอายุเร็ว กรอกทันทีหลังส่ง</li>
                <li>รองรับเฉพาะสลิปที่มี QR พร้อมเพย์ชัดเจน</li>
                <li>หากมีปัญหา แจ้งทีมงานพร้อมเลขคำสั่งซื้อ</li>
            </ul>
        </div>
        <div>
            <p class="eyebrow">ทีมซัพพอร์ต</p>
            <a class="cta-btn" href="<?= e($config['contact']); ?>" target="_blank" rel="noopener">ติดต่อแอดมิน</a>
        </div>
    </div>
</section>

<script>
    $('#topup_btn').click(function() {
        var formData = new FormData();
        formData.append('link', $('#link').val());
        $.ajax({
            type: 'POST',
            url: 'system/topup.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            Swal.fire({
                icon: 'success',
                title: 'เติมเงินสำเร็จ',
                text: res.message
            }).then(function() {
                window.location = '?page=profile&subpage=topuphis';
            });
        }).fail(function(jqXHR) {
            const res = jqXHR.responseJSON || {};
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถประมวลผลได้',
                text: res.message || 'โปรดลองใหม่อีกครั้ง'
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
    function File2Base64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = (error) => reject(error);
        });
    }

    async function imageDataFromSource(source) {
        const image = Object.assign(new Image(), { src: source });
        await new Promise((resolve) => image.addEventListener('load', resolve));
        const canvas = document.createElement('canvas');
        canvas.width = image.width;
        canvas.height = image.height;
        const context = canvas.getContext('2d');
        context.imageSmoothingEnabled = false;
        context.drawImage(image, 0, 0);
        return context.getImageData(0, 0, image.width, image.height);
    }

    $(function() {
        $('#upload').change(async function() {
            const input = this;
            const file = input.files[0];
            if (!file) {
                return;
            }
            Swal.fire({
                icon: 'info',
                title: 'กำลังตรวจสอบสลิป...',
                showConfirmButton: false,
                allowOutsideClick: false
            });
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['png', 'jpg', 'jpeg'].includes(ext)) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์ไม่รองรับ',
                    text: 'กรุณาอัปโหลดสลิป PNG หรือ JPG'
                });
                return;
            }
            try {
                const base64 = await File2Base64(file);
                const imageData = await imageDataFromSource(base64);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code && code.data) {
                    const formData = new FormData();
                    formData.append('qrcode', code.data);
                    $.ajax({
                        type: 'POST',
                        url: 'system/slip.php',
                        data: formData,
                        contentType: false,
                        processData: false,
                    }).done(function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ยืนยันสลิปแล้ว',
                            text: res.message
                        }).then(function() {
                            window.location = '?page=profile&subpage=topuphis';
                        });
                    }).fail(function(jqXHR) {
                        const res = jqXHR.responseJSON || {};
                        Swal.fire({
                            icon: 'error',
                            title: 'สลิปไม่ผ่าน',
                            text: res.message || 'กรุณาติดต่อแอดมิน'
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่พบ QR',
                        text: 'กรุณาอัปโหลดสลิปที่เห็น QR ชัดเจน'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'ประมวลผลไม่สำเร็จ',
                    text: err.message || 'โปรดลองใหม่อีกครั้ง'
                });
            }
        });
    });
</script>
