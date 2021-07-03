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
    $ret = manageData($conn);
    //QUERY SELECT + CONTROLLO CANDIDATI--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    mysqli_close($conn);
    //SET SESSION-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
    /*
        if($_SESSION['IDlista']!=$_SESSION['IDlista']){
        //setcookie('IDlista', "null");
        $_SESSION['nome_lista']= "vuoto";
        }
        else{
        //setcookie('IDlista', $_SESSION['IDlista']);
        $_SESSION['nome_lista']= $nome_lista;
        }
        if($_SESSION['n_candidati']=='null'){
        $_SESSION['IDcandidato1']="";
        $_SESSION['IDcandidato2']="";
        }
        else{
        if($n_cand>=1){
        $_SESSION['IDcandidato1']=$lista_cand[0];
        }
        if($n_cand>=2){
        $_SESSION['IDcandidato2']=$lista_cand[1];
        }
        }*/
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
        function Initialate() {
            if (getCookie('n_candidati') == 'null') {
                if (getCookie('IDlista') == 'null') {
                    document.getElementById('lista').value="vuoto";
                }
                else {
                    document.getElementById('lista').value=getCookie('nome_lista');
                }
                document.getElementById('cand1').value="vuoto";
                document.getElementById('cand2').value="vuoto";
            }
            else if (getCookie('n_candidati') == '1') {
                document.getElementById('lista').value = getCookie("nome_lista").replaceAll("+"," ");
                document.getElementById('cand1').value = getCookie("candidato1_nome").replaceAll("+"," ");
                document.getElementById('cand2').value="vuoto";
            }
            else if (getCookie('n_candidati') == '2') {
                document.getElementById('lista').value=getCookie("nome_lista").replaceAll("+"," ");
                document.getElementById('cand1').value = getCookie("candidato1_nome").replaceAll("+"," ");
                document.getElementById('cand2').value = getCookie("candidato2_nome").replaceAll("+"," ");
            }
            else {
                setCookie("vota_candidati_value", "Errore di acquisizione dei candidati votati. Riprova o contatta l'amministratore. (errore #9050-A)");
                location.href="./vota_candidati.php";
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
                <h3>Confermi le tue scelte?</h3>
                <div class="w3-display-right">
                    <button class="w3-button w3-circle" onclick="document.getElementById('WTD').style.display = 'block';toggleVideo(true);" style="padding:0;"><img src="./img/question_mark.png" alt="Check" style="width:30px;height:30px;" class="w3-circle" /></button>
                </div>
            </header>

            <form action="send_riepilogo.php" name="frm" method="POST">
                <p>
                    <label>Lista votata:</label>
                    
                    <input id="lista" name="lista" class="w3-input w3-border w3-round" type="text" value="<?php echo $ret['nome_lista']; ?>" readonly />
                </p>
                <hr />
                <p>
                    <label>Candidati votati:</label>
                    
                    <input id="cand1" name="cand1" class="w3-input w3-border w3-round w3-margin-bottom" type="text" value="<?php echo $ret['nome_candidato1'] . " " . $ret['cognome_candidato1']; ?>" readonly />
                    <input id="cand2" name="cand2" class="w3-input w3-border w3-round" type="text" value="<?php echo $ret['nome_candidato2'] . " " . $ret['cognome_candidato2']; ?>" readonly />
                </p>
                <p>
                    <button class="w3-button w3-section w3-teal w3-ripple w3-left" onclick="document.frm.action = 'vota_lista.php'; document.submit();"> Indietro </button>
                    <button class="w3-button w3-section w3-teal w3-ripple w3-right" onclick="document.submit();"> Conferma </button>
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
                    <h5 >Hai davanti a te un riepilogo della lista e dei candidati a cui darai il tuo voto. 
                        <br /> Se le scelte presenti nel riepilogo non rispecchiano le tue scelte fatte nel corso della votazione, clicca su 'indietro' e ripeti la proccedura; altrimenti clicca su 'conferma'per confermare il tuo voto.
                    </h5>
                </div>
                <hr style="margin-bottom:0;"/>
                <div class="w3-panel" style="margin-top:0;">
                    <h1 style="text-align:center;">Tutorial video</h1>
                </div>
                <div class="w3-margin-bottom">
                    <video class="w3-border" id="video1" height="auto" width="100%" controls="controls" preload="none" onclick="this.play()" loop>
                        <source type="video/mp4" src="./video/tutorial5.mp4">
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