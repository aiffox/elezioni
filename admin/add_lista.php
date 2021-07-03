<?php
require '../php/classi.php';
    require '../php/function.php';
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    session_start();

    
    manageStartAMM("../");
    $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
    if ($conn->connect_error) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ./index.php");
        exit();
    }

    if ($_SESSION['LV']<1) {
        $error=$_SESSION['info']= "Non disponi dei permessi necessari per accedere a questa sezione. (errore #8025)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ./pannello_di_controllo.php");
        exit();
    }

?>

<html>
<head>
    <title>Amministrazione</title>
    <link rel="shortcut icon" href="../img/logo1.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Andrea Cazzato">
    <link rel="stylesheet" href="../css/w3.css">
    <script>
        var nn = 1;
        function add_candidati(n) {
            n = parseInt(n)
            if (n<1) {
                return;
            }
            if (n>30) {
                return;
            }
            if (n < nn) {
                for (i = n; i < nn; i++) {
                    document.getElementsByName("candidato")[n].remove();//funziona, non toccare
                }
            }
            else {
                for (let i = nn; i < n; i++) {//tutto questo casino per evitare che vengano cancellati i nomi di tutti i candidati quando si vuole modificare il numero di essi
                    var div=document. createElement("div");
                    div.setAttribute('name', 'candidato');
                    div.innerHTML+="<div class=\"w3-row w3-border\" style=\"padding-bottom:5px;\">"+
                            "    <div class=\"w3-half w3-container\">"+
                            "        <label >Nome n." + (i + 1) + ":</label>"+
                            "        <input name=\"nome[]\" type=\"text\" class=\"w3-input w3-border w3-round\" placeholder=\"Nome...\" minlength=\"4\" maxlength=\"20\"  required>"+
                            "    </div>"+
                            "    <div class=\"w3-half w3-container\">"+
                            "        <label >Cognome n." + (i + 1) + ":</label>"+
                            "        <input name=\"cognome[]\" type=\"text\" class=\"w3-input w3-border w3-round\" placeholder=\"Cognome...\" minlength=\"4\" maxlength=\"20\"  required>"+
                            "    </div>" +
                            "    <div class=\"w3-half w3-container\">"+
                            "        <label >Codice Fiscale n." + (i + 1) + ":</label>"+
                            "        <input name=\"CF[]\" class=\"w3-input w3-border w3-round\" type=\"text\"  required minlength=\"16\" maxlength=\"16\" placeholder=\"Codice Fiscale...\"/>"+
                            "    </div>"+
                            "</div>"+
                            "<br />";
                    /*div.innerHTML += "<label >Nome Candidato " + (i + 1) + ":</label>" +
                        "<input name=\"nome_cand[]\" type=\"text\" class=\"w3-input w3-border w3-round\" placeholder=\"Inserisci nome candidato\" style=\"width:90%\" minlength=\"4\" maxlength=\"20\"  required><br />";
                        */
                    document.getElementById("scriviQUI").appendChild(div);/*
                    document.getElementById('scriviQUI').innerHTML += "<div name=\"candidato\"><label >Nome Candidato " + (i + 1) + ":</label>" +
                        "<input name=\"nome\" type=\"text\" class=\"w3-input w3-border w3-round\" placeholder=\"Inserisci nome candidato\" style=\"width:90%\" minlength=\"4\" maxlength=\"20\"  required><br /></div>";*/
                }
            }
            nn = n;
        }
    </script>
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
        <button class="w3-button w3-border-left w3-right" onclick="history.go(-1)">Indietro</button>
    </div>

    <div class="w3-modal-content w3-margin-top">
        <div class="w3-container w3-card-4">
            <header class="w3-container w3-border-bottom">
                <h3>Aggiungi lista</h3>
            </header>
            
            <form action="./actions/insert_lista.php" method="POST" name="frm">
                <p>
                    <label >Nome lista:</label>
                    <input name="nome_lista" type="text" class="w3-input w3-border w3-round" placeholder="Inserisci nome lista" style="width:90%" minlength="4" maxlength="20"  required>
               </p>
                <hr />
                <p>
                    <label >Numero Candidati:</label>
                    <input type="number" onchange="add_candidati(this.value)" min="1" max="30" value="1"/>
                    <br />
                    <div id="scriviQUI" style="padding-left:10px;">
                        <div name="candidato">
                            <div class="w3-row w3-border" style="padding-bottom:5px;">
                                <div class="w3-half w3-container">
                                    <label >Nome n.1:</label>
                                    <input name="nome[]" type="text" class="w3-input w3-border w3-round" placeholder="Nome..." minlength="4" maxlength="20"  required>
                                </div>
                                <div class="w3-half w3-container">
                                    <label >Cognome n.1:</label>
                                    <input name="cognome[]" type="text" class="w3-input w3-border w3-round" placeholder="Cognome..." minlength="4" maxlength="20"  required>
                                </div>
                                <div class="w3-half w3-container">
                                    <label >Codice Fiscale n.1:</label>
                                    <input name="CF[]" class="w3-input w3-border w3-round" type="text"  required minlength="16" maxlength="16" placeholder="Codice Fiscale..."/>
                                </div>
                            </div>
                            <br />
                        </div>
                    </div>
                    <div class="w3-container" style="margin:0;padding:0">
                    <label class="w3-small w3-text-orange" id="err_pass" style="margin:0;width:100%">
                        <?php
                            echo $_SESSION['AMM_add_lista_value'];
                            $_SESSION['AMM_add_lista_value']='';
                        ?>
                    </label>
                        </div>
                    <button class="w3-button w3-light-grey w3-border w3-ripple" onclick=""> Aggiungi</button>
                </p>
            </form>
        </div>
    </div>
                            <br />
                            <br />
    <div id="infoInsert" class="w3-modal" style="display:<?php if(isset($_SESSION['insert_info'])){ echo "block"; }else{ echo "none"; } ?>">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Informazioni sull'aggiunta</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('infoInsert').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 ><?php echo $_SESSION['insert_info']; unset($_SESSION['insert_info']); ?></h3>
            </div>
            <br />
        </div>
        <br />
    </div>
</body>
</html>