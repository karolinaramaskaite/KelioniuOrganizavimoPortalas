<?php
	session_start();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="styles/styles.css">
    <h1></h1>

    <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "keliones";
    
            // Create connection
            $conn = mysqli_connect($servername, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

    ?>
</head>
<body>
<div class="container">
    <div class="header-container">
        <header>
            <div class="header">
                <h1 class="title">Kelionių organizavimo portalas</h1>
                    <nav>
                        <ul>
                            <li><a href="?route=pasiulymai">Pasiūlymai</a></li>
                            <li><a href="?route=uzsisakyti_naujienas">Naujienlaiškis</a></li>
                            <?php
                                if ($_SESSION) {
                                    echo "<li><a href='?route=atsijungti'>Atsijungti</a></li>";
                                }
                                else echo "<li><a href='?route=prisijungti'>Prisijungti</a></li>";
                            ?>             
                        </ul>
                    </nav>
            </div>
        </header>        
    </div>
    <div class="main-container">
        <div class="main">

            <article>
            <?php 
                if(isset($_GET['route'])){
                    switch($_GET['route']){
                        case 'pasiulymai':
                            include ('pasiulymai.php');
                            break;  
                        case 'uzsisakyti_naujienas':
                            include ('naujienos.php');
                            break;
                        case 'prisijungti':
                            include 'prisijungti.php';
                            break;
                        case 'registruotis':
                            include 'registruotis.php';
                            break;
                        case 'uzsakymai':
                            include 'uzsakymai.php';
                            break;
                        case 'visi_uzsakymai':
                            include 'visi-uzsakymai.php';
                            break;
                        case 'atsijungti':
                            include 'atsijungti.php';
                            break;
                        case 'naujienlaiskis':
                            include 'naujienlaiskis.php';
                            break;
                    }
                }
                else
                include ('home.php');
                ?>
              
            </article>
        </div>
    <div class="footer-container">
        <footer>
            <h3><i>Karolina Ramaškaitė IFF-5/1</i></h3>
        </footer>
    </div>
    </div>
</div>
</body>
</html>