<html>
    <head>
        <title>Elezioni <?php echo date("Y")."-".(date("Y")+1); ?></title>
        <link rel="shortcut icon" href="./img/logo1.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Andrea Cazzato">
        <link rel="stylesheet" href="./css/w3.css">
    <meta charset="UTF-8"></head>
    <body onload="update();setInterval(update, 1000);">

            <?php
require './php/classi.php';
require './php/function.php';
        include './php/header.php';
    ?>
    
        <div class="w3-modal-content w3-margin-top">
            <div class="w3-container w3-card-4">
                <header class="w3-container w3-border-bottom">
                    <h1 class="" style="text-shadow:1px 1px 0 #444;text-align:center"><b>OOPS...</b></h1>
                </header>
                <div class="w3-container">
                    <h3>la tua sessione e' scaduta. Accedi per continuare.</h3>
                    <p style=" text-align: center;">
                        <button class="w3-button w3-section w3-teal w3-ripple w3-xlarge" onclick="location.href='./index.php'"> Accedi </button>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html> 
