<?php
$path = "https://api.telegram.org/bot5900188594:AAEmKXSRA2Aul0Su1Bu5SKbd0CL4Y7MV-iQ";
$conn = mysqli_connect('localhost', 'nongkyga_user', 'azmannaqib123', 'nongkyga_db');

$select = mysqli_query($conn,"SELECT * FROM `vouchers`");

require('function.php');
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$user = $update["message"]['from'];

function sendphoto($image){

    global $path,$chatId;

    $url        = "$path/sendPhoto?chat_id=" . $chatId ;

    $post_fields = array('chat_id'   => $chatId,
        'photo'     => new CURLFile(realpath($image))
    );

    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type:multipart/form-data"
    ));
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
    $output = curl_exec($ch);
}

function sendmsg($string){
    global $path,$chatId;

    $data = [
        'chat_id' => $chatId,
        'text' => $string,
        'parse_mode' => 'html'
    ];
    
    file_get_contents("$path/sendMessage?" . http_build_query($data) );
}

if(strpos($message, "/start") === 0){
    
    $string = "```
=====================
RUMAH NONGKY GATEWAY
========MENU=========
1. Voucher 30 Hari
2. Voucher 7 Hari
3. Voucher 2 Hari
4. Voucher 1 Hari
=====================

*balas dengan angka
    ```";

    $data = [
        'chat_id' => $chatId,
        'text' => $string,
        'parse_mode' => 'markdown'
    ];
    file_get_contents("$path/sendMessage?" . http_build_query($data) );
    
}else if(strpos($message, "/cancel") === 0){
    mysqli_query($conn,"DELETE FROM `invoices` WHERE `chat_id` = '$chatId'");

    $string = "Transaksi Dibatalkan!

Ketik /start untuk membeli voucher lain.";
    sendmsg($string);
    
}else if(strpos($message, "1") === 0){

    $cek = mysqli_query($conn, "SELECT * FROM `invoices` WHERE `chat_id` = '$chatId'");

    if (mysqli_num_rows($cek) > 0){
        $string = 'Silahkan bayar atau /cancel dahulu transaksi sebelumnya';
        sendmsg($string);

    }else{
        
        $merchantRef = "V30";
        $amount = 35000;
        $item = [
            'sku'         => 'V30',
            'name'        => 'Voucher 30 Hari',
            'price'       => 35000,
            'quantity'    => 1,
        ];
    
        $tx = request_transaksi($merchantRef,$amount,$item,$user['first_name']);
        $tx_json = json_decode($tx,TRUE);
    
        if($tx_json['success'] == true){

            $reference = $tx_json['data']['reference'];
            $status = $tx_json['data']['status'];

            mysqli_query($conn, "INSERT INTO `invoices` (`reference`, `chat_id`, `status`, `tipe`) VALUES ('$reference', '$chatId', '$status','30');");

            $url = stripslashes($tx_json['data']['qr_url']); 
            $time = time();
            $img = "qr_$time.png"; 
            
            // Function to write image into file
            file_put_contents($img, file_get_contents($url));
            sendphoto($img);
            unlink($img);
    
            $string = 'Scan menggunakan :
-GOPAY
-DANA
-OVO
-SHOPEEPAY
-Aplikasi M-Banking BCA,BRI,BNI,MANDIRI

NOMINAL : Rp 35.000,.

ketik /cancel jika ingin membatalkan pembelian';
            sendmsg($string);
    
        }else{
            sendmsg($tx);
        }
    }

}else if(strpos($message, "2") === 0){

    $cek = mysqli_query($conn, "SELECT * FROM `invoices` WHERE `chat_id` = '$chatId'");

    if (mysqli_num_rows($cek) > 0){
        $string = 'Silahkan bayar atau /cancel dahulu transaksi sebelumnya';
        sendmsg($string);

    }else{
        
        $merchantRef = "V7";
        $amount = 15000;
        $item = [
            'sku'         => 'V7',
            'name'        => 'Voucher 7 Hari',
            'price'       => 15000,
            'quantity'    => 1,
        ];
    
        $tx = request_transaksi($merchantRef,$amount,$item,$user['first_name']);
        $tx_json = json_decode($tx,TRUE);
    
        if($tx_json['success'] == true){

            $reference = $tx_json['data']['reference'];
            $status = $tx_json['data']['status'];

            mysqli_query($conn, "INSERT INTO `invoices` (`reference`, `chat_id`, `status`, `tipe`) VALUES ('$reference', '$chatId', '$status','7');");

            $url = stripslashes($tx_json['data']['qr_url']); 
            $time = time();
            $img = "qr_$time.png"; 
            
            // Function to write image into file
            file_put_contents($img, file_get_contents($url));
            sendphoto($img);
            unlink($img);
    
            $string = 'Scan menggunakan :
-GOPAY
-DANA
-OVO
-SHOPEEPAY
-Aplikasi M-Banking BCA,BRI,BNI,MANDIRI

NOMINAL : Rp 15.000,.

ketik /cancel jika ingin membatalkan pembelian';
            sendmsg($string);
    
        }else{
            sendmsg($tx);
        }
    }

}else if(strpos($message, "3") === 0){

    if(strpos($message, " ") !== false){
        $jumlah = explode(" ",$message)[1];
        sendmsg($jumlah);
    }

     $cek = mysqli_query($conn, "SELECT * FROM `invoices` WHERE `chat_id` = '$chatId'");

     if (mysqli_num_rows($cek) > 0){
         $string = 'Silahkan bayar atau /cancel dahulu transaksi sebelumnya';
         sendmsg($string);

     }else{
        
         $merchantRef = "V2";
         $amount = 5000;
         $item = [
             'sku'         => 'V2',
             'name'        => 'Voucher 2 Hari',
             'price'       => 5000,
             'quantity'    => 1,
         ];
    
         $tx = request_transaksi($merchantRef,$amount,$item,$user['first_name']);
         $tx_json = json_decode($tx,TRUE);
    
         if($tx_json['success'] == true){

             $reference = $tx_json['data']['reference'];
             $status = $tx_json['data']['status'];

             mysqli_query($conn, "INSERT INTO `invoices` (`reference`, `chat_id`, `status`, `tipe`) VALUES ('$reference', '$chatId', '$status','2');");

             $url = stripslashes($tx_json['data']['qr_url']); 
             $time = time();
             $img = "qr_$time.png"; 
            
             // Function to write image into file
             file_put_contents($img, file_get_contents($url));
             sendphoto($img);
             unlink($img);
    
             $string = 'Scan menggunakan :
 -GOPAY
 -DANA
 -OVO
 -SHOPEEPAY
 -Aplikasi M-Banking BCA,BRI,BNI,MANDIRI

 NOMINAL : Rp 5.000,.

 ketik /cancel jika ingin membatalkan pembelian';
             sendmsg($string);
    
         }else{
             sendmsg($tx);
         }
     }

}else if(strpos($message, "4") === 0){

    $cek = mysqli_query($conn, "SELECT * FROM `invoices` WHERE `chat_id` = '$chatId'");

    if (mysqli_num_rows($cek) > 0){
        $string = 'Silahkan bayar atau /cancel dahulu transaksi sebelumnya';
        sendmsg($string);

    }else{
        
        $merchantRef = "V1";
        $amount = 3000;
        $item = [
            'sku'         => 'V1',
            'name'        => 'Voucher 1 Hari',
            'price'       => 3000,
            'quantity'    => 1,
        ];
    
        $tx = request_transaksi($merchantRef,$amount,$item,$user['first_name']);
        $tx_json = json_decode($tx,TRUE);
    
        if($tx_json['success'] == true){

            $reference = $tx_json['data']['reference'];
            $status = $tx_json['data']['status'];

            mysqli_query($conn, "INSERT INTO `invoices` (`reference`, `chat_id`, `status`, `tipe`) VALUES ('$reference', '$chatId', '$status','1');");

            $url = stripslashes($tx_json['data']['qr_url']); 
            $time = time();
            $img = "qr_$time.png"; 
            
            // Function to write image into file
            file_put_contents($img, file_get_contents($url));
            sendphoto($img);
            unlink($img);
    
            $string = 'Scan menggunakan :
-GOPAY
-DANA
-OVO
-SHOPEEPAY
-Aplikasi M-Banking BCA,BRI,BNI,MANDIRI

NOMINAL : Rp 3.000,.

ketik /cancel jika ingin membatalkan pembelian';
            sendmsg($string);
    
        }else{
            sendmsg($tx);
        }
    }
}else{
    $string = "saya cuma seorang robot, gabisa di ajak ngobrol atau balas pesan, kalau ada keluhan whatsapp yang mulia paduka ali aja, saya cuma budak";
    sendmsg($string);
}