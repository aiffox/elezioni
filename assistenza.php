<?php
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
        include './php/header.php';
    ?>
    <div id="BarraUtente" class="w3-bar w3-border w3-light-grey">
        <button class="w3-button w3-border-left w3-right" onclick="history.go(-1);">Indietro</button>
    </div>
    <div class="w3-modal-content w3-margin-top" style="text-align:center">
        
        <img src="./img/comingsoon.png" alt="COMING SOON" style="width:60%;height:auto;" />
    </div>
    
    
</body>
</html> 