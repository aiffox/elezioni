<?php
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    require './php/classi.php';
    require './php/function.php';
    session_start();
    $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
    // Check connection
    if ($conn->connect_error) {
        $error = $_SESSION['send_mail_value'] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #0300)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
        header("location: ./index.php");
        exit();
    }
    $conn->set_charset("utf8");
    manageStart($conn);
    manageConsent($conn);
    /*
        if(strcmp($dati['consenso'], "1")!=0 || strcmp($dati['letto_regole'], "1")!=0){
        setcookie("err_consenso", "Sembra che tu non abbia ancora accettato i termini e il regolamento. Accettali per continuare. (errore #9025)");
        header("location: ./termini_e_condizioni.php");
        exit();
        }*/
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    mysqli_close($conn);
?>

<html>
    <head>
        <title>Elezioni</title>
        <link rel="shortcut icon" href="./img/logo1.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Andrea Cazzato">
        <link rel="stylesheet" href="./css/w3.css">
        <script type="text/javascript">
            function eraseCookie(name) {
                document.cookie = name+'=; Max-Age=-99999999;';
            }

            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                var expires = "expires="+ d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                      c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }/*
            function Initialize() {
                if (getCookie("vota_lista_value") != "") {
                    document.getElementById('voto_err').innerHTML = getCookie("vota_lista_value").replaceAll("+", " ");
                    eraseCookie("vota_lista_value");
                }
            }*/
        </script>
    <meta charset="UTF-8"></head>
    <body onload="update();setInterval(update, 1000);">

        <?php
            include './php/header.php';
        ?>
        <div id="BarraUtente" class="w3-bar w3-border w3-light-grey">
            <span id="barra_chiave" class="w3-bar-item w3-border-right w3-left" style="padding-top:3px;padding-bottom:3px">
                <img alt="utente" src="./img/utente.png" style="width: 32px;margin-right:15px" /><?php echo $_SESSION['AUT_email']; ?>
           </span>
            <button class="w3-button w3-border-left w3-right" onclick="location.href = './esci.php';">Esci</button>
        </div>
        <div class="w3-modal-content w3-margin-top">
            <div class="w3-container w3-card-4">
                <header class="w3-container w3-border-bottom w3-display-container">
                    <h3>Applica una spunta sulla tua lista preferita</h3>
                    <div class="w3-display-right">
                        <button class="w3-button w3-circle" onclick="document.getElementById('WTD').style.display = 'block';toggleVideo(true);" style="padding:0;"><img src="./img/question_mark.png" alt="Check" style="width:30px;height:30px;" class="w3-circle" /></button>
                    </div>
                </header>

                <form action="valida_voto_lista.php" name="frm" method="POST">
                    <?php
                        $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
                        // Check connection
                        if ($conn->connect_error) {
                            //echo "server";
                            echo "<p class=\"w3-xxlarge w3-text-orange\" style=\"margin:0\">Errore caricamento liste. (errore #726)</p>";
                            logError("Errore caricamento liste. (errore #726)",$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
    
                        }
                        $sql = "SELECT nome FROM liste";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($dati = $result->fetch_assoc()) {
                                echo "   <p>";
                                echo "       <input class=\"w3-radio\" type=\"radio\" name=\"lista\" value=\"" . ($dati['nome']) . "\" required>";
                                echo "       <label>" . $dati['nome'] . "</label>";
                                echo "   </p>";
                            }
                        } else {
                            echo "<p class=\"w3-xxlarge w3-text-orange\" style=\"margin:0\">Errore caricamento liste. (errore #727)</p>";
                            logError("Errore caricamento liste. (errore #727)",$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
                        }
                    ?>
                   <hr />
                    <p>
                        <input class="w3-radio" type="radio" name="lista" value="null" required />
                        <label>Non voglio esprimere preferenze</label>
                    </p>
                    <div class="w3-container" style="margin:0;padding:0">
                        <label class="w3-small w3-text-orange" id="voto_err" style="margin:0">
                            <?php
                                if ($_SESSION['vota_lista_value'] != "" && isset($_SESSION['vota_lista_value'])) {
                                    echo $_SESSION['vota_lista_value'];
                                    $_SESSION['vota_lista_value'] = "";
                                }
                            ?>
                       </label>
                    </div>
                    <p>
                        <input class="w3-button w3-section w3-teal w3-ripple  w3-right" type="submit" value="Continua"/>
                    </p>
                </form>
            </div>

        </div>
        <div id="WTD" class="w3-modal" style="display:none;">
            <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
                <header class="w3-container w3-teal">
                    <h1 style="text-align:center">Cosa fare?</h1>
                </header>
                <div class="w3-container">
                    <div class="w3-container">
                        <h5 >Seleziona la lista a cui vuoi dare il tuo voto, dopodiché clicca sul pulsante “continua” per recarti nella sezione di voto relativa ai candidati della lista votata.
                            <br />Qualora non volessi esprimere preferenze, seleziona la voce “Non voglio esprimere preferenze” posta alla fine dell’elenco di liste, dopodiché clicca sul pulsante “continua” per recarti nella sezione di riepilogo. 
                            <br />Tieni presente che, qualora tu non abbia espresso una preferenza, non potrai neanche applicare delle preferenze sui candidati.
                            <br />Se credi di star riscontrando dei problemi nella visualizzazione delle liste, clicca sul pulsante “assistenza”, per metterti in contatto con il personale, che ti aiuterà a risolvere il tuo problema.
                        </h5>
                    </div>
                    <hr style="margin-bottom:0;"/>
                    <div class="w3-panel" style="margin-top:0;">
                        <h1 style="text-align:center;">Tutorial video</h1>
                    </div>
                    <div class="w3-margin-bottom">
                        <video class="w3-border" id="video1" height="auto" width="100%" controls="controls" preload="none" onclick="this.play()" loop>
                            <source type="video/mp4" src="./video/tutorial3.mp4">
                        </video>
                    </div>
                
                    <script type="text/javascript">
                        function toggleVideo(play) {
                            var video = document.getElementById("video1");
                            if (play) {
                                video.play();
                            }
                            else {
                                video.pause();
                                video.currentTime = 0;
                            }
                        }
                    </script>
                    <input type="button" class="w3-button w3-teal w3-left" value="Assistenza" onclick="toggleVideo(false); location.href='./assistenza.php'"/>
                    <input type="button" onclick="document.getElementById('WTD').style.display = 'none'; toggleVideo(false);" class="w3-button w3-teal w3-right" value="Ho capito!" />
                </div>
                <br />
            </div>
            <br />
        </div>
    </body>
</html>