<?php 
    if(isset($_GET["id"])){
        $find = dd_q("SELECT * FROM box_product WHERE id = ? ", [$_GET['id']]);
        $pd =$find->fetch(PDO::FETCH_ASSOC);
        if($find->rowCount() >= 1){
     
?>

<style>
     .tblu-1 {
            font-size:16px;
                border:none;
                padding:0.5rem;
                border-radius:1vh;
                color:#f4f4f4;
                background: linear-gradient(to right, #d51f1f 0%, #ff9f00 100%);
                
        }
    .tblu-2 {
                border:none;
                padding:0.5rem;
                border-radius:1vh;
                color:#262626;
                background: linear-gradient(to right, #b5b5b5 0%, #fcfcfc 100%);
                
        }
    .ff {
        text-transform: uppercase;
            background: linear-gradient(to right, #d51f1f 0%, #ff9f00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration:none
    }

</style>

<div class="container-sm mt-2 col-lg-10 col-10 shadow-sm p-4 mb-4" style="background-color:#0e0e0e; border-radius:1vh;">

        <div class="p-2">
                <h5 class="m-0"><h5 class=" mb-2 mt-0" style="color:#c6c6c6;">
                <i class="fa-duotone fa-bag-shopping" style="--fa-primary-color: #09a811; font-size:14px; --fa-secondary-color: #15ed10;"></i>
                จัดการสินค้า
                    
                </h5>
             <p style="font-size:14px; color:#eaac13;">แอดมิน : <span><?php echo htmlspecialchars(strtoupper($user['username'])); ?></span></p>
                    <!-- BTN  -->
                    <span>
                        <button class="tblu-1 ms-2 me-2 mt-3 mb-0 col-12 col-lg-2" id="open_salt"> ตั้งค่าโอกาสเกลือ</button>
                    </span>
                    <span>
                        <button class="tblu-1 ms-2 me-2 mt-3 mb-0 col-12 col-lg-2" id="open_insert"> เพิ่มสต็อกใหม่</button>
                    </span>
            </div>

    <div class="table-responsive mt-3 ">
        <table id="table" class="table mt-2">
            <thead class="table-dark bg-dark ">
                <tr class="">
                    <th class="sorting sorting_asc">id</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> ของรางวัล</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> โอกาสออก</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> แก้ไข</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $get_user = dd_q("SELECT * FROM box_stock WHERE p_id = ?  ORDER BY id DESC",[$_GET['id']]);
                    while($row = $get_user->fetch(PDO::FETCH_ASSOC)){
                ?>
                    <tr >
                        <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo $row['id'];?></td>
                        <td class="text-center" style="font-size:13px; color:#e8e8e8" > <?php echo htmlspecialchars($row['username']);?></td>
                        <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo htmlspecialchars($row['password']);?></td>
                        <td ><button class="tblu-2 text-dark w-100" style="width : 130px!important" onclick="get_detail(<?php echo $row['id'];?>)"><i class="fa-solid fa-pencil"></i>&nbsp;แก้ไข</button></td>
                        <td><button class="btn btn-danger text-white w-100"  style="width : 130px!important" onclick="del('<?php echo $row['id'];?>','<?php echo htmlspecialchars($row['username']);?>')"><i class="fa-solid fa-trash" ></i> ลบ</button></td>
                    </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="product_insert" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa-duotone fa-circle-plus"></i>&nbsp;&nbsp;เพิ่มสต็อก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 m-cent ">
                    <div class="mb-2">
                        <div class="mb-2">
                            <p class="text-secondary mb-0">ใส่รางวัล <span class="text-danger">*</span></p>
                            <small class="mb-1 mt-0">การขึ้นบรรทัดใหม่จะนับเป็นรางวัลใหม่ทันที </small>
                            <textarea  id="stock_data" cols="30" rows="10" class="form-control" onkeyup="count_stock()"></textarea>
                            <p class="mb-0 mt-2">ตอนนี้นับได้ : <span class="text-danger" id="count_stock">0</span> ชิ้น </p>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิดหน้านี้</button>
                <button type="button" class="btn btn-primary ps-4 pe-4" id="insert_btn" data-id="<?php echo $pd["id"]?>">เพิ่มสินค้า</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="product_salt" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa-duotone fa-pencil"></i>&nbsp;&nbsp;แก้ไขโอกาสเกลือ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 m-cent ">
                    <div class="mb-2">
                        <div class="mb-2">
                            <p class="text-secondary mb-1">โอกาสเกลือ (สูงสุด 100 %) <span class="text-danger">*</span></p>
                                <span class="text-main" style="font-size:14px"> แนะนำตั้งเรท 98%-100% ต่ำกว่านี้ โอกาสออกค่อนข้างสูง</span>
                            <input type="text" id="p_salt" class="form-control" value="<?php echo $pd['percent'];?>">
                        </div>
                        <div class="mb-2">
                            <p class="text-secondary mb-1">ระบุของรางวัลหากได้เกลือ <span class="text-danger">*</span></p>
                            <input type="text" id="p_salt_prize" class="form-control" value="<?php echo $pd['salt_prize'];?>" placeholder="เช่น ไม่ได้รับรางวัล">
                        </div>
                    </div> 
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิดหน้านี้</button>
                <button type="button" class="btn btn-primary ps-4 pe-4" id="salt_btn" data-id="<?php echo $pd["id"];?>">เพิ่มสินค้า</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="product_detail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa-duotone fa-pencil"></i>&nbsp;&nbsp;แก้ไขรางวัล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 m-cent ">
                    <div class="mb-2">
                        <div class="mb-2">
                            <p class="text-secondary mb-0">แก้ไขรางวัล <span class="text-danger">*</span></p>
                            <input  id="upt_stock_data" type="text" class="form-control" >
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
        <script>
            function count_stock() {
                var array = $("#stock_data").val().split("\n");
                $("#count_stock").html(array.length);
            }
            $("#open_insert").click(function(){
                const myModal = new bootstrap.Modal('#product_insert', {
                    keyboard: false
                })
                myModal.show();
            });
            $("#open_salt").click(function(){
                const myModal = new bootstrap.Modal('#product_salt', {
                    keyboard: false
                })
                myModal.show();
            });
            $("#salt_btn").click(function(){
                var formData = new FormData();
                formData.append('id'  , $("#salt_btn").attr("data-id") );
                formData.append('salt'  , $("#p_salt").val() );
                formData.append('salt_prize'  , $("#p_salt_prize").val());
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/product_salt_update.php',
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
                            window.location = "?page=<?php echo $_GET['page'];?>&id=<?php echo $_GET['id'];?>";
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
            $("#save_btn").click(function(){
                var formData = new FormData();
                formData.append('id'  , $("#save_btn").attr("data-id") );
                formData.append('data'  , $("#upt_stock_data").val());
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/stock_update.php',
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
                            window.location = "?page=<?php echo $_GET['page'];?>&id=<?php echo $_GET['id'];?>";
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
                formData.append('id'  , $("#insert_btn").attr("data-id") );
                formData.append('data'  , $("#stock_data").val());
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/stock_insert.php',
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
                            window.location = "?page=<?php echo $_GET['page'];?>&id=<?php echo $_GET['id'];?>";
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
                    url: 'system/backend/call/stock_detail.php',
                    data:formData,
                    contentType: false,
                    processData: false,   
                }).done(function(res){
                    console.log(res);
                    $("#upt_stock_data").val(res.username);
                    $("#save_btn").attr('data-id', id);
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
                    text: "คุณแน่ใจหรอที่จะลบผู้ใช้  "+username,
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
                            url: 'system/backend/stock_del.php',
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
                                    window.location = "?page=<?php echo $_GET['page'];?>&id=<?php echo $_GET['id'];?>";
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
        let salt = <?php if($pd["type"] == "0"){echo "true";}else{echo "false";}?>;
        if(salt){
            $("#open_salt").removeClass("d-none");
        }
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
<?php 
       }
    }
?>