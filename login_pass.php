<?php

    require './php/classi.php';
    require './php/function.php';
include './php/Period_manage.php';
mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
session_start();
IPcheck("./");
if(!isset($_SESSION['EMAIL'])){
    header("location: ./index.php");
}

?>
<html>
<head>
    <title>Elezioni</title>
    <link rel="shortcut icon" href="./img/logo1.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Andrea Cazzato">
    <link rel="stylesheet" href="./css/w3.css">
<meta charset="UTF-8"></head>

<body onload="update(); setInterval(update, 1000);
    <?php

        if (isset($_SESSION['SENT'])) {
            echo "document.getElementById('MailSent').style.display = 'block';";
            unset($_SESSION['SENT']);
        }
        
    ?>
">
      
    <?php
        include './php/header.php';
    ?>
    <div id="BarraUtente" class="w3-bar w3-border w3-light-grey">
        <span id="barra_chiave" class="w3-bar-item w3-border-right w3-left" style="padding-top:3px;padding-bottom:3px">
             <img alt="utente" src="./img/utente.png" style="width: 32px;margin-right:15px"/>
             <?php
                echo $_SESSION['EMAIL'];
             ?>
       </span >
        <a class="w3-button w3-border-left w3-right" href="./index.php">Indietro</a>
    </div>

    <div class="w3-modal-content w3-margin-top">
        <div class="w3-container w3-card-4">
            <header class="w3-container w3-border-bottom w3-display-container">
                <h3>Inserisci la password</h3>
                <div class="w3-display-right">
                    <button class="w3-button w3-circle" onclick="document.getElementById('WTD').style.display = 'block';toggleVideo(true);" style="padding:0;"><img src="./img/question_mark.png" alt="Check" style="width:30px;height:30px;" class="w3-circle" /></button>
                </div>
            </header>
            <br />
            
            <form name="frm2" action="./pass_verifier.php" method="POST">
                <div id="campo_pass" class="w3-container">

                    <input name="email" class="w3-input w3-border w3-round" type="hidden" style="width:90%" value="<?php
                            echo $_SESSION['EMAIL'];
                        ?>" />
                    <label>Password</label>
                    <input name="password" id="js-pass" class="w3-input w3-border w3-round" type="password" style="width:90%" required minlength="6" maxlength="6" />
                    <div class="w3-container" style="margin:0;padding:0">
                        <label class="w3-small w3-text-orange" id="err_pass">
                            <?php
                                if ($_SESSION['send_pass_value'] != '') {
                                    echo $_SESSION['send_pass_value'];
                                }
                                unset($_SESSION['send_pass_value']);
                            ?>
                       </label>
                    </div>
                    <button onclick="" class="w3-button w3-section w3-teal w3-ripple">Login</button>
                </div>
            </form>
        </div>

    </div>
    <div id="MailSent" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Email inviata!</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('MailSent').style.display = 'none';document.getElementById('js-pass').focus();" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="img/email.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <hr />
            <div class="w3-container">
                <h3>Ti abbiamo inviato una mail contenente la password per il login! Se non la trovi, controlla la casella di spam.</h3>
            </div>
        </div>
        <br />
    </div>
    <div id="WTD" class="w3-modal" style="display:none;">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Cosa fare?</h1>
            </header>
            <div class="w3-container">
                <div class="w3-container">
                   <h5 >Controlla la tua casella di posta elettronica. Dovresti trovare una mail dal titolo 'Elezioni' contenente la password per poter accedere. Se non trovi tale mail o hai sbagliato a digitare il tuo indirizzo, clicca sul pulsante 'indietro' posto in alto a destra della pagina e ripeti la procedura. Una volta acquisita la password, inseriscila nel campo "password" e clicca sul pulsante "Login" per accedere alle elezioni.</h5>
                </div>
                <hr style="margin-bottom:0;"/>
                <div class="w3-panel" style="margin-top:0;">
                    <h1 style="text-align:center;">Tutorial video</h1>
                </div>
                <div class="w3-margin-bottom">
                    <video class="w3-border" id="video1" height="auto" width="100%" controls="controls" preload="none" onclick="this.play()" loop>
                        <source type="video/mp4" src="./video/tutorial2.mp4">
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