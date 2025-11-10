<?php
error_reporting(0);

require_once './a_func.php';
require_once './mucity_slips.php';

$ys = new yosiket();

function dd_return($status, $message)
{
    if ($status) {
        $json = ['status' => 'success', 'message' => $message];
        http_response_code(200);
        die(json_encode($json));
    } else {
        $json = ['status' => 'fail', 'message' => $message];
        http_response_code(400);
        die(json_encode($json));
    }
}

class ONIMAI_API
{
    private $apiUrl = 'https://api.onimai.cloud/v1/';

    public function verifySlip($qrcode, $token)
    {
        $endpoint = "slip/verify";

        $curl = curl_init();

        $payload = json_encode([
            'qrcode' => $qrcode
        ]);

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer ".$token,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }

        return json_decode($response, true);
    }
}

$onimai = new ONIMAI_API();
$config_bank = dd_q("SELECT * FROM bank WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

//////////////////////////////////////////////////////////////////////////

header('Content-Type: application/json; charset=utf-8;');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['id'])) {
        $plr = dd_q("SELECT * FROM users WHERE id = ?", [$_SESSION['id']])->fetch(PDO::FETCH_ASSOC);
        if ($_POST['qrcode'] != '') {
            try {
                $apiToken = '41f71cbb232693c0d115a4b4ef690e86c10b596f0fef1480c241aa7b82d2633f'; // Token จาก slip token https://api.onimai.cloud
                $verificationResult = $onimai->verifySlip($_POST['qrcode'], $apiToken);

                if ($verificationResult['status'] === 'success') {
                    if ($verificationResult['used'] == 1) {
                        dd_return(false, "สลิปนี้ถูกใช้งานไปแล้ว");
                    }

                    $recv_name_th = $verificationResult['data']['receiver']['THname'];
                    $recv_name_trimmed = mb_substr($recv_name_th, 0, -2);

                    if ($config_bank['fname'] === $recv_name_trimmed) {
                        $info = $verificationResult['data'];
                        $amount = $info['amount'];
                        $ref = $info['transRef'];

                        $q1 = dd_q("SELECT * FROM kbank_trans WHERE ref = ?", [$ref]);
                        $q2 = dd_q("SELECT * FROM kbank_trans WHERE qr = ?", [$_POST['qrcode']]);
                        if ($q1->rowCount() == 0 || $q2->rowCount() == 0) {
                            $ha = dd_q(
                                "INSERT INTO `topup_his` (`id`, `link`, `amount`, `date`, `uid`, `uname`) VALUES (NULL, ?, ?, NOW(), ?, ?)",
                                [
                                    "สลิปบัญชีชื่อ : " . $info['sender']['THname'],
                                    $amount,
                                    $_SESSION['id'],
                                    $plr['username']
                                ]
                            );
                            $insert_ref = dd_q("INSERT INTO `kbank_trans`(`qr`, `ref`, `sender`, `date`) VALUES(?, ?, ?, ?)", [$_POST['qrcode'], $ref, $info['sender']['THname'], date("Y-m-d h:i:s")]);
                            $update_user = dd_q("UPDATE users SET point = point + ?, total = total + ? WHERE id = ?", [$amount, $amount, $_SESSION['id']]);
                            if ($ha && $insert_ref && $update_user) {
                                dd_return(true, "คุณเติมเงินสำเร็จ " . $amount . " บาท");
                            } else {
                                dd_return(false, "SQL ผิดพลาด");
                            }
                        } else {
                            dd_return(false, "สลิปนี้ใช้แล้ว");
                        }
                    } else {
                        dd_return(false, "ชื่อผู้รับเงินไม่ตรงกับที่ตั้งไว้");
                    }
                } else {
                    dd_return(false, "Qr code ไม่ถูกต้อง");
                }
            } catch (Exception $e) {
                dd_return(false, "เกิดข้อผิดพลาด: " . $e->getMessage());
            }
        } else {
            dd_return(false, "กรุณาส่งข้อมูลให้ครบ");
        }
    } else {
        dd_return(false, "เข้าสู่ระบบก่อนดำเนินการ");
    }
} else {
    dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}

