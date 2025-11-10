<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

<style>
     .tblu-1 {
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
    .bg-from {
        background-color:#030508; 
        border:none;
        padding:0.5rem 1.5rem;
        margin-top:0.5rem;
        border-radius:1vh;
    }
           
</style>

<div class="container-sm mt-0  p-4 mb-4" data-aos="fade-down">

    <center>
        <h3 class="ff">จัดการ Api Byshop</h3>
    </center>
    
    <div class="col-lg-6 m-auto">

        <div class="mb-2 <?php echo $bg?> shadow-sm p-4 mb-4 rounded">
            
            <p class="m-0 text-secondary">เปิดใช้งาน / ปิด <span class="text-danger">*</span></p>
            <select class="form-control mb-2 bg-from"  id="st">
                <option value="on" <?php if ($byshop_status == "on") {echo "selected";} ?> style="color: #000">On</option>
                <option value="off" <?php if ($byshop_status == "off") {echo "selected";} ?> style="color: #000">Off</option>
            </select>

            <div class="mb-2 text-secondary">
                <p class="m-0  ">Api Key <span class="text-danger">*</span></p>
                <input type="text" id="apikey" class="form-control bg-from" value="<?php echo $byshop_key; ?>">
            </div>

            <div class="mb-2 text-secondary">
                <p class="m-0  ">ราคาที่จะบวกเพิ่ม <span class="text-danger">*</span></p>
                <input type="text" id="cost" class="form-control bg-from" value="<?php echo $byshop_cost; ?>">
            </div>
            


            <button class="tblu-1 w-100" id="subm">บันทึกข้อมูล</button>
            <button class="tblu-2 w-100 mt-2" id="check">เช็คยอดเงินของคุณ</button>
        </div>
        



    







        
    </div>
</div>
<script type="text/javascript">
    
    $("#subm").click(function(e) {
        e.preventDefault();
        var check;
        // if ($('#pc').is(':checked')) {
        //     check = "on";
        // } else {
        //     check = "off";
        // }
        var formData = new FormData();
        formData.append('st', $("#st").val());
        formData.append('apikey', $("#apikey").val());
        formData.append('cost', $("#cost").val());
        

        $('#btn_regis').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: 'system/backend/byshop_editinfo.php',
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




    $("#check").click(function(e) {
        e.preventDefault();
        var check;
        // if ($('#pc').is(':checked')) {
        //     check = "on";
        // } else {
        //     check = "off";
        // }
        var formData = new FormData();
        formData.append('st', $("#st").val());
        

        $('#btn_regis').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: 'system/byshop/checkmoney.php',
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