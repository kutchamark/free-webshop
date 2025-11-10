<style>
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

<div class="modal fade" id="product_insert" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa-duotone fa-pencil"></i>&nbsp;&nbsp;แก้ไขสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 m-cent ">
                    <div class="mb-2 ">
                        <p class="m-0">จำนวนสมาชิก <span class="text-danger">*</span></p>
                        <input type="text" id="s_count" class="form-control" value="<?php echo $static['m_count']; ?>">
                    </div>
                    <div class="mb-2 ">
                        <p class="m-0">จำนวนการสั่งซื้อ <span class="text-danger">*</span></p>
                        <input type="text" id="b_count" class="form-control" value="<?php echo $static['b_count']; ?>">
                    </div>
                    <div class="mb-2 ">
                        <center><small class="m-0 text-danger">* หากต้องการล้างค่า ให้กรอกเป็น 0 แล้วบันทึก * </small></center>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิดหน้านี้</button>
                <button type="button" class="btn btn-primary ps-4 pe-4" id="insert_btn" data-id="">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>



<div class="container-sm mt-2 border-2 shadow-sm p-4 rounded">
    <center>
        <h3 class="ff">&nbsp;<i class="fa-duotone fa-list-check fa-fade"></i>&nbsp;จัดการป๊อบอัพโชว์</h3>
    </center>
            <div class="container-sm mt-2 border-2 shadow-sm p-2 mb-0 rounded">
            <div class="row justify-content-between">
                    <div class="mb-2 col">
                        <div class="text-center">
                            <p class="m-0 text-secondary">รูป <span class="text-danger">*</span></p>
                            <input type="text" class="form-control bg-from form-control-color w-100" id="bganuuou" value="<?php echo $config['bg_ann']; ?>">
                        </div>
                    </div>
                <div class="mb-2 col">
                    <div class="text-center">
                        <p class="m-0 text-secondary">ข้อความ <span class="text-danger">*</span></p>
                        <input type="text" class="form-control bg-from form-control-color w-100" id="txanuuou" value="<?php echo $config['tx_ann']; ?>">
                    </div>
                </div>
            </div>
 </div>
 </div>



