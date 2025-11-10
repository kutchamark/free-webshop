<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">Create account</span>
        <h1>Join <?= e($config['name']); ?></h1>
        <p>One profile unlocks every box, redemption, and support channel.</p>
    </div>
</section>

<section class="page-card" data-aos="fade-up" data-aos-delay="100">
    <div class="layout-grid">
        <div>
            <div class="form-field mb-3">
                <label for="user">Username</label>
                <input type="text" id="user" class="form-control" placeholder="Pick a unique username">
            </div>
            <div class="form-field mb-3">
                <label for="pass">Password</label>
                <input type="password" id="pass" class="form-control" placeholder="••••••••">
            </div>
            <div class="form-field mb-3">
                <label for="pass2">Confirm password</label>
                <input type="password" id="pass2" class="form-control" placeholder="Repeat your password">
            </div>
            <div class="form-field mb-3">
                <label>Verification</label>
                <div id="capcha" class="g-recaptcha"></div>
            </div>
            <button class="cta-btn w-100" id="btn_regis">Create account</button>
            <p class="mt-3 text-muted">Already have access? <a href="?page=login">Sign in</a></p>
        </div>
        <div class="stacked-card">
            <p class="eyebrow">Benefits</p>
            <ul class="step-flow">
                <li>Secure wallet and instant drops.</li>
                <li>Native premium app downloads.</li>
                <li>24/7 order history & support.</li>
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

    $('#btn_regis').on('click', function(e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('user', $('#user').val());
        formData.append('pass', $('#pass').val());
        formData.append('pass2', $('#pass2').val());
        formData.append('captcha', grecaptcha.getResponse());
        $('#btn_regis').attr('disabled', 'disabled').text('Creating account...');
        $.ajax({
            type: 'POST',
            url: 'system/register.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            grecaptcha.reset();
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Account created',
                    text: res.message
                }).then(function() {
                    window.location = "?page=home";
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to register',
                    text: res.message
                });
                $('#btn_regis').removeAttr('disabled').text('Create account');
            }
        }).fail(function(jqXHR) {
            grecaptcha.reset();
            const res = jqXHR.responseJSON || {};
            Swal.fire({
                icon: 'error',
                title: 'Server error',
                text: res.message || 'Please try again later.'
            });
            $('#btn_regis').removeAttr('disabled').text('Create account');
        });
    });
</script>
