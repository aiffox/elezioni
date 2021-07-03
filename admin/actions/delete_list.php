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
        $error=$_SESSION['info']= "Non disponi dei permessi necessari per accedere a questa sezione. (errore #8025)";
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

    if($PREPARAZIONE){

        if(!isset($_GET['IDlista'])){
            $error=$_SESSION['delete_info']= "Errore nell'acquisizione dei dati. Riprova.";
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }
        $IDlista=$conn->real_escape_string($_GET['IDlista']);

        $sql = "SELECT * FROM liste WHERE nome='".$IDlista."'";
        //echo $sql;
        $result = $conn->query($sql);
        if ($result->num_rows <= 0) {
            $error=$_SESSION['delete_info']= "Non e' presente alcuna lista chiamata '".$IDlista."'. Riprova.";
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }
    
        $sql = "DELETE FROM liste WHERE nome='".$IDlista."'";
        //echo $sql;
        $conn->query($sql);


        $_SESSION['delete_info']= "Eliminazione riuscita!";
    }
    else{
        $_SESSION['delete_info']="Errore. Si può eliminare una lista solo in fase di preparazione. Per eliminare una lista, termina la votazione attuale e/o elimina i dati della vecchia votazione tramite il tasto \"elimina votazione\"";
    }
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    mysqli_close($conn);

    //REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    //header("location: ../pannello_di_controllo.php");
    exit();
?>