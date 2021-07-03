<?php
mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
session_start();
?>

<html>
<head>
    <title>Amministrazione</title>
    <link rel="shortcut icon" href="../img/logo1.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Andrea Cazzato">
    <link rel="stylesheet" href="../css/w3.css">
    
<meta charset="UTF-8"></head>

<body onload="update();setInterval(update, 1000);">

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
            <img alt="utente" src="../img/amministratore.png" style="width: 32px;margin-right:15px" /><?php echo $_SESSION['AMM_email'] ?>
        </span>
        <button class="w3-button w3-border-left w3-right" onclick="location.href = './esci.php';">Esci</button>
    </div>

    <div class="w3-modal-content w3-margin-top">
        <div class="w3-container w3-card-4">
            <header class="w3-container w3-border-bottom">
                <h3>Effettua l'accesso</h3>
            </header>
            <br />
            <form name="frm" action="./pass_verifier.php" method="POST">
                <label>Password</label>
                <input name="password" id="input_pass" class="w3-input w3-border w3-round" type="password" style="width:90%" required minlength="12" maxlength="12" />
                <div class="w3-container" style="margin:0;padding:0">
                    <label class="w3-small w3-text-orange" id="err_pass" style="margin:0;width:100%">
                        <?php
                            if (isset($_SESSION['AMM_pass_value'])) {
                                echo $_SESSION['AMM_pass_value'];
                            }
                            unset($_SESSION['AMM_pass_value']);
                        ?>
                    </label>
                </div>
                <button onclick="" class="w3-button w3-section w3-teal w3-ripple">Accedi</button>
            </form>
        </div>

    </div>
    
</body>
</html> 
