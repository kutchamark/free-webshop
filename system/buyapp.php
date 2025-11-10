<?php
require_once 'a_func.php';


function dd_return($status , $message)
{
    if ($status) {

        $json = ['message' => $message];
        http_response_code(200);
        die(json_encode($json));
    } else {
        $json = ['message' => $message];
        http_response_code(400);
        die(json_encode($json));
    }
}

// //////////////////////////////////////////////////////////////////////////

function buy($id , $key , $userid) {
    // URL ของ API
    $apiUrl = "https://byshop.me/api/buy";

    // ข้อมูลที่ต้องส่ง
    $data = array(
        'id' => $id,
        'keyapi' => $key,
        'username_customer' => $userid
    );

    // กำหนดตัวเลือกของ cURL
    $options = array(
        CURLOPT_URL => $apiUrl,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
    );

    // สร้าง cURL handle
    $ch = curl_init();
    curl_setopt_array($ch, $options);

    // ทำ HTTP POST request
    $response = curl_exec($ch);

    $resultArray = json_decode($response, true);

    return $resultArray;
}

function getinfo($id) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://byshop.me/api/product?id='.$id, 
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $load_packz = json_decode($response);

	foreach ($load_packz as $data) {
        return $data;
    }
}

header('Content-Type: application/json; charset=utf-8;');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['id'])) {
        $appinfo = getinfo($_POST['id']);
        $ap =  $appinfo->price;
        $img =  $appinfo->img;
        $allprice = $ap + $byshop_cost;

        if ($user['point'] >= $allprice) {
            $api = buy($_POST['id'] , $byshop_key , $_SESSION['id']);
            $status = $api['status'];
            $mes = $api['message'];
            $name = $api['name'];

            if ($status == 'success') {
                                    $f = dd_q("SELECT * FROM setting");
                                    $dt = $f->fetch(PDO::FETCH_ASSOC);
                                    $sToken = $dt['webhook_dc'];
                                    $ip = $_SERVER['REMOTE_ADDR'];
                                    $sMessage = "มีรายการสั่งซื้อแอพพรีเมียมร้าน : ".$dt['name']. "\n";
                                    $sMessage .= "ชื่อสินค้า : " .$name. " \n";
                                    $sMessage .= "รายละเอียดสินค้า : " .$info. " \n";
                                    $sMessage .= "ผู้ใช้ : " .$user['username']. " \n";
                                    $sMessage .= "ราคา : ".$allprice. " บาท \n";
                                    $chOne = curl_init(); 
                                    curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 
                                    curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 
                                    curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 
                                    curl_setopt( $chOne, CURLOPT_POST, 1); 
                                    curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=".$sMessage); 
                                    $headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$sToken.'', );
                                    curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 
                                    curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1); 
                                    $result = curl_exec( $chOne );


                $update = dd_q("UPDATE users SET point = point - ? WHERE id = ?", [$allprice , $_SESSION['id']]);
                if ($update){
                    dd_return(true , $mes);
                } else {
                    dd_return(false , 'SQL ผิดพลาด');
                }
            } else {
                if ($mes == 'ยอดเงินไม่เพียงพอ') {
                    dd_return(false , 'ยอดเงินของเจ้าของร้านไม่เพียงพอ โปรดติดต่อเจ้าของร้าน');
                } else {
                    dd_return(false , $mes);
                }
            }
        } else {
            dd_return(false , 'เงินไม่เพียงพอ');
        }
        
    } else {
        dd_return(false, "error",  "เข้าสู่ระบบก่อนทำรายการ");
    }
} else {
    dd_return(false, "error",  "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
