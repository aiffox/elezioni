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
    $_GET['CF']=$conn->real_escape_string($_GET['CF']);
    $_GET['nome']=$conn->real_escape_string($_GET['nome']);
    $_GET['cognome']=$conn->real_escape_string($_GET['cognome']);
    $_GET['IDlista']=$conn->real_escape_string($_GET['IDlista']);

    $periodo=$result->fetch_assoc();
    $PREPARAZIONE=($periodo['end']==null || $periodo['start']==null || (date('Y-m-dTH:i')<$periodo['end'] && date('Y-m-dTH:i')<$periodo['start']));
    $DURANTE=(date('Y-m-dTH:i')>=$periodo['start'] && date('Y-m-dTH:i')<=$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
    $TERMINE=(date('Y-m-dTH:i')>$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);

    if($PREPARAZIONE){
        if(!isset($_GET['IDlista']) || !isset($_GET['nome']) || !isset($_GET['cognome']) || !isset($_GET['CF'])){
            $error=$_SESSION['insert_info']= "Errore nel passaggio dei dati. Riprova.";
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }
        if(!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{2,31}$/', $_GET['nome']) || !preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{2,31}$/', $_GET['cognome']) || !preg_match('/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/i', $_GET['CF']) ){
            $error=$_SESSION['insert_info']= "I dati inseriti non sono nel formato corretto. Verifica di aver inserito solo lettere o numeri e il codice fiscale corretto";
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }

    
        $sql = "SELECT * FROM candidati WHERE codice_fiscale='".$_GET['CF']."';";
        echo $sql."<br>";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $error=$_SESSION['insert_info']= "Il candidato e' gia' presente in questa o in un'altra lista";
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            //header("location: ../pannello_di_controllo.php");
            exit();
        }

        $sql = "INSERT INTO candidati (nome,cognome,codice_fiscale,nome_lista) VALUES('".$_GET['nome']."','".$_GET['cognome']."','".$_GET['CF']."','".$_GET['IDlista']."');";
        echo $sql."<br>";
        $result = $conn->query($sql);

    
        $_SESSION['insert_info']= "Il candidato ". $_GET['nome']." ".$_GET['cognome'] ." e' stato aggiunto.";
    }
    else{
        $_SESSION['insert_info']="Errore. Si può aggiungere un candidato solo in fase di preparazione. Per aggiungere un candidato, termina la votazione attuale e/o elimina i dati della vecchia votazione tramite il tasto \"elimina votazione\"";
    }
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    mysqli_close($conn);

    //REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    //header("location: ../pannello_di_controllo.php");
    exit();

?>