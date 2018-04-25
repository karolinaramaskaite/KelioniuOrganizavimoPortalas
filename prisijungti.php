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
        <form action"" method="post">
            <b><i>Prisijungimo el. paštas:</i></b><br>
            <input type="email" placeholder="El. paštas" name="elpastas" required><br>
            <b><i>Slaptažodis:</i></b><br>
            <input type="password" placeholder="Slaptažodis" name="slaptazodis" required><br>
            <input type="submit" value="Prisijungti" name="prisijungti"><br>
        </form>
        <i>Neturite paskyros? <a href="?route=registruotis">Užsiregistruokite!</a></i>

        <?php

            if(isset($_POST['elpastas'], $_POST['slaptazodis'])){
                $email = $_POST['elpastas'];
                $slaptazodis = $_POST['slaptazodis'];
                $hash = '';

                $sql="SELECT * FROM vartotojai WHERE el_pastas = '$email'";
                $result = $conn->query($sql);
                $row = mysqli_fetch_array($result);
                if(isset($row)){
                    $hash = $row['slaptazodis'];
                    if (password_verify($slaptazodis, $hash)){
                        $_SESSION['user'] = $row['id'];
                        $_SESSION['vardas'] = $row['vardas'];
                        $_SESSION['pavarde'] = $row['pavarde'];
                        $_SESSION['elpastas'] = $row['el_pastas'];
                        $_SESSION['tipas'] = $row['vartotojo_tipas'];
                        echo "<script type='text/javascript'>  window.location='index.php'; </script>";
                    }
                }
                else echo "Prisijungimo vardas arba slaptažodis yra neteisingi<br>"."$2y$11$3F35MtgmX80PSGT87iOOdecKCxbH.hrW25vYW7svMPVAPYIeGIKo2"."<br>".$slaptazodis;
            }

        ?>

    </div>
</body>
</html>