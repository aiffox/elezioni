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
    require '../php/classi.php';
    require '../php/function.php';
    include '../php/Period_manage.php';
    mb_internal_encoding("UTF-8");
    session_start();
    IPcheck("../");
?>
<html>
<head>
    <title>Elezioni</title>
    <link rel="shortcut icon" href="../img/logo1.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Andrea Cazzato">
    <link rel="stylesheet" href="../css/w3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta charset="UTF-8"></head>

<body class="" onload="update(); setInterval(update, 2000);"> 
   <?php
    if(!isset($_SESSION['send_mail_value'])){
        $_SESSION['send_mail_value']="";
    }
    ?> 

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
    
    <div class="w3-modal-content w3-margin-top">
        <div class="w3-container w3-card-4">
            
            <header class="w3-container w3-border-bottom w3-display-container">
                <h3>Effettua l'accesso</h3>
            </header>
            <br />
            <form name="frm1" id="frm1" action="./user_verify.php" method="POST">
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
                    <button onclick="" class="w3-button w3-section w3-teal w3-ripple">Avanti</button>
                </div>
            </form>
        </div>
    </div>
    <?php if($_SESSION['DISABLE_AVVERTENZA']==1){ ?>
    <div id="id01" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">ATTENZIONE</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <hr />
            <div class="w3-container">
                <h3>Stai tentando di accedere ad un'area riservata ai soli amministratori. L'accesso a quest'area da parte di non autorizzati e' severamente vietato.</h3>
            </div>
        </div>
        <br />
    </div>
    <?php } $_SESSION['DISABLE_AVVERTENZA']=1; ?>
</body>
</html> 