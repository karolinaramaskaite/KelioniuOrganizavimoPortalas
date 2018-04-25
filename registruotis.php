<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="styles/styles.css">
</head>
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
<body>
    <div class="login">
        <form action="" method="post">
            <b><i>Vardas:</i></b><br>
            <input type="text" placeholder="Vardas" name="vardas" required><br>
            <b><i>Pavardė:</i></b><br>
            <input type="text" placeholder="Pavarde" name="pavarde" required><br>
            <b><i>Prisijungimo el. paštas:</i></b><br>
            <input type="email" placeholder="El. paštas" name="el_pastas" required><br>
            <b><i>Slaptažodis:</i></b><br>
            <input type="password" placeholder="Slaptažodis" name="slaptazodis" required><br>
            <b><i>Pakartokite slaptažodį:</i></b><br>
            <input type="password" placeholder="Pakartokite slaptažodį" name="slaptazodis2" required><br>
            <input type="submit" value="Registruotis" name="registruotis"><br>
        </form>


        <?php
            // Duomenys iš registracijos formos įvedami į lentelę
            if(isset($_POST['vardas'], $_POST['pavarde'], $_POST['el_pastas'], $_POST['slaptazodis'], $_POST['slaptazodis2'])){
                $vardas = $_POST['vardas'];
                $pavarde = $_POST['pavarde'];
                $elpastas = $_POST['el_pastas'];
                $slaptazodis = $_POST['slaptazodis'];
                $pakartojimas = $_POST['slaptazodis2'];
                $vartotojas = "vartotojas";
                if($slaptazodis === $pakartojimas) {
                    $options = [
                        'cost' => 11,
                    ];
                    
                    $slaptazodis = password_hash($pakartojimas, PASSWORD_BCRYPT, $options);

                    $sql="INSERT INTO vartotojai (vardas, pavarde, el_pastas, vartotojo_tipas, slaptazodis) VALUES('$vardas', '$pavarde', '$elpastas', '$vartotojas', '$slaptazodis')";
                    $result = $conn->query($sql);
                }
            }
        ?>
    </div>
</body>
</html>