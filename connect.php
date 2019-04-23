<?php
include_once "stemming.php";
$koneksi = mysqli_connect("localhost", "root", "", "tbi_stemming");
if (!$koneksi) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
$query = "SELECT katadasar FROM tb_katadasar"; //query untuk cek katadasar
$hasil = $koneksi->query($query);
$data = array();
while($aw = mysqli_fetch_array($hasil)){
    array_push($data, $aw['katadasar']); //mengambil seluruh kata dasar pada database dan di masukan ke array
}

if(isset($_POST['kata'])){
    $kata = $_POST['kata'];
    $kata_dasar = stemming($kata,$data); //memeriksa hasil stemming pada kata dasar
}
?>