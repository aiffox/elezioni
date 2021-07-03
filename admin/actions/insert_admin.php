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
    if ($_SESSION['LV']<2) {
        $error=$_SESSION['panel_info']= "Non disponi dei permessi necessari per accedere a questa sezione. (errore #8025)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../pannello_di_controllo.php");
        exit();
    }



//AGGIUNTA DATI-------------------------------------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_GET['livello']) || !isset($_GET['nome'])){
    $error=$_SESSION['insert_info']="Errore nel passaggio dei dati.";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}
if($_GET['livello']!=0 && $_GET['livello']!=1){
    $error=$_SESSION['insert_info']="Livello privilegi non valido.";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}
if(!MAIL_VALIDATE($_GET['nome'])){
    $error=$_SESSION['insert_info']="Email non valida. Ricorda che deve appartenere al dominio '@itismaglie.it'.";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}

$sql = "SELECT * FROM account_amministratori WHERE email = '" . $conn->real_escape_string($_GET['nome']) . "'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
   $error= $_SESSION['insert_info']="Errore. L'indirizzo email e' gia' associato ad un altro amministratore.";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}

$password=randomPasswordAMM();
$sql = "INSERT INTO account_amministratori(email,livello_privilegi,password) VALUES( '" . $conn->real_escape_string($_GET['nome']) . "'," . $conn->real_escape_string($_GET['livello']) . ", '".md5($password)."' )";
$result = $conn->query($sql);

$sql = "SELECT * FROM account_amministratori WHERE email = '" . $conn->real_escape_string($_GET['nome']) . "'";
$result = $conn->query($sql);
if ($result->num_rows <= 0) {
    $error=$_SESSION['insert_info']= "Errore durante l'inserimento dell'amministratore nel database.";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}

$_SESSION['insert_info']="L'amministratore ".$_GET['nome']." e' stato aggiunto con successo con livello di privilegi '";
$_SESSION['insert_info'].= $_GET['livello'] == 0 ? "Viewer" : "Editor";
$_SESSION['insert_info'].="'. ATTENZIONE: la password e' {".$password."}, non sara' piu' possibile recuperarla, quindi non perderla!";
//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>