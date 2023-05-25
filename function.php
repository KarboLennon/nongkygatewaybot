<?php

$merchantCode = 'T18015';
$apiKey       = 'kG4aJsMqEQDbu15SN2EVyE7Wyy7Znh3HNAK5Qpan';
$privateKey   = '8UaNZ-BDWnI-ehVIY-G6TJR-lBKQT';


function request_transaksi($merchantRef,$amount,$item,$name){
    
    global $apiKey,$privateKey,$merchantCode;

    $data = [
        'method'         => 'QRIS',
        'merchant_ref'   => $merchantRef,
        'amount'         => $amount,
        'customer_name'  => $name,
        'customer_email' => "email@gmail.com",
        'customer_phone' => '081234567890',
        'order_items'    => [$item],
        'return_url'     => 'https://t.me/rumahnongky_bot',
        'callback_url'   => 'https://nongky-gateway.my.id/bot/callback.php',   
        'expired_time' => (time() + (1 * 2 * 60)), // 1 jam
        'signature'    => hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey)
    ];
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_URL            => 'https://tripay.co.id/api/transaction/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
        CURLOPT_FAILONERROR    => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
    ]);
    
    $response = curl_exec($curl);
    $error = curl_error($curl);
    
    curl_close($curl);
    
    if (empty($error)){
        return $response;
    }else{
        return $error;
    }
}

?>