<?php 

    function IPcheck($root){
        $MAX_VISITE=5;
        $LASSO_DI_TEMPO_IN_MINUTI=5;

        $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
        if ($conn->connect_error) {
            logError("Tracciamento non attivo per ".$_SERVER['REMOTE_ADDR'],"null");
            header("location: ./index.php");
            exit();
        }
        $conn->set_charset("utf8");

        $sql = "SELECT * FROM periodi_utilizzo WHERE periodi_utilizzo.IP='".$_SERVER['REMOTE_ADDR']."'";
        $result=$conn->query($sql);

        if ($result->num_rows <= 0) {
            $sql = "INSERT INTO periodi_utilizzo (periodi_utilizzo.IP) VALUES ('".$_SERVER['REMOTE_ADDR']."')";
            $conn->query($sql);
        }
        else{
            $data=$result->fetch_assoc();
            $hourAgo = date('Y-m-dTH:i H:i:s', strtotime("-$LASSO_DI_TEMPO_IN_MINUTI minute"));

            if($data['inizio_periodo']>$hourAgo){
                $sql = "UPDATE periodi_utilizzo SET n_visite=n_visite+1 WHERE IP='".$_SERVER['REMOTE_ADDR']."'";
                if($data['n_visite']>=$MAX_VISITE){
                    $date1 = new DateTime(date('H:i:s',strtotime("-$LASSO_DI_TEMPO_IN_MINUTI minute")));
                    $date2 = new DateTime($data['inizio_periodo']);
                    $time_to_wait = $date1->diff($date2)->format("%i minuti e %s secondi");
?>
                    <head>
                        <title>Amministrazione</title>
                        <link rel="shortcut icon" href="<?php echo $root; ?>img/logo1.ico" />
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <meta name="author" content="Andrea Cazzato">
                        <link rel="stylesheet" href="<?php echo $root; ?>css/w3.css">
                        <meta charset="UTF-8">
                    </head>
                    <body onload="update();setInterval(update, 1000);/*Controllo_stato();*/<?php if(isset($_SESSION['AMM_ALERT'])){ echo "document.getElementById('id01').style.display = 'block'"; unset($_SESSION['AMM_ALERT']);}?>">
                        <header class="w3-container w3-teal">
                            <h1 style="text-align:center">Amministrazione<span style="text-align:right;" class="w3-right w3-large w3-section" id="Orario"></span></h1>
                            <script> 
                                var update = function () {
                                    date = new Date();
                                    let min = date.getMinutes();
                                    let sec = date.getSeconds();
                                    if (min<10) {
                                        min = '0'+min;
                                    }/*
                                    if (sec<10) {
                                        sec = '0'+sec;
                                    }*/
                                    document.getElementById('Orario').innerHTML = date.getHours() + ":" + min/*+ ":" + sec*/;
                                };
                        </script>
                        </header>
                        <div id="BarraUtente" class="w3-bar w3-border w3-light-grey">
                            <span id="barra_chiave" class="w3-bar-item w3-border-right w3-left" style="padding-top:3px;padding-bottom:3px">
                                <img alt="utente" src="<?php echo $root; ?>img/amministratore.png" style="width: 32px;margin-right:15px" /><?php if(isset($_SESSION['AUT_email'])){ echo $_SESSION['AUT_email'];} else{ echo$_SESSION['AMM_email'];} ?>
                            </span>
                            <button class="w3-button w3-border-left w3-right" onclick="location.href = '<?php echo $root; ?>esci.php';">Esci</button>
                        </div>
                        <div class="w3-modal-content w3-margin-top">
                            <div class="w3-container w3-card-4">
                                <header class="w3-container w3-border-bottom">
                                    <h2>Azione bloccata</h2>
                                </header>
                                <div class="w3-container">
                                    <h4>
<?php
                                        echo "Hai effettuato troppe operazioni negli ultimi $LASSO_DI_TEMPO_IN_MINUTI minuti. Torna tra $time_to_wait.";
?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </body>
<?php
                    logError("IL DISPOSITIVO STA EFFETTUANDO TROPPE OPERAZIONI!",$_SERVER['REMOTE_ADDR']);
                    exit();
                }
            }
            else{
                $sql = "UPDATE periodi_utilizzo SET n_visite=1, inizio_periodo=CURRENT_TIMESTAMP() WHERE IP='".$_SERVER['REMOTE_ADDR']."'";
            }
            $conn->query($sql);
        }
        mysqli_close($conn);
    }

    function IPrestore(){
        $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
        if ($conn->connect_error) {
            logError("Tracciamento IP non attivo per ".$_SERVER['REMOTE_ADDR'],"null");
            header("location: ./index.php");
            exit();
        }
        $conn->set_charset("utf8");

        $sql = "UPDATE periodi_utilizzo SET n_visite=n_visite-1 WHERE IP='".$_SERVER['REMOTE_ADDR']."'";
        $conn->query($sql);

        mysqli_close($conn);
    }

?>