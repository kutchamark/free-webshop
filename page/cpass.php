<div class="layout-grid">
    <div class="form-field">
        <label for="o_pass">Current password</label>
        <input type="password" id="o_pass" class="form-control" placeholder="Current password">
    </div>
    <div class="form-field">
        <label for="pass">New password</label>
        <input type="password" id="pass" class="form-control" placeholder="New password">
    </div>
    <div class="form-field">
        <label for="pass2">Confirm new password</label>
        <input type="password" id="pass2" class="form-control" placeholder="Repeat new password">
    </div>
</div>
<button id="btn_change_pass" class="cta-btn mt-3">Update password</button>

<script>
    $('#btn_change_pass').on('click', function(e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('o_pass', $('#o_pass').val());
        formData.append('pass', $('#pass').val());
        formData.append('pass2', $('#pass2').val());
        $('#btn_change_pass').attr('disabled', 'disabled').text('Updating...');
        $.ajax({
            type: 'POST',
            url: 'system/changepass.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Password updated',
                    text: res.message
                }).then(function() {
                    window.location = '?page=profile';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong',
                    text: res.message
                });
                $('#btn_change_pass').removeAttr('disabled').text('Update password');
            }
        }).fail(function(jqXHR) {
            const res = jqXHR.responseJSON || {};
            Swal.fire({
                icon: 'error',
                title: 'Server error',
                text: res.message || 'Please try again later.'
            });
            $('#btn_change_pass').removeAttr('disabled').text('Update password');
        });
    });
</script>
