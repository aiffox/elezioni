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
       // header("location: ./index.php");
        exit();
    }
    $periodo=$result->fetch_assoc();
    $PREPARAZIONE=($periodo['end']==null || $periodo['start']==null || (date('Y-m-dTH:i')<$periodo['end'] && date('Y-m-dTH:i')<$periodo['start']));
    $DURANTE=(date('Y-m-dTH:i')>=$periodo['start'] && date('Y-m-dTH:i')<=$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
    $TERMINE=(date('Y-m-dTH:i')>$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);

    if($DURANTE){

        if(!isset($_GET['IDvotante'])){
            $error=$_SESSION['update_info']="Errore nel passaggio dei dati.";
            //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }
        $sql = "SELECT * FROM account_votanti WHERE email='".$conn->real_escape_string($_GET['IDvotante'])."'";
        $result=$conn->query($sql);
        if ($result->num_rows <= 0) {
            $error = $_SESSION['update_info'] = "Non sono presenti account con email uguale a ".$_GET['IDvotante'].". (errore #020d4)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }
        $data=$result->fetch_assoc();
        if($data['tentativi']<=0){
            $sql = "UPDATE account_votanti SET tentativi=5 WHERE email='".$conn->real_escape_string($_GET['IDvotante'])."'";
            $conn->query($sql);

            $_SESSION['update_info']="Utente sbloccato con successo.";
        }
        else{
            $sql = "UPDATE account_votanti SET tentativi=-1 WHERE email='".$conn->real_escape_string($_GET['IDvotante'])."'";
            $conn->query($sql);

            $_SESSION['update_info']="Utente bloccato con successo.";
        }
    }
    else{
        $_SESSION['update_info']="Errore. Si può bloccare un votante solo in fase di votazione.";
    }
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    mysqli_close($conn);

    //REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    //header("location: ../pannello_di_controllo.php");
    exit();

?>