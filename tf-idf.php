<?php 
include_once "connect.php";

$kata = "Pada suatu hari petani tersebut pergi ke sungai di dekat tempat tinggalnya, ia bermaksud mencari ikan untuk lauknya hari ini. Dengan hanya berbekal sebuah kail, umpan dan tempat ikan, ia pun langsung menuju ke sungai. Setelah sesampainya di sungai, petani tersebut langsung melemparkan kailnya. Sambil menunggu kailnya dimakan ikan, petani tersebut berdoa,“Ya Alloh, semoga aku dapat ikan banyak hari ini”. Beberapa saat setelah berdoa, kail yang dilemparkannya tadi nampak bergoyang-goyang. Ia segera menarik kailnya. Petani tersebut sangat senang sekali, karena ikan yang didapatkannya sangat besar dan cantik sekali";
echo "<b><h3>Kata asli :</b></h3> $kata";
echo "<br>";
echo "<b><h3>Langkah 1 : pisahkan dokumen</h3></b>";
$pisah = explode(".",$kata);
$pisah = preg_replace('/[^A-Za-z0-9]/', ' ', $pisah);

for($i = 0 ; $i < sizeof($pisah) ; $i++ ){
    echo "<b>Dokumen " . ($i + 1) . " (d" . ($i + 1) . ")</b> : <br>";
    echo $pisah[$i] . "<br>";
}

echo "<br><b><i>Jadi total jumlah dokumen adalah (D) = ". sizeof($pisah) . "</b></i>";

echo "<br><br>Kemudian dilakukan filtering seperti melakukan pemotongan kata ditiap kata yang menyusun, menghilangkan tanda baca, angka dan kemudian dilakukan Stopword (menghilangkan kata-kata yang terdapat dalam daftar stopword) lalu kemudian di stemming (mengubah kata berimbuhan menjadi kata dasarnya).<br><br>Setelah melalui proses Stopword dan Stemming: <br>"; 
$hasilStemming = array();
for($i = 0 ; $i < sizeof($pisah) ; $i++ ){
    $_pisah = explode(" ", $pisah[$i]);
    $hasil = "";
    $index = 0;
    foreach($_pisah as $p){

        if($p == "tersebut" || $p == "sekali"){
            $_kata_dasar = $p;
        } else {
            $_kata_dasar = stemming($p,$data); //memeriksa hasil stemming pada kata dasar
        }

        if($index == 0){
            $hasil .= $_kata_dasar;
        } else {
            $hasil .= " " . $_kata_dasar;
        }
        $index++;
    }
    array_push($hasilStemming, $hasil);
    echo "<b>Dokumen " . ($i + 1) . " (d" . ($i + 1) . ")</b> : <br> $hasil <br>";
}

$KATA_DASAR_TABLE = array();
 for($i = 0 ; $i < sizeof($hasilStemming) ; $i++ ){
    $_pisah2 = preg_replace('/\s+/', '_', $hasilStemming[$i]);
    $_pisah3 = explode("_", $_pisah2);
    foreach($_pisah3 as $x){
        if($x != ""){
            if(!in_array($x, $KATA_DASAR_TABLE)){
                array_push($KATA_DASAR_TABLE, $x);
            }
        }
    }
}

echo '<br>'.
    '<table border="1" style="width:75%;">'.
    '<tr>'.
        '<th rowspan="2">Q</th>'.
        '<th colspan="'. sizeof($hasilStemming) .'">tf</th>'.
        '<th rowspan="2">df</th>'.
        '<th rowspan="2">D/df</th>'.
        '<th rowspan="2">IDF</th>'.
        '<th rowspan="2">IDF+1</th>'.
    '</tr>'.
    '<tr>';
    for($i = 0 ; $i < sizeof($hasilStemming) ; $i++ ){
        echo "<th>D". ($i + 1) ."</th>";
    }
    echo '</tr>';
    $idfPlus1Array = array();
    foreach($KATA_DASAR_TABLE as $a){
        // echo $aw . " - " . $he . "<br>";
        echo "<tr><td>$a</td>";
        $df = (int) 0;
        $Ddf = (float) 0;
        $idf = (float) 0;
        $idfPlus1 = (float) 0;
        
        for($i = 0 ; $i < sizeof($hasilStemming) ; $i++ ){
            $_pisah2 = preg_replace('/\s+/', '_', $hasilStemming[$i]);
            $_pisah3 = explode("_", $_pisah2);
            $count = 0;
            foreach($_pisah3 as $p){
                if($p == $a){
                    $count++;
                }
            }
            echo "<td align='center'>$count</td>";
            $df = $df + $count;        
        }
        $Ddf = round((sizeof($hasilStemming) / (int) $df),2);
        $idf = round((1 / $df), 2);
        $idfPlus1 = 1 + $idf;
        array_push($idfPlus1Array, $idfPlus1);
        echo "<td align='center'>$df</td>";
        echo "<td align='center'>$Ddf</td>";
        echo "<td align='center'>$idf</td>";
        echo "<td align='center'>$idfPlus1</td>";
        echo "</tr>";
    }
    echo '</table>';
?>

<br><br>
<table border="1" style="width:75%;">
    <tr>
        <th rowspan="2">Q</th>
        <th colspan="<?= sizeof($hasilStemming); ?>">W = tf * (IDF +1)</th>
    </tr>
    <tr>
        <?php 
         for($i = 0 ; $i < sizeof($hasilStemming) ; $i++ ){
            echo "<th>D". ($i + 1) ."</th>";
        }
        $indexW = 0;
        foreach($KATA_DASAR_TABLE as $a){
            echo "<tr><td>$a</td>";

            for($i = 0 ; $i < sizeof($hasilStemming) ; $i++ ){
                $_pisah2 = preg_replace('/\s+/', '_', $hasilStemming[$i]);
                $_pisah3 = explode("_", $_pisah2);
                $count = 0;
                foreach($_pisah3 as $p){
                    if($p == $a){
                        $count++;
                    }
                }
                echo "<td align='center'>". ($count * $idfPlus1Array[$indexW])  ."</td>";             
            }
            echo "</tr>";
            $indexW++;
        }

        ?>
    </tr>
</table>
   