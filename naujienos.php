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
if ($_SESSION) {
    echo "<i><a href='?route=naujienlaiskis'>Mano užsisakytos naujienos</a></i><br>";
}
?>
<div class="login">
<form action"" method="post">
    <b><i>Pradžios data:</i></b><br>
    <input type="date" name="pradzia" required><br>
    <b><i>Pabaigos data:</i></b><br>
    <input type="date" name="pabaiga" required><br>
    <input type="submit" value="Užsisakyti" name="uzsisakyti"><br>
</form>

<?php
    if(isset($_POST['uzsisakyti'])){
        $id = $_SESSION['user'];
        $pradzia = $_POST['pradzia'];
        $pabaiga = $_POST['pabaiga'];

        $sql = "INSERT INTO naujienlaiskis (pradzios_data, pabaigos_data, vartotojo_id) VALUES('$pradzia', '$pabaiga', '$id')";
        $result = $conn->query($sql);

    }
?>
