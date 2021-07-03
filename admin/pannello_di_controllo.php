<?php
require '../php/classi.php';
require '../php/function.php';
mb_internal_encoding("UTF-8");
header("cache-control: no-store,no-cache,must-revalidate");
session_start();
    manageStartAMM("../");

    $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
    if ($conn->connect_error) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../index.php");
        exit();
    }

    $sql = "SELECT inizio_votazione as start,fine_votazione as end FROM informazioni";

    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #12805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../index.php");
        exit();
    }
    $periodo=$result->fetch_assoc();
    //print_r( $periodo);
    //exit();
    $PREPARAZIONE=($periodo['end']==null || $periodo['start']==null || (date('Y-m-dTH:i')<$periodo['end'] && date('Y-m-dTH:i')<$periodo['start']));
    $DURANTE=(date('Y-m-dTH:i')>=$periodo['start'] && date('Y-m-dTH:i')<=$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
    $TERMINE=(date('Y-m-dTH:i')>$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);

?>

<html>
<head>
    <title>Amministrazione</title>
    <link rel="shortcut icon" href="../img/logo1.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Andrea Cazzato">
    <link rel="stylesheet" href="../css/w3.css">
    
    <script>
        function lvValidate(lv) {
            return lv == '0' || lv == '1';

        }
        
    </script>
    <script>
        function nomeValidate(string) {
            return /^[A-Za-z0-9]{2,13}$/.test(string);

        }
        function cfValidate(string) {
            return /^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/i.test(string);

        }
    </script>
    <script>
        function endValidate(ID, nome) {
            var inizio=document.frmData.inizio.value;
            var fine = document.frmData.fine.value;

            
            return inizio <= fine;;
        }
        function endSet() {
            if (document.frmData.inizio.value) {
                document.frmData.fine.setAttribute("min", document.frmData.inizio.value);
            }
            
        }
    </script>
    <script>
        async function asyncOpen(URL,nome_componente) {
            console.log(URL);
            try {
                let response = await fetch(URL);
                getComponent(nome_componente);
                console.log(response);
            } catch (e) {
                console.log("Si è verificato un errore con la funzione asyncOpen!");
            }
        }
        function toggleSelect(selected) {
            var list = document.getElementsByName("button_list");
            for (let i = 0; i < list.length; i++) {
                if(list[i].classList.contains("w3-white")){
                    list[i].classList.remove("w3-white");
                }
                if(!list[i].classList.contains("w3-border-bottom")){
                    list[i].classList.add("w3-border-bottom");
                }
            }
            selected.classList.remove("w3-border-bottom");
            selected.classList.add("w3-white");
            selected.blur()
        }
        async function getComponent(nome) {
            try {
                let response = await fetch("./" + nome);
                response.text().then(function (text) {
                    document.getElementById('content').innerHTML = text;
                });
            
                console.log(response);
            } catch (e) {
                console.log("Si è verificato un errore!");
            }
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
            }
    </script>
    </header>

    <div id="BarraUtente" class="w3-bar w3-border w3-light-grey">
        <span id="barra_chiave" class="w3-bar-item w3-border-right w3-left" style="padding-top:3px;padding-bottom:3px">
            <img alt="utente" src="../img/amministratore.png" style="width: 32px;margin-right:15px" /><?php echo $_SESSION['AMM_email'] ?>
        </span>
        <button class="w3-button w3-border-left w3-right" onclick="location.href = './esci.php';">Esci</button>
    </div>

    <div class="w3-modal-content w3-margin-top">
        <button onclick="getComponent('gestione_component.php'); toggleSelect(this)" name="button_list" class=" w3-teal w3-button w3-border-top w3-border-bottom w3-border-left w3-border-right" >Gestione</button>
        <script>
            getComponent('gestione_component.php');
            toggleSelect(document.getElementsByName('button_list')[0]);
        </script>
         <?php
            if($_SESSION['LV']>=1){
        ?>
            <button onclick="getComponent('admin_component.php'); toggleSelect(this)" name="button_list" class=" w3-teal w3-button w3-border-top w3-border-bottom w3-border-left w3-border-right" >Amministratori</button>
        <?php } ?>
            <button onclick="getComponent('liste_component.php'); toggleSelect(this)" name="button_list" class=" w3-teal w3-button w3-border-top w3-border-bottom w3-border-left w3-border-right" >Liste</button>
        <?php
            //if(!$PREPARAZIONE){
        ?>
            <button onclick="getComponent('logerrori_component.php'); toggleSelect(this)" name="button_list" class=" w3-teal w3-button w3-border-top w3-border-bottom w3-border-left w3-border-right" >LOG errori</button>
        <?php //} ?>
        <?php if(!$PREPARAZIONE){ ?>
        <button onclick="getComponent('votanti_component.php'); toggleSelect(this)" name="button_list" class=" w3-teal w3-button w3-border-top w3-border-bottom w3-border-left w3-border-right" >Votanti</button>
        <?php } ?>
        <div id="content" class="w3-container w3-card-4">


        </div>
    </div>
        <br />
    <div id="Info" class="w3-modal" style="display:<?php if(isset($_SESSION['panel_info'])){ echo "block"; }else{ echo "none"; } ?>">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Informazioni</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('Info').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 ><?php echo $_SESSION['panel_info']; unset($_SESSION['panel_info']); ?></h3>
            </div>
            <br />
        </div>
        <br />
    </div>
    <div id="InfoGen" class="w3-modal" style="display:<?php if(isset($_SESSION['info'])){ echo "block"; }else{ echo "none"; } ?>">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Informazioni</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('InfoGen').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 ><?php echo $_SESSION['info']; unset($_SESSION['info']); ?></h3>
            </div>
            <br />
        </div>
        <br />
    </div>
    <div id="infoDelete" class="w3-modal" style="display:<?php if(isset($_SESSION['delete_info'])){ echo "block"; }else{ echo "none"; } ?>">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Informazioni sull'eliminazione</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('infoDelete').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 ><?php echo $_SESSION['delete_info']; unset($_SESSION['delete_info']); ?></h3>
            </div>
            <br />
        </div>
        <br />
    </div>
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
    <div id="infoUpdate_" class="w3-modal" style="display:<?php if(isset($_SESSION['update_info'])){ echo "block"; }else{ echo "none"; } ?>">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Informazioni sull'aggiornamento</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('infoUpdate_').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 ><?php echo $_SESSION['update_info']; unset($_SESSION['update_info']); ?></h3>
            </div>
            <br />
        </div>
        <br />
    </div>
</body>
</html> 
