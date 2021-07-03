<?php
if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || 
   $_SERVER['HTTPS'] == 1) ||  
   isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&   
   $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
{
   $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
   header('HTTP/1.1 301 Moved Permanently');
   header('Location: ' . $redirect);
   exit();
}
    require './php/classi.php';
    require './php/function.php';
    include './php/Period_manage.php';
    mb_internal_encoding("UTF-8");
    session_start();
    IPcheck("./");
?>
<html>
<head>
    <title>Elezioni</title>
    <link rel="shortcut icon" href="./img/logo1.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Andrea Cazzato">
    <link rel="stylesheet" href="./css/w3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta charset="UTF-8"></head>

<body class="" onload="update(); setInterval(update, 2000);
    

<?php
    if(!isset($_SESSION['send_mail_value'])){
        $_SESSION['send_mail_value']="";
    }
    ?>
">  
    

    <?php
        require './php/header.php';
    ?>
    
    <div class="w3-modal-content w3-margin-top">
        <div class="w3-container w3-card-4">
            
            <header class="w3-container w3-border-bottom w3-display-container">
                <h3>Effettua l'accesso</h3>
                <div class="w3-display-right">
                    <button class="w3-button w3-circle" onclick="document.getElementById('WTD').style.display = 'block';toggleVideo(true);" style="padding:0;"><img src="./img/question_mark.png" alt="Check" style="width:30px;height:30px;" class="w3-circle" /></button>
                </div>
            </header>
            <br />
            <form name="frm1" id="frm1" action="./send_mail.php" method="POST">
                <div id="campo_email" class="w3-container">
                    <label>Email</label>
                    <input name="email" id="js-email" class="w3-input w3-border w3-round" type="email" style="width:90%" required value="<?php
                            if (MAIL_VALIDATE($_SESSION['EMAIL'])) {
                                echo $_SESSION['EMAIL'];
                            }
                        ?>" />
                    <div class="w3-container" style="margin:0;padding:0">
                        <label class="w3-small w3-text-orange" id="err_email">
                            <?php
                                echo $_SESSION['send_mail_value'];
                                unset($_SESSION['send_mail_value']);
                            ?>
                       </label>
                    </div>
                    <button onclick="" class="w3-button w3-section w3-teal w3-ripple">Ricevi Password</button>
                </div>
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
                    <h5 >Inserisci l'indirizzo di posta elettronica fornito dalla scuola. Il formato di tale indirizzo è '<b>COGNOME.NOME@itismaglie.it</b>'.</h5>
                </div>
                <hr style="margin-bottom:0;"/>
                <div class="w3-panel" style="margin-top:0;">
                    <h1 style="text-align:center;">Tutorial video</h1>
                </div>
                <div class="w3-margin-bottom">
                    <video class="w3-border" id="video1" height="auto" width="100%" controls="controls" preload="none" onclick="this.play()" loop>
                        <source type="video/mp4" src="./video/tutorial1.mp4">
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