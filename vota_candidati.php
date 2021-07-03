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
        logError($error, $conn->real_escape_string((isset($_SESSION['AUT_email'])) ? $_SESSION['AUT_email'] : "null"));
        header("location: ./index.php");
        exit();
    }
    $conn->set_charset("utf8");
    manageStart($conn);
    manageConsent($conn);
    if (isset($_SESSION['IDlista'])) {
        if ($_SESSION['IDlista'] == - 1) {
            mysqli_close($conn);
            header("location: ./riepilogo.php");
            exit();
        }
    } else {
        $error=$_SESSION['vota_lista_value'] = "Errore nell'acquisizione del voto! Riprova o contatta l'amministratore. (errore #0700)";
        logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email'])) ? $_SESSION['AUT_email'] : "null"));
        header("location: ./vota_lista.php");
        exit();
    }
    ?>
<html>
    <head>
        <meta charset="UTF-8">
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
                Uncheck(document.getElementById('no_voto'));
                if (getCookie("vota_candidati_value") != "") {
                    document.getElementById('cand_err').innerHTML = getCookie("vota_candidati_value").replaceAll("+", " ");
                    eraseCookie("vota_candidati_value");
                }
            }*/

            function Check(sel) {
                document.getElementById('cand_err').innerHTML = "";
                var chk_arr =  document.getElementsByName("cand[]");
                var chklength = chk_arr.length;

                var c=0;
                for (k = 0; k < chklength; k++){
                    if (chk_arr[k].checked) {
                        c++;
                    }
                }
                if (c>2) {
                    sel.checked = false;
                }
            }

            function Uncheck(sel) {
                document.getElementById('cand_err').innerHTML = "";
                var chk_arr =  document.getElementsByName("cand[]");
                var chklength = chk_arr.length;

                for(k=0;k< chklength;k++){
                    chk_arr[k].disabled  = sel.checked;
                } 
            }
            function ok() {
                document.getElementById('cand_err').innerHTML = "";
                var ok = false;
                ok = document.getElementById("no_voto").checked;
                var chk_arr =  document.getElementsByName("cand[]");
                var chklength = chk_arr.length;

                for (k = 0; k < chklength; k++){
                    if (chk_arr[k].checked) {
                        ok = true;
                    }
                }
                return ok;
            }
        </script>
   </head>
    <body onload="update();setInterval(update, 1000);<?php if ($_SESSION['vota_candidati_value'] != "") {   echo "Uncheck(document.getElementById('no_voto'));";} ?>/*Initialize()*/">

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
                    <h3>Candidati della lista <?php 
                        $sql = "SELECT nome FROM liste WHERE nome = '" . $conn->real_escape_string($_SESSION['IDlista']) . "'";
                        $result = $conn->query($sql);
                        if ($result->num_rows <= 0) {
                            $error=$_SESSION['vota_lista_value'] = "Voto non valido! Riprova o contatta l'amministratore. (errore #0708)";
                            logError($error,(isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null"));
                            header("location: ./vota_lista.php");
                            exit();
                        }
                        else{
                            $dati = $result->fetch_assoc();
                            echo $dati['nome'].":";
                        }
                        ?>
                    </h3>
                    <div class="w3-display-right">
                        <button class="w3-button w3-circle" onclick="document.getElementById('WTD').style.display = 'block';toggleVideo(true);" style="padding:0;"><img src="./img/question_mark.png" alt="Check" style="width:30px;height:30px;" class="w3-circle" /></button>
                    </div>
                </header>

                <form action="valida_voto_candidati.php" name="frm" method="POST">
                    <?php
                        //QUERY SELECT--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        
                        $sql = "SELECT candidati.nome,candidati.cognome,candidati.codice_fiscale FROM candidati WHERE candidati.nome_lista='" . $conn->real_escape_string($_SESSION['IDlista'])."'";
                        $result = $conn->query($sql);
                        if ($result->num_rows <= 0) {
                            echo "<p class=\"w3-xxlarge w3-text-orange\" style=\"margin:0\">Errore caricamento candidati. (errore #0709)</p>";
                            logError("Errore caricamento candidati. (errore #0709)",(isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null"));
                        }
                        //INJECT DATI---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        while ($dati = $result->fetch_assoc()) {
                            echo "   <p>";
                            echo "      <input class=\"w3-check\" name=\"cand[]\" type=\"checkbox\" onchange=\"Check(this)\" value=\"" . $conn->real_escape_string($dati['codice_fiscale']) . "\">";
                            echo "      <label>" . $dati['nome'] . " " . $dati['cognome'] . "</label>";
                            echo "   </p>";
                        }
                        //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        mysqli_close($conn);
                    ?>

                    <hr />
                    <p>
                        <input class="w3-check" id="no_voto" name="no_voto" onchange="Uncheck(this)" type="checkbox" value="null" />
                        <label>Non voglio esprimere preferenze</label>
                    </p>
                    <div class="w3-container" style="margin:0;padding:0">
                        <label class="w3-small w3-text-orange" id="cand_err" style="margin:0">
                            <?php
                                if ($_SESSION['vota_candidati_value'] != "") {
                                    echo $_SESSION['vota_candidati_value'];
                                    $_SESSION['vota_candidati_value'] = "";
                                }
                            ?>

                        </label>
                    </div>
                    <p>
                        <button class="w3-button w3-section w3-teal w3-ripple w3-left" onclick="document.frm.action = 'vota_lista.php'; "> Indietro </button>
                        <input type="button" class="w3-button w3-section w3-teal w3-ripple w3-right" onclick="if (ok()) { document.frm.submit(); } else { document.getElementById('cand_err').innerHTML='Selezione almeno una voce.' }" value="Continua" />
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
                        <h5 >Seleziona da uno a due candidati a cui dare il tuo voto.
                            <br />Qualora non volessi esprimere preferenze, seleziona la voce "Non voglio esprimere preferenze" posta alla fine dell'elenco di candidati, dopodiché clicca sul pulsante "continua" per recarti nella sezione di riepilogo. 
                            <br />Qualora avessi cambiato idea sulla tua preferenza riguardo le liste, clicca sul pulsante "indietro" e ripeti la procedura di votazione della lista.
                            <br />Se credi di star riscontrando dei problemi nella visualizzazione dei candidati, clicca sul pulsante "assistenza", per metterti in contatto con il personale, che ti aiuterà a risolvere il tuo problema.
                        </h5>
                    </div>
                    <hr style="margin-bottom:0;"/>
                    <div class="w3-panel" style="margin-top:0;">
                        <h1 style="text-align:center;">Tutorial video</h1>
                    </div>
                    <div class="w3-margin-bottom">
                        <video class="w3-border" id="video1" height="auto" width="100%" controls="controls" preload="none" onclick="this.play()" loop>
                            <source type="video/mp4" src="./video/tutorial4.mp4">
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