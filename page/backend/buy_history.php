
<style>
    .bb {
        margin-top:-6rem;
    }
</style>
<div class="container-fluid bb p-0 col-lg-10 col-10 mt-4" style="background-color:#0e0e0e; border-radius:1vh;">
    <div class="container-sm m-auto p-2 pt-0 aos-init aos-animate" data-aos="zoom-in">


        <div class="container-fluid ps-3 pe-3 pb-3" style="border-radius:1vh;">
        
        <div class="p-2">
            <h5 class="m-0"><h5 class=" mb-2 mt-2" style="color:#e3e3e3;">
            <i class="fa-duotone fa-clock-rotate-left fa-spin fa-spin-reverse" style="--fa-primary-color: #e3e3e3; font-size:14px; --fa-secondary-color: #15ed10;"></i>
             ประวัติการสั่งซื้อ</h5>
             <p style="font-size:14px; color:#eaac13;">ของสมาชิก</span></p>
        </div>

       
        <div class="m-0" style="background-color:#00000000">
            
        
            <div class="table-responsive">
                <table class="table  table-striped " id="table" >
                    <thead >
                    <tr >
                        <th scope="col"  style="color:#eaac13;" class="text-center" class="sorting sorting_asc">#</th>
                        <th scope="col" class="ps-3 text-center" style="color:#eaac13;">ชื่อผู้ใช้</th>
                        <th scope="col" class="ps-3 text-center" style="color:#eaac13;">รายการ</th>
                        <th scope="col" class="ps-5 text-center" style="color:#eaac13;">สินค้า</th>
                        <th scope="col" class="ps-5 text-center" style="color:#eaac13;">จำนวนเงิน</th>
                        <th scope="col" class="text-center"      style="color:#eaac13;">วันที่</th>
                    </tr>
                    </thead>
                    <tbody style="color:#2c2c2c;">

                    
                            <?php 
                                $get_user = dd_q("SELECT * FROM boxlog ORDER BY date DESC");
                                while($row = $get_user->fetch(PDO::FETCH_ASSOC)){
                            ?>


                            <tr>
                                <th scope="row" class="text-center" style="color:#f9ad09">         <?php echo number_format($i);?></th>
                                <td style="color:#e8e8e8; font-size:13px;" >                       <?php echo htmlspecialchars($row['username']);?></td>
                                <td style="color:#e8e8e8; font-size:13px;" >                       <?php echo htmlspecialchars($row['category']);?></td>
                                <td style="color:#e8e8e8; font-size:13px;">                        <?php echo htmlspecialchars($row['prize_name']);?></td>
                                <td class="text-center" style=" color:#e8e8e8; font-size:13px;"><?php echo number_format($row['price']); ?></td>
                                <td class="text-center" style="color:#e8e8e8; font-size:13px;">    <?php echo htmlspecialchars($row['date']);?></td>
                            </tr>
                        <?php
                                $i++;
                            }
                        ?>

                        </tbody>
                    </table>
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