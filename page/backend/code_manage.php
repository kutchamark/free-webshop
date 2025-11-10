<style>
    .tblu-1 {
            font-size:16px;
                border:none;
                padding:0.5rem;
                border-radius:1vh;
                color:#f4f4f4;
                background: linear-gradient(to right, #d51f1f 0%, #ff9f00 100%);
        }
</style>
<div class="container-fluid bb p-1 col-lg-8 col-10" style="background-color:#0e0e0e;">

        <div class="container-fluid ps-3 pe-3 pb-3" style="background-color:#100f0f; box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px; border-radius:1vh;">
        
        <div class="p-2">
            <h5 class="m-0"><h5 class=" mb-2 mt-2" style="color:#c6c6c6;">
                <i class="fa-duotone fa-clock-rotate-left fa-spin fa-spin-reverse" style="--fa-primary-color: #09a811; font-size:14px; --fa-secondary-color: #15ed10;"></i>
                    จัดการโค๊ด
                <span>
                    <button class="tblu-1 ms-2 me-2 mt-3 mb-0 col-5 col-lg-3" id="open_insert"> เพิ่มโค้ดใหม่</button>
                </span>

             </h5>
             

             <p style="font-size:14px; color:#eaac13;">สำหรับแอดมิน : <span><?php echo htmlspecialchars(strtoupper($user['username'])); ?></span></p>
        </div>

       
        <div class="m-0" style="background-color:#00000000">
            
        
            <div class="table-responsive">
                <table class="table  table-striped " id="table" >
                    
                    <thead >


                        <tr >

                            <th scope="col"  style="color:#eaac13;" class="text-center" class="sorting sorting_asc">id</th>
                            <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> โค้ด</th>
                            <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> รางวัล (เครดิต)</th>
                            <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> ใช้แล้ว</th>
                            <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> จำนวน</th>
                            <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> แก้ไข</th>
                            <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> ลบ</th>

                        </tr>

                    </thead>
                    <tbody style="color:#2c2c2c;">
                            <?php 
                                $get_user = dd_q("SELECT * FROM redeem ORDER BY id DESC");
                                while($row = $get_user->fetch(PDO::FETCH_ASSOC)){
                            ?>
                                <tr >
                                    <td class="text-center" style="color:#f9ad09"><?php echo $row['id'];?></td>
                                    <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo htmlspecialchars($row['code']);?></td>
                                    <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo htmlspecialchars($row['prize']);?></td>
                                    <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo number_format($row['count']);?></td>
                                    <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo number_format($row['max_count']);?></td>
                                    <td ><button class="btn btn-warning text-dark w-100" style="width : 130px!important" onclick="get_detail(<?php echo $row['id'];?>)"><i class="fa-solid fa-pencil"></i>&nbsp;แก้ไข</button></td>
                                    <td><button class="btn btn-danger text-white w-100"  style="width : 130px!important" onclick="del('<?php echo $row['id'];?>','<?php echo htmlspecialchars($row['username']);?>')"><i class="fa-solid fa-trash" ></i> ลบ</button></td>
                                </tr>

                            <?php
                                }
                            ?>
                    </tbody>
                    </table>
                    <!-- END -->


                </div>
            </div>

            <div class="modal fade" id="product_insert" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa-duotone fa-pencil"></i>&nbsp;&nbsp;แก้ไขสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 m-cent ">
                    <div class="mb-2">
                        <div class="mb-2">
                            <p class="text-secondary mb-1">กำหนดโค้ด <span class="text-danger">*</span></p>
                            <input type="text" id="code" class="form-control" value="">
                        </div>
                        <div class="mb-2">
                            <p class="text-secondary mb-1">จำนวนเงินที่ได้รับ <span class="text-danger">*</span></p>
                            <input type="text" id="reward" class="form-control" value="">
                        </div>
                        <div class="mb-2">
                            <p class="text-secondary mb-1">จำกัดจำนวนคนรับ <span class="text-danger">*</span></p>
                            <input type="text"  id="count"  class="form-control" >
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิดหน้านี้</button>
                <button type="button" class="btn btn-primary ps-4 pe-4" id="insert_btn" data-id="">เพิ่มโค้ด</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="product_detail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa-duotone fa-pencil"></i>&nbsp;&nbsp;แก้ไขสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 m-cent ">
                    <div class="mb-2">
                        <div class="mb-2">
                            <p class="text-secondary mb-1">กำหนดโค้ด <span class="text-danger">*</span></p>
                            <input type="text" id="upt_code" class="form-control" value="">
                        </div>
                        <div class="mb-2">
                            <p class="text-secondary mb-1">จำนวนเงินที่ได้รับ <span class="text-danger">*</span></p>
                            <input type="text" id="upt_reward" class="form-control" value="">
                        </div>
                        <div class="mb-2">
                            <p class="text-secondary mb-1">จำกัดจำนวนคนรับ <span class="text-danger">*</span></p>
                            <input type="text"  id="upt_count"  class="form-control" >
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_btn" data-id="">Save changes</button>
            </div>
        </div>
    </div>
</div>


        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#table').DataTable();
    });
