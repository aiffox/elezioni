<?php
require '../php/classi.php';require '../php/function.php';
mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");session_start();
//CONNESSIONE---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
    if ($conn->connect_error) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #802)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string($email));
        header("location: ./login_pass.php");
        exit();
    }
//CONTROLLO DATI INSERITI---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    if(!isset($_SESSION['AMM_email'])){
        $error=$SESSION['send_mail_value']= "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail. (errore #800)";
        //echo "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail";
        unset($_SESSION['AMM_email']);
        header("location: ./login_pass.php");
        exit();
    }
    $email = $_SESSION['AMM_email'];
    if(!MAIL_VALIDATE($_SESSION['AMM_email'])){
        unset($_SESSION['AMM_email']);
        unset($_SESSION['AMM_password']);
        header("location: ../sessione_scaduta.php");
        exit();
    }

    if(!isset($_POST['password'])){
        $error=$_SESSION['AMM_pass_value']= "Un errore del browser ha impedito la ricezione della password, prova a digitare nuovamente la password. (errore #801)";
        //echo "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail";
        logError($error,$conn->real_escape_string($email));
        header("location: ./login_pass.php");
        exit();
    }
    if(!chkPassAMM($_POST['password'])){
        $error=$_SESSION['AMM_pass_value']= "Controlla di avere inserito la password in formato valido. (errore #9007-A)";//par fermare eventuali comandi mysql posti al posto dell'email
        //echo "Controlla di avere inserito una mail valida".$email;
        logError($error,$conn->real_escape_string($email));
        header("location: ./login_pass.php");
        exit();
    }
    $password = $_POST['password'];





//QUERY---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    $sql = "SELECT livello_privilegi,password FROM account_amministratori WHERE email = '" . $conn->real_escape_string($email) . "'";
    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $error=$_SESSION['send_mail_value']= "L' indirizzo email non e' autorizzato ad accedere al pannello di controllo. Utilizza un indirizzo email autorizzato. (errore #803)";
        //echo "L' indirizzo email non e' autorizzato ad accedere alle elezioni. Utilizza un indirizzo email autorizzato.".$email;
        logError($error,$conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    $dati=$result->fetch_assoc();

//CONTROLLO E CONFRONTO DATI DATABASE-------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    if(strcmp(md5($password),$dati['password'])!=0){
        $error=$_SESSION['AMM_pass_value']= "Password non valida. (errore #804)";
        //echo "nop";
        logError($error,$conn->real_escape_string($email));
        header("location: ./login_pass.php");
        exit();
    }

//SET COOKIE------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    $_SESSION['LV']=$dati['livello_privilegi'];
    $_SESSION['AMM_password']=  $password;





    /*-------------------- DA TOGLIERE ------------------ SOLO PER I TEST----------------------*/
        if(strcmp($email, 'ammi.prova1@itismaglie.it')==0 || 
           strcmp($email, 'ammi.prova2@itismaglie.it')==0 
           ){
            logError("Un admin-tester ha effettuato l'accesso... [".$_SERVER['REMOTE_ADDR']."]", $conn->real_escape_string($email));
        }   
    /*-----------------------------------------------------------------------*/





//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    mysqli_close($conn);

//REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    header("location: ./pannello_di_controllo.php");

?>