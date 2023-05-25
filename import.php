<?php
$file = fopen('voc.csv', 'r');

$v30 = [];
$v7 = [];
$v2 = [];
$v1 = [];

while (($line = fgetcsv($file)) !== FALSE) {
  //$line is an array of the csv elements

    if($line[0] != "" && strlen($line[0]) == 8 ){
        array_push($v30,$line[0]);
    }
    if($line[1] != "" && strlen($line[1]) == 8 ){
        array_push($v7,$line[1]);
    }
    if($line[2] != "" && strlen($line[2]) == 8 ){
        array_push($v2,$line[2]);
    }
    if($line[3] != "" && strlen($line[3]) == 8 ){
        array_push($v1,$line[3]);
    }

}


fclose($file);

$conn = mysqli_connect('localhost', 'nongkyga_user', 'azmannaqib123', 'nongkyga_db');

foreach($v30 as $voc){
    $import = mysqli_query($conn,"INSERT INTO `vouchers` VALUES('','$voc','30') ");
}
foreach($v7 as $voc){
    $import = mysqli_query($conn,"INSERT INTO `vouchers` VALUES('','$voc','7') ");
}
foreach($v2 as $voc){
    $import = mysqli_query($conn,"INSERT INTO `vouchers` VALUES('','$voc','2') ");
}
foreach($v1 as $voc){
    $import = mysqli_query($conn,"INSERT INTO `vouchers` VALUES('','$voc','1') ");
}

$total = count($v30)+count($v7)+count($v2)+count($v1);

echo "Berhasil Import ".$total." Vouchers"

?>