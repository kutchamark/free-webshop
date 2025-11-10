<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">เข้าสู่ระบบ</span>
        <h1>ยินดีต้อนรับกลับ</h1>
        <p>เข้าถึงการสั่งซื้อ การเติมเงิน และประวัติทั้งหมดของคุณได้อย่างปลอดภัยในที่เดียว</p>
    </div>
</section>

<section class="page-card" data-aos="fade-up" data-aos-delay="100">
    <div class="layout-grid">
        <div>
            <div class="form-field mb-3">
                <label for="user">ชื่อผู้ใช้</label>
                <input type="text" id="user" class="form-control" placeholder="กรอกชื่อผู้ใช้">
            </div>
            <div class="form-field mb-3">
                <label for="pass">รหัสผ่าน</label>
                <input type="password" id="pass" class="form-control" placeholder="••••••••">
            </div>
            <div class="form-field mb-3">
                <label>ยืนยันความปลอดภัย</label>
                <div id="capcha" class="g-recaptcha"></div>
            </div>
            <button class="cta-btn w-100" id="btn_login">เข้าสู่ระบบ</button>
            <p class="mt-3 text-muted">ยังไม่มีบัญชี? <a href="?page=register">สมัครสมาชิก</a></p>
        </div>
        <div class="stacked-card">
            <p class="eyebrow">ทำไมต้องเข้าสู่ระบบ?</p>
            <ul class="step-flow">
                <li>ติดตามการเปิดกล่องทั้งหมดได้ละเอียด</li>
                <li>ดาวน์โหลดสินค้าดิจิทัลและแอปพรีเมียมได้ทันที</li>
                <li>บริหารยอดเงิน โปรไฟล์ และใบเสร็จให้อยู่ในที่เดียว</li>
            </ul>
        </div>
    </div>
</section>

<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
<script>
    var onloadCallback = function() {
        grecaptcha.render('capcha', {
            'sitekey': '<?= $conf['sitekey']; ?>'
        });
    };

    $(document).on('click', '#btn_login', function(e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('user', $("#user").val());
        formData.append('pass', $("#pass").val());
        formData.append('captcha', grecaptcha.getResponse());
        $('#btn_login').attr('disabled', 'disabled').text('กำลังเข้าสู่ระบบ...');
        $.ajax({
            type: 'POST',
            url: 'system/login.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            grecaptcha.reset();
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'เข้าสู่ระบบสำเร็จ',
                    text: res.message
                }).then(function() {
                    window.location = "?page=home";
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถเข้าสู่ระบบได้',
                    text: res.message
                });
                $('#btn_login').removeAttr('disabled').text('เข้าสู่ระบบ');
            }
        }).fail(function() {
            grecaptcha.reset();
            Swal.fire({
                icon: 'error',
                title: 'เซิร์ฟเวอร์มีปัญหา',
                text: 'โปรดลองอีกครั้งในภายหลัง'
            });
            $('#btn_login').removeAttr('disabled').text('เข้าสู่ระบบ');
        });
    });
</script>