<div class="container-sm mt-0 border-2 shadow-sm p-4 mb-4 rounded">
    <center>
        <h3 class="ff">&nbsp;<i class="fa-duotone fa-list-check fa-fade"></i>&nbsp;จัดการเว็บไซต์</h3>
    </center>

    <div class="col-lg-6 m-auto">
        <div class="mb-4 mt-2">
            <button class="btn btn-outline-warning w-100 " id="open_insert">ตั้งค่าจำนวนโชว์บนเว็บไซต์</button>
        </div>
        <div class="mb-2 mt-2">
            <p class="m-0 text-secondary ">ชื่อเว็บไซต์ <span class="text-danger">*</span></p>
            <input type="text" id="site_name" class="form-control bg-from" value="<?php echo $config['name']; ?>">
        </div>


        <div class="row justify-content-between">
            <div class="mb-2 col">
                <p class="m-0 text-secondary ">โลโก้เว็บ <span class="text-danger">*</span></p>
                <input type="text" id="site_logo" class="form-control bg-from" value="<?php echo $config['logo']; ?>">
            </div>
            <div class="mb-2 col">
                <p class="m-0 text-secondary ">ภาพพื้นหลังเว็บ <span class="text-danger">*</span></p>
                <input type="text" id="site_bg" class="form-control bg-from" value="<?php echo $config['bg']; ?>">
            </div>
        </div>
        
        <div class="mb-2">
            <p class="m-0 text-secondary ">ประกาศ<span class="text-danger">*</span></p>
            <input type="text" id="ann" class="form-control bg-from" value="<?php echo $config['ann']; ?>">
        </div>     
        
        <div class="row justify-content-between">
            <div class="mb-5 col">
                <div class="text-center">
                    <p class="m-0 text-secondary">สีหลักของเว็บไซต์ <span class="text-danger">*</span></p>
                    <input type="color" class="form-control bg-from form-control-color w-100" id="site_main_color" value="<?php echo $config['main_color']; ?>">
                </div>
            </div>
            <div class="mb-5 col">
                <div class="text-center">
                    <p class="m-0 text-secondary">สีรองของเว็บไซต์ <span class="text-danger">*</span></p>
                    <input type="color" class="form-control bg-from form-control-color w-100" id="site_sec_color" value="<?php echo $config['sec_color']; ?>">
                </div>
            </div>
        </div>

        <div class="mb-2">
            <p class="m-0 text-secondary ">Webhook Line (Token) <span class="text-danger">*</span></p>
            <input type="text" id="webhook_dc" class="form-control bg-from" value="<?php echo $config['webhook_dc']; ?>">
        </div>


        
       
        <div class="mb-2">
            <p class="m-0 text-secondary ">ช่องทางการติดต่อ (ลิงค์) <span class="text-danger">*</span></p>
            <input type="text" id="site_contact" class="form-control bg-from" value="<?php echo $config['contact']; ?>">
        </div>
        
        <div class="mb-2 ">
            <p class="m-0 text-secondary ">คำอธิบายร้านค้า <span class="text-danger">*</span></p>
            <textarea id="site_des" rows="10" class="form-control bg-from"><?php echo $config['des']; ?></textarea>
        </div>

        <!-- phone angpao -->
        <div class="mb-2 ">
            <p class="m-0 text-secondary ">เบอร์รับเงิน (TrueWallet) <span class="text-danger">*</span></p>
            <input type="text" id="site_phone" class="form-control  bg-from" value="<?php echo $config['wallet']; ?>">
        </div>
        <div class="mb-2 ">
            <input class="form-check-input" type="checkbox" value="1" id="pc" <?php if ($config['fee'] == "on") {
                                                                                    echo "checked";
                                                                                } ?>>
            <label class="form-check-label mb-2" for="pc">
                ค่าธรรมเนียม เก็บ 2.3% ไม่เกิน 10 บาท
            </label>
        </div>
        <!-- phone angpao -->

        <div class="mb-2">
            <button class=" w-100 tblu-1" id="btn_regis" 
            style="background-color:#background: linear-gradient(to right, #d51f1f 0%, #ff9f00 100%);">บันทึกข้อมูล</button>
        </div>

        <!-- END -->
        <style>
            .tblu-1 {
                border:none;
                padding:0.5rem;
                border-radius:1vh;
                color:#f4f4f4;
                background: linear-gradient(to right, #d51f1f 0%, #ff9f00 100%);
        }
           
        </style>
    
    </div>
</div>

<script type="text/javascript">
    $("#insert_btn").click(function() {
        var formData = new FormData();
        formData.append('s_count', $("#s_count").val());
        formData.append('m_count', $("#m_count").val());
        formData.append('b_count', $("#b_count").val());
        $.ajax({
            type: 'POST',
            url: 'system/backend/static_udpate.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            result = res;
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
        });
        // $("#save_btn").attr("data-id") <- id user
    });
    $("#open_insert").click(function() {
        const myModal = new bootstrap.Modal('#product_insert', {
            keyboard: false
        })
        myModal.show();
    });
    $("#btn_regis").click(function(e) {
        e.preventDefault();
        var check;
        if ($('#pc').is(':checked')) {
            check = "on";
        } else {
            check = "off";
        }
        var formData = new FormData();
        formData.append('name', $("#site_name").val());
        formData.append('bg', $("#site_bg").val());
        formData.append('phone', $("#site_phone").val());
        formData.append('main_color', $("#site_main_color").val());
        formData.append('logo', $("#site_logo").val());
        formData.append('sec_color', $("#site_sec_color").val());
        formData.append('contact', $("#site_contact").val());
        formData.append('des', $("#site_des").val());
        formData.append('ann', $("#ann").val());
        formData.append('webhook_dc', $("#webhook_dc").val());
        formData.append('fee', check);
        formData.append('bg_ann', $("#bganuuou").val());
        formData.append('tx_ann', $("#txanuuou").val());
        $('#btn_regis').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: 'system/backend/website.php',
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
</script>