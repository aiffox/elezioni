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
        //header("location: ../index.php");
        exit();
    }
    if ($_SESSION['LV']<1) {
        $error=$_SESSION['panel_info']= "Non disponi dei permessi necessari per accedere a questa sezione. (errore #8025)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        //header("location: ../pannello_di_controllo.php");
        exit();
    }

    $sql = "SELECT inizio_votazione as start,fine_votazione as end FROM informazioni";
    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #12805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        //header("location: ./index.php");
        exit();
    }
    $periodo=$result->fetch_assoc();
    $PREPARAZIONE=($periodo['end']==null || $periodo['start']==null || (date('Y-m-dTH:i')<$periodo['end'] && date('Y-m-dTH:i')<$periodo['start']));
    $DURANTE=(date('Y-m-dTH:i')>=$periodo['start'] && date('Y-m-dTH:i')<=$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
    $TERMINE=(date('Y-m-dTH:i')>$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);

    if($TERMINE){
        if(!isset($_GET['conferma']) || $_GET['conferma']!='SI'){
            $error=$_SESSION['update_info']="I dati ricevuti non sono corretti. Per preservare i dati, la votazione non verrà eliminata.";
            //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }
        $sql =  " UPDATE informazioni SET inizio_votazione=null, fine_votazione=null; ";
        $conn->query($sql);
        $sql =  " SET FOREIGN_KEY_CHECKS = 0; ";
        $sql .=  " DELETE FROM voti WHERE 1=1; ";
        $sql .=  " DELETE FROM periodi_utilizzo WHERE 1=1; ";
        $sql .=  " DELETE FROM account_votanti WHERE 1=1;";
        $sql .=  " SET FOREIGN_KEY_CHECKS = 1;";
        
        $conn->query($sql);

        $_SESSION['delete_info']="I dati riguardanti la precedente votazione sono stati eliminati con successo.";
    }
    else{
        $_SESSION['delete_info']="Impossibile eliminare dati. Verifica che la votazione sia terminata e che i dati non siano già stati eliminati.";
    }








//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>