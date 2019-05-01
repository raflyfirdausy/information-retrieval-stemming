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
    '<table border="1" style="width:50%;">'.
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
<table border="1" style="width:50%;">
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
            echo "<tr>";
            echo "<td>$a</td>";

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

<b><h3>Langkah 2 : Hitung Bobot Kalimat</h3></b>

<?php 
    for($i = 0 ; $i < sizeof($hasilStemming) ; $i++ ){
        echo "<b>Dokumen " . ($i + 1) . " (d" . ($i + 1) . ")</b> : <br>";
        $_pisah2 = preg_replace('/\s+/', '_', $hasilStemming[$i]);
        $_pisah3 = explode("_", $_pisah2);
        $tfIdfArray = array();
        foreach($_pisah3 as $x){
            if($x != ""){
                if(!in_array($x, $tfIdfArray)){
                    array_push($tfIdfArray, $x);
                }
            }
        }

        $indexTF = 0;
        foreach($tfIdfArray as $t){
            if($indexTF != sizeof($tfIdfArray) - 1){
                echo "tfidf($t) + ";
            } else {
                echo "tfidf($t)";
            }
            $indexTF++;
        }
        echo "<br><br>";
    }

    echo "<b><h3>Pembobotan</b></h3>";
    $PengurutanKalimatArray = array();
    for($i = 0 ; $i < sizeof($hasilStemming) ; $i++ ){
        echo "<b>Dokumen " . ($i + 1) . " (d" . ($i + 1) . ")</b> : ";
        $_pisah2 = preg_replace('/\s+/', '_', $hasilStemming[$i]);
        $_pisah3 = explode("_", $_pisah2);
        $tfIdfArray = array();
        foreach($_pisah3 as $x){
            if($x != ""){
                if(!in_array($x, $tfIdfArray)){
                    array_push($tfIdfArray, $x);
                }
            }
        }

        // die(var_dump($tfIdfArray));
        $indexTF = 0;
        $jumlah = 0;
        foreach($tfIdfArray as $t){ 
            // die(var_dump($hasilStemming[$i]));
            // die(var_dump($t));
            $KataDiCari = "";
            foreach($hasilStemming as $q){
                $KataDiCari .= $q;
            }
            // die(var_dump($KataDiCari));       
            $_pisah2 = preg_replace('/\s+/', '_', $KataDiCari);
            $_pisah3 = explode("_", $_pisah2);
            // die(var_dump($_pisah3));
            $count = 0;
            // die(var_dump($t));
            for($a = 0 ; $a < sizeof($_pisah3) ; $a++ ){
                // die(var_dump($_pisah3[$a]));
                if($_pisah3[$a] == $t){
                    $count++;                    
                }
            }
            $jumlah = $jumlah + $count;
            $pKalimat = [
                "kalimat" => $hasilStemming[$i],
                "jumlah" => $jumlah,
                "dokumen" => "Dokumen " . ($i + 1) . " (d" . ($i + 1) . ")"
            ];

            if($indexTF != sizeof($tfIdfArray) - 1){
                echo $count . " + ";
            } else {
                echo $count . " = " . $jumlah;
            }
            
            $indexTF++;
        }    
        array_push($PengurutanKalimatArray, $pKalimat);
        echo "<br>";
    }
    // die(json_encode($PengurutanKalimatArray));

    function array_sort($array, $on, $order=SORT_ASC){

        $new_array = array();
        $sortable_array = array();
    
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }
    
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }
    
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
        return $new_array;
    }
  
    echo '<br><b><h3>Langkah 3 : Pengurutan Kalimat</h3></b>';
    $sorted = array_sort($PengurutanKalimatArray, "jumlah", SORT_DESC);
    // die(var_dump($sorted));
    echo 
    '<table border="1" width="30%">'.
    '<tr>'.
        '<th>Dokumen</th>'.
        '<th>Score Pembobotan</th>'.
    '</tr>';

    foreach($sorted as $key => $value){
        // die(var_dump($value));
        echo '<tr><td align="center">'. $value["dokumen"] .'</td>';
        echo '<td align="center">'. $value["jumlah"] .'</td></tr>';
    }
    echo '</table>';

    $n = 3;
    echo '<br><b><h3>Langkah 4 : AMBIL N('. $n .') KALIMAT SEBAGAI RINGKASAN </h3></b>';
    $indexSorted = 0;
    foreach($sorted as $key => $value){
        $indexSorted++;
        echo "<b>" . $value["dokumen"] . "</b><br>" . $value["kalimat"] . "<br><br>";
        if($indexSorted >= $n){
            break;
        }
    }

    echo "<b>Kalimat Gabungan : </b><br>";
    $indexSortedLagi = 0;
    foreach($sorted as $key => $value){
        
        $indexSortedLagi++;
        if($indexSortedLagi >= $n){
            echo " " . trim($value["kalimat"]). ".";
            break;
        } else {
            echo " " . trim($value["kalimat"]). ",";
        }
    }


?>

