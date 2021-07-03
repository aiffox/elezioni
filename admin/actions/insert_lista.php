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
        $error=$_SESSION['info']= "Non disponi dei permessi necessari per accedere a questa sezione. (errore #8025)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../pannello_di_controllo.php");
        exit();
    }
    $sql = "SELECT inizio_votazione as start,fine_votazione as end FROM informazioni";
    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #12805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ./index.php");
        exit();
    }
    $periodo=$result->fetch_assoc();
    $PREPARAZIONE=($periodo['end']==null || $periodo['start']==null || (date('Y-m-dTH:i')<$periodo['end'] && date('Y-m-dTH:i')<$periodo['start']));
    $DURANTE=(date('Y-m-dTH:i')>=$periodo['start'] && date('Y-m-dTH:i')<=$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
    $TERMINE=(date('Y-m-dTH:i')>$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
    



//AGGIUNTA DATI-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
if($PREPARAZIONE){
    $sql = "SELECT * FROM liste WHERE nome = '" . $conn->real_escape_string($_POST['nome_lista']) . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $error=$_SESSION['insert_info']="Errore. Il nome della lista e' gia' usato da una lista precedentemente aggiunta.";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));    
        header("location: ../add_lista.php");
        exit();
    }

    $sql = "INSERT INTO liste(nome) VALUES( '" . $conn->real_escape_string($_POST['nome_lista']) . "')";
    $result = $conn->query($sql);

    $sql = "SELECT * FROM liste WHERE nome = '" . $conn->real_escape_string($_POST['nome_lista']) . "'";
    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $error=$_SESSION['insert_info']= "Errore durante l'inserimento del nome della lista nel database.";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../add_lista.php");
        exit();
    }
    $dati=$result->fetch_assoc();
    $IDlista=$dati['nome'];


    for($i=0;$i<count($_POST['nome']);$i++,$max++){
        if(!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{2,31}$/',$_POST['nome'][$i]) || !preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{2,31}$/', $_POST['cognome'][$i]) || !preg_match('/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/i', $_POST['CF'][$i]) ){
            $error=$_SESSION['insert_info']= "I dati inseriti non sono nel formato corretto (" . $i+1 . "). Verifica di aver inserito solo lettere o numeri e il codice fiscale corretto";
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            header("location: ../pannello_di_controllo.php");
            exit();
        }

        $nome=$conn->real_escape_string($_POST['nome'][$i]);
        $cognome=$conn->real_escape_string($_POST['cognome'][$i]);
        $CF=$conn->real_escape_string($_POST['CF'][$i]);

        $sql = "INSERT INTO candidati (nome,cognome,codice_fiscale,nome_lista) VALUES('".$nome."','".$cognome."','".$CF."','".$IDlista."');";
        echo $sql."<br>";
        $result = $conn->query($sql);
        }

        $_SESSION['insert_info']="La lista ".$_POST['nome_lista']." e' stata aggiunta con successo.";
    }
    else{
        $_SESSION['insert_info']="Errore. Si può aggiungere una lista solo in fase di preparazione. Per aggiungere una lista, termina la votazione attuale e/o elimina i dati della vecchia votazione tramite il tasto \"elimina votazione\"";
    }
//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);

//REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

header("location: ../add_lista.php");
exit();


?>