</script>



        <script>
            $("#open_insert").click(function(){
                const myModal = new bootstrap.Modal('#product_insert', {
                    keyboard: false
                })
                myModal.show();
            });
            $("#save_btn").click(function(){
                var formData = new FormData();
                formData.append('id'  , $("#save_btn").attr("data-id") );
                formData.append('reward'  , $("#upt_reward").val());
                formData.append('count'  , $("#upt_count").val());
                formData.append('code'  , $("#upt_code").val());
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/code_update.php',
                    data:formData,
                    contentType: false,
                    processData: false,   
                }).done(function(res){
                    result = res;
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: result.message
                    }).then(function() {
                            window.location = "?page=<?php echo $_GET['page'];?>";
                    });
                }).fail(function(jqXHR){
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
            $("#insert_btn").click(function(){
                var formData = new FormData();
                formData.append('code'  , $("#code").val());
                formData.append('reward'  , $("#reward").val());
                formData.append('count'  , $("#count").val());
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/code_insert.php',
                    data:formData,
                    contentType: false,
                    processData: false,   
                }).done(function(res){
                    result = res;
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: result.message
                    }).then(function() {
                            window.location = "?page=<?php echo $_GET['page'];?>";
                    });
                }).fail(function(jqXHR){
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
            function get_detail(id){
                var formData = new FormData();
                formData.append('id'  , id);
                
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/call/code_detail.php',
                    data:formData,
                    contentType: false,
                    processData: false,   
                }).done(function(res){
                    console.log(res);
                    $("#upt_code").val(res.code);
                    $("#upt_reward").val(res.reward);
                    $("#upt_count").val(res.max_count);
                    $("#save_btn").attr("data-id" , id);
                    const myModal = new bootstrap.Modal('#product_detail', {
                        keyboard: false
                    })
                    myModal.show();
                }).fail(function(jqXHR){
                    console.log(jqXHR);
                    res = jqXHR.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: res.message
                    })
                    //console.clear();
                });
                
            }
            function del(id , username){
                Swal.fire({
                    title: 'ยืนยันที่จะลบ?',
                    text: "คุณแน่ใจหรอที่จะลบ   "+username,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ลบเลย'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = new FormData();
                        formData.append('id'  , id);
                        $.ajax({
                            type: 'POST',
                            url: 'system/backend/code_del.php',
                            data:formData,
                            contentType: false,
                            processData: false,   
                        }).done(function(res){
                            result = res;
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: result.message
                            }).then(function() {
                                    window.location = "?page=<?php echo $_GET['page'];?>";
                            });
                        }).fail(function(jqXHR){
                            console.log(jqXHR);
                            res = jqXHR.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: res.message
                            })
                            //console.clear();
                        });
                    }
                })
                
                
            }
        </script>
        
<script type="text/javascript">
    $(document).ready(function () {
        $('#table').DataTable();
    });
        $("#btn_regis").click(function(e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append('name'  , $("#site_name").val() );
            formData.append('bg'  , $("#site_bg").val() );
            formData.append('phone'  , $("#site_phone").val() );
            formData.append('main_color'  , $("#site_main_color").val() );
            formData.append('logo'  , $("#site_logo").val() );
            formData.append('sec_color'  , $("#site_sec_color").val() );
            formData.append('contact'  , $("#site_contact").val() );
            formData.append('des'  , $("#site_des").val() );
            $('#btn_regis').attr('disabled', 'disabled');
            $.ajax({
                type: 'POST',
                url: 'system/backend/website.php',
                data:formData,
                contentType: false,
                processData: false,   
            }).done(function(res){
                result = res;
                console.log(result);
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: result.message
                }).then(function() {
                        window.location = "?page=<?php echo $_GET['page'];?>";
                });
            }).fail(function(jqXHR){
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