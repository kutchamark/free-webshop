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
                จัดการสมาชิก
                    
                </h5>
                <p style="font-size:14px; color:#eaac13;">แอดมิน : <span><?php echo htmlspecialchars(strtoupper($user['username'])); ?></span></p>
            </div>

    <div class="table-responsive ">
        <table id="table" class="text-center mt-4">
            <thead class="table-dark bg-dark">
                <tr class="text-center">
                    <th class="sorting sorting_asc">id</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> ชื่อผู้ใช้</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> เงินคงเหลือ</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> ยอดการเติม</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> แก้ไข</th>
                    <th scope="col" class="ps-3 text-center" style="color:#eaac13;"> ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $get_user = dd_q("SELECT * FROM users ORDER BY id DESC");
                while ($row = $get_user->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <tr>
                        <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo $row['id']; ?></td>
                        <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo number_format($row['point']); ?></td>
                        <td class="text-center" style="font-size:13px; color:#e8e8e8"><?php echo htmlspecialchars($row['total']); ?></td>
                        <td class="text-white"><button class="tblu-1 text-dark w-100" onclick="get_detail(<?php echo $row['id']; ?>)"><i class="fa-solid fa-pencil"></i>&nbsp;แก้ไข</button></td>
                        <td class="text-white"><button class="btn btn-danger text-white w-50" onclick="del('<?php echo $row['id']; ?>','<?php echo htmlspecialchars($row['username']); ?>')"><i class="fa-solid fa-trash"></i> ลบ</button></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- <div class="d-flex justify-content-between">
        <div>
            <a class="btn btn-primary <?php if(!isset($_GET['item']) || $_GET['item'] == 1){echo"disabled";}?>" href="?page=backend&type=<?php echo $_GET['type'];?>&item=<?php if(!isset($_GET['item']) || $_GET['item'] == 1){echo"1";}else{echo $_GET['item'] - 1;}?>" >หน้าที่แล้ว</a>
            <a class="text-primary ms-2" href="?page=backend&type=<?php echo $_GET['type'];?>&item=1" >หน้าแรก</a>
            
        </div>
        <p class="text-primary align-self-center mb-0"><?php echo htmlspecialchars($current_page);?></p>
        <div>
            <a class="text-primary me-2" href="?page=backend&type=<?php echo $_GET['type'];?>&item=<?php echo $total_pages?>" >หน้าสุดท้าย</a>
            <a class="btn btn-primary <?php if(($current_page + 1) > $total_pages){echo"disabled";}?>" href="?page=backend&type=<?php echo $_GET['type'];?>&item=<?php echo $current_page + 1;?>">หน้าต่อไป</a>
        </div>
    </div> -->
</div>
<div class="modal fade" id="product_detail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa-duotone fa-pencil"></i>&nbsp;&nbsp;แก้ไขผู้ใช้</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 m-cent ">
                    <div class="mb-2">
                        <div class="mb-2">
                            <p class="text-secondary mb-1">ชื่อผู้ใช้งาน <span class="text-danger">*</span></p>
                            <input type="text" id="username" class="form-control" disabled="disabled" value="username">
                        </div>
                        <div class="mb-2">
                            <p class="text-secondary mb-1">รหัสผ่าน <span class="text-danger">*</span></p>
                            <input type="text" id="password" class="form-control" value="" placeholder="ปล่อยว่างหากไม่ต้องการแก้ไข">
                        </div>
                        <div class="mb-2">
                            <p class="text-secondary mb-1">เงินคงเหลือ <span class="text-danger">*</span></p>
                            <input type="text" id="points" class="form-control" value="0">
                        </div>
                        <div class="mb-2">
                            <p class="text-secondary mb-1">ยอดการเติม <span class="text-danger">*</span></p>
                            <input type="text" id="total" class="form-control" value="0">
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
    $("#save_btn").click(function() {
        var formData = new FormData();
        formData.append('id', $("#save_btn").attr("data-id"));
        formData.append('password', $("#password").val());
        formData.append('total', $("#total").val());
        formData.append('point', $("#points").val());
        $.ajax({
            type: 'POST',
            url: 'system/backend/user_setting.php',
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

    function get_detail(id) {
        var formData = new FormData();
        formData.append('id', id);

        $.ajax({
            type: 'POST',
            url: 'system/backend/call/user_detail.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(res) {
            $("#username").val(res.username);
            $("#points").val(res.points);
            $("#total").val(res.total);
            $("#save_btn").attr("data-id", id);
            const myModal = new bootstrap.Modal('#product_detail', {
                keyboard: false
            })
            myModal.show();
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

    }

    function del(id, username) {
        Swal.fire({
            title: 'ยืนยันที่จะลบ?',
            text: "คุณแน่ใจหรอที่จะลบผู้ใช้  " + username,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ลบเลย'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/user_del.php',
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
            }
        })


    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table').DataTable();
    });

    $("#btn_regis").click(function(e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('name', $("#site_name").val());
        formData.append('bg', $("#site_bg").val());
        formData.append('phone', $("#site_phone").val());
        formData.append('main_color', $("#site_main_color").val());
        formData.append('logo', $("#site_logo").val());
        formData.append('sec_color', $("#site_sec_color").val());
        formData.append('contact', $("#site_contact").val());
        formData.append('des', $("#site_des").val());
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