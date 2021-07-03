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
        exit();
    }



//AGGIUNTA DATI-------------------------------------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_GET['ID'])){
    $error=$_SESSION['delete_info']="Errore nel passaggio dei dati.";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}

$sql = "SELECT livello_privilegi, email FROM account_amministratori WHERE email = '" . $conn->real_escape_string($_GET['ID']) . "'";
$result = $conn->query($sql);
if ($result->num_rows <= 0) {
   $error= $_SESSION['delete_info']="Errore. Non esistono amministratori con email uguale a ".$_GET['ID'].".";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}
$data=$result->fetch_assoc();
$nome=$data['email'];
if($data['livello_privilegi']==2){
    $error= $_SESSION['delete_info']="Impossibile eliminare amministratore con privilegio 'ADMIN'.";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}

$sql = "DELETE FROM account_amministratori WHERE email='".$nome. "'";
$result = $conn->query($sql);

$sql = "SELECT * FROM account_amministratori WHERE email ='" . $nome. "'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $error=$_SESSION['delete_info']= "Errore durante l'eliminazione dell'amministratore nel database.";
    //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
    logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
    exit();
}

$_SESSION['delete_info']="L'amministratore ".$nome." e' stato rimosso con successo";
//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>