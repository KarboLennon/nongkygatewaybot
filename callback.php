<?php

// Include file koneksi database
$db = new mysqli('localhost', 'nongkyga_user', 'azmannaqib123', 'nongkyga_db');
$path = "https://api.telegram.org/bot5900188594:AAEmKXSRA2Aul0Su1Bu5SKbd0CL4Y7MV-iQ";
$conn = mysqli_connect('localhost', 'nongkyga_user', 'azmannaqib123', 'nongkyga_db');

// Ambil data JSON
$json = file_get_contents('php://input');

// Ambil callback signature
$callbackSignature = isset($_SERVER['HTTP_X_CALLBACK_SIGNATURE'])
    ? $_SERVER['HTTP_X_CALLBACK_SIGNATURE']
    : '';

// Isi dengan private key anda
$privateKey = '8UaNZ-BDWnI-ehVIY-G6TJR-lBKQT';

// Generate signature untuk dicocokkan dengan X-Callback-Signature
$signature = hash_hmac('sha256', $json, $privateKey);

// Validasi signature
if ($callbackSignature !== $signature) {
    exit(json_encode([
        'success' => false,
        'message' => 'Invalid signature',
    ]));
}

$data = json_decode($json);

if (JSON_ERROR_NONE !== json_last_error()) {
    exit(json_encode([
        'success' => false,
        'message' => 'Invalid data sent by payment gateway',
    ]));
}

// Hentikan proses jika callback event-nya bukan payment_status
if ('payment_status' !== $_SERVER['HTTP_X_CALLBACK_EVENT']) {
    exit(json_encode([
        'success' => false,
        'message' => 'Unrecognized callback event: ' . $_SERVER['HTTP_X_CALLBACK_EVENT'],
    ]));
}

$reference = $db->real_escape_string($data->reference);
$status = strtoupper((string) $data->status);

if ($data->is_closed_payment === 1) {
    $result = $db->query("SELECT * FROM invoices WHERE reference = '{$reference}' AND status = 'UNPAID' LIMIT 1");

    if (! $result) {
        exit(json_encode([
            'success' => false,
            'message' => 'Invoice not found or already paid: ' . $reference,
        ]));
    }

    while ($invoice = $result->fetch_object()) {
        $chatid = $invoice->chat_id;
        $tipe = $invoice->tipe;

        switch ($status) {
            // handle status PAID
            case 'PAID':

                $voc = mysqli_query($conn,"SELECT * FROM `vouchers` WHERE `tipe` = '$tipe' LIMIT 1");

                while($v = mysqli_fetch_assoc($voc)){
                    $idpocer = $v['id'];
                    $pocer = $v['kode'];
                }

                $string = "Terima kasih sudah melakukan pembayaran, Voucher = $pocer , klik /start untuk membeli kembali";
                
                file_get_contents("$path/sendMessage?chat_id=$chatid&text=".urlencode($string));
                
                mysqli_query($conn,"DELETE FROM `invoices` WHERE `reference` = '$reference' LIMIT 1");
                mysqli_query($conn,"DELETE FROM `vouchers` WHERE `id` = '$idpocer' LIMIT 1"); 

                break;

            // handle status EXPIRED
            case 'EXPIRED':

                $string = "Transaksi Kadaluarsa! , klik /start untuk membeli lagi";
                
                file_get_contents("$path/sendMessage?chat_id=$chatid&text=".urlencode($string));
                mysqli_query($conn,"DELETE FROM `invoices` WHERE `reference` = '$reference' LIMIT 1");
                break;

            // handle status FAILED
            case 'FAILED':

                $string = "Transaksi Gagal!";
                $data = [
                    'chat_id' => $chatid,
                    'text' => $string,
                    'parse_mode' => "html"
                ];
                
                file_get_contents("$path/sendMessage?" . http_build_query($data) );
                mysqli_query($conn,"DELETE FROM `invoices` WHERE `reference` = '$reference' LIMIT 1");
                break;

            default:
                exit(json_encode([
                    'success' => false,
                    'message' => 'Unrecognized payment status',
                ]));
        }

        exit(json_encode(['success' => true]));
    }
}