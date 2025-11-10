<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">Redeem</span>
        <h1>Convert your gift link</h1>
        <p>Paste the voucher URL to add credits instantly.</p>
    </div>
</section>

<section class="page-card" data-aos="fade-up" data-aos-delay="100">
    <div class="form-field mb-3">
        <label for="redeem-link">Gift or redeem link</label>
        <input type="text" id="redeem-link" class="form-control" placeholder="https://...">
    </div>
    <button class="cta-btn w-100" id="redeem-btn">Redeem now</button>
    <p class="text-muted mt-3">Links can only be used once. Double-check they come from our official partners.</p>
</section>

<script>
    $('#redeem-btn').click(function() {
        var formData = new FormData();
        formData.append('link', $('#redeem-link').val());
        $.ajax({
            type: 'POST',
            url: 'system/redeem.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            Swal.fire({
                icon: 'success',
                title: 'Redeemed',
                text: res.message
            }).then(function() {
                window.location = '?page=profile';
            });
        }).fail(function(jqXHR) {
            const res = jqXHR.responseJSON || {};
            Swal.fire({
                icon: 'error',
                title: 'Redeem failed',
                text: res.message || 'Please try again.'
            });
        });
    });
</script>
