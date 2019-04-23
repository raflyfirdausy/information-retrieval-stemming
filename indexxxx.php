<?php 
include_once "connect.php"; 

$query = "SELECT katadasar FROM tb_katadasar";
$hasil = $koneksi->query($query);
$data = array();
while($aw = mysqli_fetch_array($hasil)){
    array_push($data, $aw['katadasar']);
}

if(isset($_POST['kata'])){
    $kata = $_POST['kata'];
    $kata_dasar = stemming($kata,$data);
}
?>

<html>
    <head>
        <title>
            UNTUNG
        </title>
    </head>
    <body>
        <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
            <input type="text" name="kata">
            <input type="submit" value="CARI">
        </form>

        <?php if(isset($kata_dasar)){ ?>
            Hasil : <?= $kata_dasar; ?>
         <?php } ?>
    </body>
</html>