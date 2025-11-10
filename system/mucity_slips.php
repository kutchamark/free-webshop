<?php
class yosiket
{
  public function slip_check($qrcode)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://suba.rdcw.co.th/v1/inquiry',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '{
          "payload": "' . $qrcode . '"
      }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        /// แก้เลขจากตรงนี้ base64_encode('เลขclientid:เลขsecret')
        'Authorization: Basic ' . base64_encode('dd3fb422e72c43768e9f7c240b32c4a8:Zc1GEsBD7yaY-Mlo6xsg5')
      ),
    ));

    $response = curl_exec($curl);
    return $response;
  }
}