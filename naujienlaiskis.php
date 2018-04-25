<?php
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'keliones';

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

?>

<?php
    $id = $_SESSION['user'];
    $pradzia = '';
    $pabaiga = '';
    $sql = "SELECT * FROM naujienlaiskis WHERE vartotojo_id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($rows = mysqli_fetch_assoc($result)) {
        $pradzia = $rows['pradzios_data'];
        $pabaiga = $rows['pabaigos_data'];
        echo "<b>Pasiūlymai skirti laikotarpiui nuo ".$pradzia." iki ".$pabaiga."</b><br><br>";

        // Spausdinamos kelionės su laukiančiųjų eile į lentelę
        $sql='SELECT k.id, k.salis, k.miestas, k.kaina_zmogui, k.vietu_skaicius, k.isvykimo_data, k.grizimo_data, k.trukme_dienomis, k.aprasymas, l.laukianciuju_skaicius AS laukiantys
        FROM keliones k inner join laukianciuju_eile l on k.id = l.keliones_id GROUP BY k.salis';
        $results = mysqli_query($conn, $sql);

        if ($results->num_rows > 0) {
            while($row = mysqli_fetch_assoc($results)) {
                $id = $row['id'];
                $trinti = 'trinti'.$id;
                $redaguoti = 'redaguoti'.$id;
                $uzsakymas = 'uzsakymas'.$id;
                $keisti = 'keisti'.$id;
                $salis = $row['salis'] ;
                $miestas = $row['miestas'] ;
                $kaina = $row['kaina_zmogui'] ;
                $eile = $row['laukiantys'] ;
                $vietusk = $row['vietu_skaicius'] ;
                $isvykimo = $row['isvykimo_data'] ;
                $grizimo = $row['grizimo_data'] ;
                $trukme = $row['trukme_dienomis'] ;
                $aprasymas = $row['aprasymas'] ;
                if ($isvykimo >= $pradzia && $grizimo <= $pabaiga) 
                {
                echo '<table>'
                .'<tr>'
                .'<td>'.'<b>Šalis:</b>'.'</td>'
                .'<td>'.$salis.'</td>'
                .'</tr>'
                .'<td>'.'<b>Miestas:</b>'.'</td>'
                .'<td>'.$miestas.'</td>'
                .'<tr>'
                .'<td>'.'<b>Kaina žmogui:</b>'.'</td>'
                .'<td>'.$kaina.' eur.'.'</td>'
                .'</tr>'
                .'<tr>'
                .'<td>'.'<b>Laukiančiųjų eilė:</b>'.'</td>'
                .'<td>'.$eile.'</td>'
                .'</tr>'
                .'<tr>'
                .'<td>'.'<b>Vietų skaičius:</b>'.'</td>'
                .'<td>'.$vietusk.'</td>'
                .'</tr>'
                .'<tr>'
                .'<td>'.'<b>Išvykimo data:</b>'.'</td>'
                .'<td>'.$isvykimo.'</td>'
                .'</tr>'
                .'<tr>'
                .'<td>'.'<b>Grįžimo data:</b>'.'</td>'
                .'<td>'.$grizimo.'</td>'
                .'</tr>'
                .'<tr>'
                .'<td>'.'<b>Trukmė dienomis:</b>'.'</td>'
                .'<td>'.$trukme.'</td>'
                .'</tr>'
                .'<tr>'
                .'<td>'.'<b>Aprašymas:</b>'.'</td>'
                ."<td style='width : 500px'>$aprasymas</td>"
                .'</tr>'
                .'</table>';

                // Mygtukas 'Užsakyti' iššaukia užsakymo formą
                if($_SESSION) {
                    if ($_SESSION['user']) {
                        echo "<form action='' method='post'>"
                        ."<input type = 'submit' value='Užsisakyti' name='$id'>"
                        .'</form>';
                    }
                }
                if($_SESSION) {
                    if ($_SESSION['tipas'] == 'admin') {
                        // Mygtukas 'Trinti' ištrina kelionę
                        echo "<form action='' method='post'>"
                        ."<input type = 'submit' value='Trinti' name='$trinti'>"
                        .'</form>';
                    }
                }
                if($_SESSION) {
                    if ($_SESSION['tipas'] == 'admin') {
                        // Mygtukas 'Redaguoti' redaguoja kelionę
                        echo "<form action='' method='post'>"
                        ."<input type = 'submit' value='Redaguoti' name='$redaguoti'>"
                        .'</form>';
                    }
                }

                // Užsakymo forma
                if(isset($_POST[$id])) {
                    echo "<form action='' method='post'>"
                    .'<b><i>Keliaujančių žmonių skaičius:</i></b><br>'
                    ."<input type = 'number' placeholder='Žmonių skaičius' name='$uzsakymas' required><br>"
                    ."<input type = 'password'  name='Slaptazodis' placeholder='Slaptažodis' required>"
                    ."<input type = 'submit' value='Užsakymas' name='patvirtinta'>"
                    .'</form>'; 
                }
                // Patvirtinamas kelionės užsakymas
                if(isset($_POST[$uzsakymas])) {
                    $uzsak = $_POST[$uzsakymas];
                    $suma = $uzsak * $kaina;             
                    $mokejimas = false;
                    $vartotojas = $_SESSION['user']; // Kai bus padarytas prisijungimas, vartotojo id bus is sesijos
                    $sql="INSERT INTO uzsakymai(keliones_id, zmoniu_skaicius, suma, mokejimas_ivykdytas, vartotojo_id) 
                    VALUES('$id', '$uzsak', '$suma', '$mokejimas', '$vartotojas')";
                    $result = mysqli_query($conn, $sql);
                    $sql="SELECT laukianciuju_skaicius FROM laukianciuju_eile  WHERE keliones_id = '$id'";
                    $result = mysqli_query($conn, $sql);
                    //Jau šios kelionės laukiančiųjų eilėje esantis skaičius
                    $skaicius = 0;
                    if ($result->num_rows > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $skaicius = $row['laukianciuju_skaicius'];
                        }
                    }
                    // Prie laukiančiųjų eilėje skaičiaus pridedamas dabar užsisakiusiųjų skaičius ir tai įdedama į laukiančiųjų eilės lentelę
                    $uzsakym = $uzsak + $skaicius;
                    $sql="UPDATE laukianciuju_eile SET laukianciuju_skaicius = '$uzsakym' WHERE keliones_id = '$id'";
                    $result = mysqli_query($conn, $sql);

                    // Gaunamas laukiančiųjų eilės id
                    $eilesId = 0;
                    $sql="SELECT id FROM laukianciuju_eile WHERE keliones_id = '$id'";
                    $result = mysqli_query($conn, $sql);
                    if ($result->num_rows > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $eilesId = $row['id'];
                        }
                    }

                    // Į Vartotojų laukiančiųjų eilės lentelę įdedamas vartotojo id ir laukiančiųjų eilės id
                    $sql="INSERT INTO vartotoju_laukianciuju_eile(vartotojo_id, eiles_id) VALUES('$vartotojas', '$eilesId')";
                    $result = mysqli_query($conn, $sql);
                    
                    echo "<meta http-equiv='refresh' content='0'>";
                }
                // Kelionės ištrynimas
                if(isset($_POST[$trinti])) {
                    $sql="DELETE FROM keliones WHERE id = '$id'";
                    $result = mysqli_query($conn, $sql);
                    // Ištrynus kelionę perkraunamas puslapis
                    echo "<meta http-equiv='refresh' content='0'>";
                }

                if(isset($_POST[$redaguoti])) {

                    echo "<form action='' method='post' >" 
                    ."<b><i>Keliones šalis:</i></b><br>" 
                    ."<input type='text' style='width: 500px' name='salis2' value='$salis'><br>" 
                    ."<b><i>Miestas:</i></b><br>" 
                    ."<input type='text' style='width: 500px' name='miestas2' value='$miestas'><br>" 
                    ."<b><i>Kaina žmogui:</i></b><br>" 
                    ."<input type='text' style='width: 500px' name='kaina2' value='$kaina'><br>" 
                    ."<b><i>Vietų skaičius:</i></b><br>" 
                    ."<input type='number' style='width: 500px' name='vietos2' value='$vietusk'><br>" 
                    ."<b><i>Išvykimo data:</i></b><br>" 
                    ."<input type='date' style='width: 500px' name='isvykimas2' value='$isvykimo'><br>" 
                    ."<b><i>Grįžimo data:</i></b><br>" 
                    ."<input type='date' style='width: 500px' name='grizimas2' value='$grizimo'><br>" 
                    ."<b><i>Trukmė dienomis:</i></b><br>" 
                    ."<input type='number' style='width: 500px' name='trukme2' value='$trukme'><br>" 
                    ."<b><i>Keliones aprašymas:</i></b><br>" 
                    ."<textarea name='aprasymas2' style='width: 500px' id='' cols='30' rows='10'>$aprasymas</textarea><br>" 
                    ."<input type='submit' value='Keisti' name='$keisti'><br><br>" 
                    ."</form>";

                    }

                    if (isset($_POST[$keisti])) {
                        $salis2 = $_POST['salis2'];
                        $miestas2 = $_POST['miestas2'];
                        $kaina2 = $_POST['kaina2'];
                        $vietos2 = $_POST['vietos2'];
                        $isvykimas2 = $_POST['isvykimas2'];
                        $grizimas2 = $_POST['grizimas2'];
                        $trukme2 = $_POST['trukme2'];
                        $aprasymas2 = $_POST['aprasymas2'];

                        $sql = "UPDATE keliones SET salis = '$salis2', miestas ='$miestas2', kaina_zmogui = '$kaina2', vietu_skaicius = '$vietos2', 
                        isvykimo_data = '$isvykimas2', grizimo_data = '$grizimas2', trukme_dienomis = '$trukme2', aprasymas = '$aprasymas2' WHERE id = '$id'";
                        $result = mysqli_query($conn, $sql);
                        if($result) echo "YEP";

                        echo "<meta http-equiv='refresh' content='0'>";
                }
                echo '<br><br>';
                
            }
        }
        } else {
            echo '0 results';
        }
    }
}
?>