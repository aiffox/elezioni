<!DOCTYPE html>
<html>
<head>
    <title>Elezioni</title>
    <link rel="shortcut icon" href="./img/logo1.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Andrea Cazzato">
    <link rel="stylesheet" href="./css/w3.css">
    <script type="text/javascript">
        function Valida() {
            return true;
        }
    </script>
<meta charset="UTF-8"></head>
<body onload="update();setInterval(update, 1000);">

    <?php
require './php/classi.php';
require './php/function.php';
        include './php/header.php';
    ?>

    <div class="w3-modal" style="display:block">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Grazie!</h1>
            </header>
            <div class="w3-center"><br>
                <span onclick="window.location.href = './index.php'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
                <img src="img/check.png" alt="Check" style="width:20%" class="w3-circle">
            </div>
            <hr />
            <div class="w3-container">
                <h3 >Il tuo voto è stato registrato correttamente! A breve riceverai una mail contenente il riepilogo del tuo voto.</h3>
            </div>
            <br />
         </div>
    </div>
</body>
</html>