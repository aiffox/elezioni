<?php
    require '../../php/classi.php';
    require '../../php/function.php';
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    session_start();
    manageStartAMM("../../");

    $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
    if ($conn->connect_error) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../index.php");
        exit();
    }
    if ($_SESSION['LV']<1) {
        $error=$_SESSION['panel_info']= "Non disponi dei permessi necessari per accedere a questa sezione. (errore #8025)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        exit();
    }

    if($_GET['termina']=='SI'){
        $sql = "UPDATE informazioni SET inizio_votazione=null";
        $conn->query($sql);
        $sql = "UPDATE informazioni SET fine_votazione=null";
        $conn->query($sql);
        $_SESSION['update_info']="La votazione ha avuto fine.";
        exit();
    }
    
    if(!isset($_GET['inizio']) || !isset($_GET['fine'])){
        $error=$_SESSION['update_info']="Errore nel passaggio dei dati.";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        exit();
    }
    if($_GET['inizio']>=$_GET['fine'] && $_GET['fine']!='null' && $_GET['inizio']!='null'){
        $error=$_SESSION['update_info']="Errore. La data di inizio deve essere antecedente alla data di fine votazione.";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        exit();
    }
    if((!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/",$_GET['inizio']) && $_GET['inizio']!='null') || (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/",$_GET['fine']) && $_GET['fine']!='null')){
        $error=$_SESSION['update_info']="Errore. Verifica che le date siano corrette e riprova.".$_GET['inizio'];
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        exit();
    }

    $sql = "UPDATE informazioni SET inizio_votazione='".$conn->real_escape_string($_GET['inizio'])."'";
    $conn->query($sql);
    $sql = "UPDATE informazioni SET fine_votazione='".$conn->real_escape_string($_GET['fine'])."'";
    $conn->query($sql);

    $_SESSION['update_info']="Le nuove date di inizio e fine votazione sono rispettivamente ".str_replace("T"," alle ",$_GET['inizio'])." e ".str_replace("T"," alle ",$_GET['fine']).".";
//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>