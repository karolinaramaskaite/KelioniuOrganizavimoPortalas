
<?php
            $servername = "localhost";
            $username = "karram1";
            $password = "wa3thaiLitohmaev";
            $dbname = "karram1";
    
            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

    ?>

<?php
        $userId = $_SESSION['user'];
        // Gaunami vartotojo padaryti užsakymai
        $sql="SELECT u.id, u.zmoniu_skaicius, u.suma, u.mokejimas_ivykdytas, v.vardas, 
        v.pavarde, v.el_pastas, k.salis, k.miestas, k.isvykimo_data, k.grizimo_data, k.id as kid
        FROM uzsakymai u 
        inner join vartotojai v 
        on u.vartotojo_id = v.id
        inner join keliones k
        on u.keliones_id = k.id
        where v.id = $userId ";
        $result = $conn->query($sql);
        
        // Užsakymai spausdinami į lentelę
        echo "<table>"
        ."<tr>"
        ."<td>"."<b>Vardas</b>"."</td>"
        ."<td>"."<b>Pavardė</b>"."</td>"
        ."<td>"."<b>El. paštas:</b>"."</td>"
        ."<td>"."<b>Žmonių skaičius</b>"."</td>"
        ."<td>"."<b>Suma (eur)</b>"."</td>"
        ."<td>"."<b>Mokėjimas</b>"."</td>"
        ."<td>"."<b>Šalis</b>"."</td>"
        ."<td>"."<b>Miestas</b>"."</td>"
        ."<td>"."<b>Išvykimo data</b>"."</td>"
        ."<td>"."<b>Grįžimo data</b>"."</td>"
        ."<td>"."<b>Redaguoti/Trinti</b>"."</td>"
        ."</tr>";

        if ($result) {
            while($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $trinti = 'trinti'.$id;
                $zmones = $row['zmoniu_skaicius'];
                $suma = $row["suma"] ;
                if($row["mokejimas_ivykdytas"] == 0)
                $mokejimas = 'Nepatvirtintas';
                else $mokejimas = 'Patvirtintas';
                $vardas = $row["vardas"] ;
                $pavarde = $row["pavarde"] ;
                $elpastas = $row["el_pastas"] ;
                $salis = $row["salis"] ;
                $miestas = $row["miestas"] ;
                $isvykimas = $row["isvykimo_data"] ;
                $grizimas = $row["grizimo_data"] ;
                $kid = $row["kid"];
                echo "<tr>"
                ."<td>"."<i>$vardas</i>"."</td>"
                ."<td>"."<i>$pavarde</i>"."</td>"
                ."<td>"."<i>$elpastas</i>"."</td>"
                ."<td>"."<i>$zmones</i>"."</td>"
                ."<td>"."<i>$suma</i>"."</td>"
                ."<td>"."<i>$mokejimas</i>"."</td>"
                ."<td>"."<i>$salis</i>"."</td>"
                ."<td>"."<i>$miestas</i>"."</td>"
                ."<td>"."<i>$isvykimas</i>"."</td>"
                ."<td>"."<i>$grizimas</i>"."</td>";
                if($mokejimas == 'Nepatvirtintas') {
                    echo"<td>"
                    ."<form action='' method='post'>"
                    ."<input type='submit' value='Redaguoti' name='$id'>"
                    ."</form>"
                    ."<form action='' method='post'>"
                    ."<input type='submit' value='Trinti' name='$trinti'>"
                    ."</form>"
                    ."</td>";
                    echo "<tr>";
                }
                else {
                    echo "<td></td>";
                    echo "<tr>";
                }
                

                $uzsakymas = 'uzsakymas'.$id;

                if(isset($_POST[$id])) {
                    echo "<form action='' method='post'>"
                    .'<b><i>Redaguokite keliaujančių žmonių skaičių:</i></b><br>'
                    ."<input type = 'number' placeholder='Žmonių skaičius' name='$uzsakymas' required><br>"
                    ."<input type = 'submit' value='Užsakymas' name='patvirtinta'>"
                    .'</form>'; 
                }
                

                if(isset($_POST[$uzsakymas])) {
                    $uzsak = $_POST[$uzsakymas];
                    $kaina = $suma / $zmones;
                    $suma = $uzsak * $kaina;           
                    $mokejimas = false;
                    $vartotojas = $_SESSION['user']; 
                    $sql="UPDATE uzsakymai SET suma = '$suma', zmoniu_skaicius = '$uzsak' WHERE id = '$id'";
                    $results = mysqli_query($conn, $sql);
                    $sql="SELECT laukianciuju_skaicius FROM laukianciuju_eile  WHERE keliones_id = '$kid'";
                    //Jau šios kelionės laukiančiųjų eilėje esantis skaičius
                    $skaicius = 0;
                    $results = $conn->query($sql);
                    $rows = mysqli_fetch_array($results);
                    if(isset($rows)){
                        $skaicius = $rows['laukianciuju_skaicius'];
                    }
                    // Prie laukiančiųjų eilėje skaičiaus pridedamas dabar užsisakiusiųjų skaičius ir tai įdedama į laukiančiųjų eilės lentelę
                    $uzsak = $uzsak - $zmones;
                    $uzsakym = $uzsak + $skaicius;
                    $sql="UPDATE laukianciuju_eile SET laukianciuju_skaicius = '$uzsakym' WHERE keliones_id = '$kid'";
                    $results = mysqli_query($conn, $sql);
                    echo "<meta http-equiv='refresh' content='0'>";
                }      
                
                if(isset($_POST[$trinti])) {
                    $sql="DELETE FROM uzsakymai WHERE id = '$id'";
                    $results = mysqli_query($conn, $sql);
                    $sql="SELECT laukianciuju_skaicius FROM laukianciuju_eile  WHERE keliones_id = '$kid'";
                    //Jau šios kelionės laukiančiųjų eilėje esantis skaičius
                    $skaicius = 0;
                    $results = $conn->query($sql);
                    $rows = mysqli_fetch_array($results);
                    if(isset($rows)){
                        $skaicius = $rows['laukianciuju_skaicius'];
                    }
                    // Prie laukiančiųjų eilėje skaičiaus pridedamas dabar užsisakiusiųjų skaičius ir tai įdedama į laukiančiųjų eilės lentelę
                    $uzsakym = $skaicius - $zmones;
                    $sql="UPDATE laukianciuju_eile SET laukianciuju_skaicius = '$uzsakym' WHERE keliones_id = '$kid'";
                    $results = mysqli_query($conn, $sql);
                    echo "<meta http-equiv='refresh' content='0'>";
                }
            }
            
        }
        echo"</table>"; 
?>
