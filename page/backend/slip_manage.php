<style>
     .tblu-1 {
                border:none;
                padding:0.5rem;
                border-radius:1vh;
                color:#f4f4f4;
                background: linear-gradient(to right, #d51f1f 0%, #ff9f00 100%);
        }
    .ff {
        text-transform: uppercase;
            background: linear-gradient(to right, #d51f1f 0%, #ff9f00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration:none
    }
    .bg-from {
        background-color:#030508; 
        border:none;
        padding:0.5rem 1.5rem;
        margin-top:0.5rem;
        border-radius:1vh;
    }
           
</style>


<?php $bank = dd_q("SELECT * FROM bank WHERE 1")->fetch(PDO::FETCH_ASSOC); ?>
<div class="container-sm bg-glass  mt-2 shadow-sm p-4 mb-4 rounded" data-aos="fade-down">

    <center>
        <h3 class="ff">&nbsp;
        <i class="fa-duotone fa-money-check-pen fa-fade" style="--fa-primary-color: #db0f14; --fa-secondary-color: #ef292f;"></i>&nbsp;จัดการธนาคาร</h3>
    </center>


    <div class="col-lg-6 m-auto">
        <div class="mb-2 ">
            <div class="row">
                <div class="col me-2">
                    <p class="m-0 text-secondary">ชื่อ<span class="text-danger">*</span></p>
                    <input type="text" id="fname" class="form-control bg-from" value="<?php echo $bank['fname']; ?>">
                </div>
                <div class="col">
                    <p class="m-0 text-secondary">นามสกุล<span class="text-danger">*</span></p>
                    <input type="text" id="lname" class="form-control bg-from" value="<?php echo $bank['lname']; ?>">
                </div>
            </div>
        </div>
        <div class="mb-4 ">
            <p class="m-0 text-secondary">เลขบัญชี<span class="text-danger">*</span></p>
            <input type="text" id="bnum" class="form-control bg-from" value="<?php echo $bank['bnum']; ?>">
        </div>
        <div class="mb-4 ">
            <p class="m-0 text-secondary">Qrcode ภาพสลิปรับเงิน<span class="text-danger">*</span></p>
            <input type="text" id="qrcode" class="form-control bg-from" value="<?php echo $bank['qrcode']; ?>">
        </div>


        <div class="mb-2">
            <button class="tblu-1 w-100" id="btn_regis"> 
            <i class="fa-duotone fa-circle-check fa-fade" style="--fa-primary-color: #ffffff; --fa-secondary-color: #099922;"></i> บันทึกข้อมูล</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#btn_regis").click(function(e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('fname', $("#fname").val());
        formData.append('lname', $("#lname").val());
        formData.append('bnum', $("#bnum").val());
        formData.append('qrcode', $("#qrcode").val());
        $('#btn_regis').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: 'system/backend/slip_manage.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            result = res;
            console.log(result);
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: result.message
            }).then(function() {
                window.location = "?page=<?php echo $_GET['page']; ?>";
            });
        }).fail(function(jqXHR) {
            console.log(jqXHR);
            res = jqXHR.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: res.message
            })
            //console.clear();
            $('#btn_regis').removeAttr('disabled');
        });
    });
    $(document).ready(function() {
        $('#table').DataTable();
    });
</script>