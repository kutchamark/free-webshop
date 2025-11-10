<?php

$curl = curl_init();

$data = array(
    'keyapi' => $byshop_key,
);


curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://byshop.me/api/history-all', 
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_POSTFIELDS => http_build_query($data),
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
));

$response = curl_exec($curl);
curl_close($curl);
$load_packz = json_decode($response);


?>

<div class="modal fade text-white bg-glass" id="appInfoModal" tabindex="-1" role="dialog" aria-labelledby="appInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-white bg-glass" style="border-radius: 1vh;">
        
            <div class="modal-header" style="background-color:#131111;">
                <h5 class="modal-title ttcolor" id="appInfoModalLabel">ข้อมูล App Premium</h5>
                <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close"></button>
                
            </div>
            <div class="modal-body text-dark" id="appInfoContent">
                <!-- ข้อมูลจะถูกแสดงที่นี่ -->
            </div>
        </div>
    </div>
</div>




<style>
    .bb {
        margin-top:1rem;
    }
    .ttcolor {
            text-transform: uppercase;
            background: linear-gradient(to right, #ff4040 0%, #ff9e00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
</style>
<div class="container-fluid bb p-2 col-lg-10 col-10" style="background-color:#0e0e0e;">


        <div class="container-fluid ps-3 pe-3 pb-3" style="border-radius:1vh;">
        
        <div class="p-2">
            <h5 class="m-0"><h5 class=" mb-2 mt-2" style="color:#e3e3e3;">
            <i class="fa-duotone fa-clock-rotate-left fa-spin fa-spin-reverse" style="--fa-primary-color: #e3e3e3; font-size:14px; --fa-secondary-color: #15ed10;"></i>
             ประวัติการสั่งซื้อแอพทั้งหมด</h5>
             <p style="font-size:14px; color:#eaac13;">แอดมิน : <span><?php echo htmlspecialchars(strtoupper($user['username'])); ?></span></p>
        </div>

       
        <div class=" m-0" style="background-color:#00000000" >
            
        
            <div class="table-responsive">
                <table class="table  table-striped " id="table" >
                    <thead >
                    <tr >
                        <th scope="col" class="ps-3 text-center" style="color:#eaac13;">รายการ</th>
                        <th scope="col" class="ps-5 text-center" style="color:#eaac13;">สินค้า</th>
                        <th scope="col" class="ps-5 text-center" style="color:#eaac13;">แจ้งปัญหา</th>
                        <th scope="col" class="text-center"      style="color:#eaac13;">วันที่</th>
                    </tr>
                    </thead>
                    <tbody style="color:#2c2c2c;">
                        <?php 
                            foreach ($load_packz as $data) {
                        ?>
                            <tr>
                                <td style="color:#e8e8e8; font-size:13px;" ><?=$data->name;?></td>
                                <td style="color:#e8e8e8; font-size:13px;"><button class="btn  bg-main w-100 mb-2 view-info-btn text-white" data-toggle="modal" data-target="#appInfoModal" data-info="Email : <?= htmlspecialchars($data->email); ?> | Password : <?= htmlspecialchars($data->password); ?>"><i class="fa-regular fa-search"></i>&nbsp;ดูข้อมูล</button></td>
                                <td class="text-center " style=" color:#e8e8e8; font-size:13px;"><a style="width: 10rem;" type="button" class="btn btn-outline-secondary" target="_blank" data-toggle="modal" data-target="#report<?=$data->id;?>" href="#"><i class="fa fa-wrench"></i> แจ้งปัญหา</a></td>
                                <td class="text-center" style="color:#e8e8e8; font-size:13px;"><?=$data->time;?></td>
                            </tr>
                        <!-- report แจ้งปัญหา -->
                                <div class="modal fade " id="report<?=$data->id;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header ">
                                            <h5 class="modal-title ttcolor" id="exampleModalLabel"><i class="fa fa-wrench"></i> แจ้งปัญหา</h5>
                                            <button style="border:none; padding:0.5rem; border-radius:50px;" type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa-duotone fa-circle-xmark"></i>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                        <div class="container">
                                        <iframe frameborder="0" height="450" src=" https://report_product.byshop.me/api/report/?OrderID=<?=$data->id;?>" width="100%"></iframe>
                                        </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button style="background-color:#e55d5d; border:none;" type="button" class="btn btn-secondary" data-dismiss="modal">ปิดหน้านี้</button>
                                    
                                        </div>
                                        </div>
                                    </div>
                                </div>	
                        <?php  } ?>

                        </tbody>
                    </table>
                </div>
            </div>


        </div>


    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
    // Triggered when "ดูข้อมูล" button is clicked
    $(".view-info-btn").click(function() {
        // Get the info attribute from the button
        var appInfo = $(this).data("info");

        // Set the content of the modal body
        $("#appInfoContent").html(appInfo);
    });
</script>
