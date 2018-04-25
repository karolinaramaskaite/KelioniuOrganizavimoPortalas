    <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "keliones";
    
            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

    ?>

<?php
        // Gaunami visų vartotojų padaryti užsakymai
        $sql="SELECT u.id, u.zmoniu_skaicius, u.suma, u.mokejimas_ivykdytas, v.vardas, v.pavarde, v.el_pastas, k.salis, k.miestas, k.vietu_skaicius AS vietos, k.id AS kelionesId
        FROM uzsakymai u 
        inner join vartotojai v 
        on u.vartotojo_id = v.id
        inner join keliones k
        on u.keliones_id = k.id
        group by u.id";
        $result = mysqli_query($conn, $sql);

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
        ."<td>"."<b>Patvirtinti mokėjimą</b>"."</td>"
        ."</tr>";

        if ($result->num_rows > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $id = $row['id'];
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
                $vietusk = $row["vietos"] ;
                $keliones_id = $row["kelionesId"] ;
                echo "<tr>"
                ."<td>"."<i>$vardas</i>"."</td>"
                ."<td>"."<i>$pavarde</i>"."</td>"
                ."<td>"."<i>$elpastas</i>"."</td>"
                ."<td>"."<i>$zmones</i>"."</td>"
                ."<td>"."<i>$suma</i>"."</td>"
                ."<td>"."<i>$mokejimas</i>"."</td>"
                ."<td>"."<i>$salis</i>"."</td>"
                ."<td>"."<i>$miestas</i>"."</td>";
                // Jeigu mokėjimas nėra patvirtintas, eilutėje atsiranda mygtukas "Patvirtinti mokėjimą"
                if($mokejimas == 'Nepatvirtintas') {
                    echo"<td>"
                    ."<form action='' method='post'>"
                    ."<input type='submit' value='Patvirtinti mokėjimą' name='$id'>"
                    ."</form>"
                    ."</td>";
                }
                else {
                    echo "<td></td>";
                }
                echo "<tr>";
                if(isset($_POST[$id])) {
                    // Patvirtinus mokėjimą, duomenų bazėje taip pat patvirtinamas mokėjimas
                    $sql="UPDATE uzsakymai SET mokejimas_ivykdytas = 1 WHERE id = '$id'";
                    $results = mysqli_query($conn, $sql);
                    
                    // Vietų skaičius sumažinamas tiek, kiek buvo nurodyta užsakyme žmonių
                    echo "$vietusk";
                    $vietusk -= $zmones;
                    echo "$vietusk";
                    $sql="UPDATE keliones SET vietu_skaicius = '$vietusk' WHERE id = '$keliones_id'";
                    $results = mysqli_query($conn, $sql);

                    // Gaunamas laukiančiųjų eilėje esančių žmonių skaičius
                    $sql="SELECT laukianciuju_skaicius FROM laukianciuju_eile  WHERE keliones_id = '$keliones_id'";
                    $results = mysqli_query($conn, $sql);
                    $skaicius = 0;
                    if ($results->num_rows > 0) {
                        while($row = mysqli_fetch_assoc($results)) {
                            $skaicius = $row['laukianciuju_skaicius'];
                        }
                    }
                    // Iš laukiančiųjų eilės atimamas užsakyme esančių žmonių skaičius, nes patvirtinus mokėjimą žmonės braukiami iš laukiančiųjų eilės
                    $uzsakym = $skaicius - $zmones;
                    $sql="UPDATE laukianciuju_eile SET laukianciuju_skaicius = '$uzsakym' WHERE keliones_id = '$keliones_id'";
                    $results = mysqli_query($conn, $sql);
                    echo "<meta http-equiv='refresh' content='0'>";
                }
            }
            echo "</table>";
        }
?>
