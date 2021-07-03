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

    if(!$PREPARAZIONE){
        if(!isset($_GET['IDaccount'])){
            $error=$_SESSION['update_info']="Errore nel passaggio dei dati.";
            //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }
        $sql = "SELECT * FROM account_votanti WHERE email='".$conn->real_escape_string($_GET['IDaccount'])."'";
        $result=$conn->query($sql);
        if ($result->num_rows <= 0) {
            $error = $_SESSION['update_info'] = "non sono presenti account con email uguale a ".$_GET['IDaccount'].". (errore #020d4)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }

        $sql = "UPDATE voti SET data_voto=null, CF_primo_candidato=null, CF_secondo_candidato=null, nome_lista=null WHERE email='".$conn->real_escape_string($_GET['IDaccount'])."'";
        $conn->query($sql);

        $_SESSION['update_info']="Voto annullato con successo.";
        //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        mysqli_close($conn);
    }
    else{
        $_SESSION['update_info']="Errore. Si può annullare un voto solo durante una votazione o al termine di essa, non durante la preparazione.";
    }
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    mysqli_close($conn);

    //REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    //header("location: ../pannello_di_controllo.php");
    exit();

?>