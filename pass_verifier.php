<?php
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    session_start();
    require './php/classi.php';
    require './php/function.php';
    //CONNESSIONE---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

      
    $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
    // Check connection
    if ($conn->connect_error) {
        $error = $_SESSION['send_mail_value'] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #0200)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    $conn->set_charset("utf8");
    $email = $conn->real_escape_string($_SESSION['EMAIL']); 
    //CONTROLLO DATI INSERITI---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    if (!isset($_SESSION['EMAIL'])) {
        $error=$_SESSION['send_mail_value'] = "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail. (errore #0201)";
        //echo "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail";
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    if (!chkEmail($email)) {
        $error = $_SESSION['send_mail_value'] = "Controlla di avere inserito una mail valida. (errore #706)"; //par fermare eventuali comandi mysql posti al posto dell'email
        //echo "Controlla di avere inserito una mail valida".$email;
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    if (!isset($_POST['password'])) {
        $error = $_SESSION['send_pass_value'] = "Un errore del browser ha impedito la ricezione della password, prova a digitare nuovamente la password. (errore #0202)";
        //echo "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail";
        logError($error, $conn->real_escape_string($email));
        header("location: ./login_pass.php");
        exit();
    }
    $password = $conn->real_escape_string($_POST['password']);
    if (!chkPass($password)) {
        $error = $_SESSION['send_pass_value'] = "Controlla di avere inserito la password in formato valido. (errore #0203)"; //par fermare eventuali comandi mysql posti al posto dell'email
        
        //echo "Controlla di avere inserito una mail valida".$email;
        logError($error, $conn->real_escape_string($email));
        header("location: ./login_pass.php");
        exit();
    }
    //QUERY SELECT--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $sql = "SELECT voti.data_voto, account_votanti.tentativi FROM voti,account_votanti WHERE account_votanti.email = '" . $conn->real_escape_string($email) . "' AND voti.email=account_votanti.email AND password='" . md5($password) . "'";
    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $sql= "UPDATE account_votanti SET tentativi=tentativi-1 WHERE email='".$email."'";
        $result = $conn->query($sql);
        $sql= "SELECT tentativi FROM account_votanti WHERE email='".$email."'";
        $result = $conn->query($sql);
        $dati = $result->fetch_assoc();
        if($dati['tentativi']<=0){
            if($dati['tentativi']==0){
                $error = $_SESSION['send_mail_value'] = "Hai esaurito i tentativi a disposizione. Contatta un amministratore. (errore #a02064)";
            }
            else{
                $error = $_SESSION['send_mail_value'] = "Un amministratore ha bloccato la votazione per questo utente. (errore #02064)";
            }
            
        }
        else{
            $error = $_SESSION['send_pass_value'] = "Credenziali errate. Hai altri ".$dati['tentativi']." tentativi (errore #0204)";
            logError($error, $conn->real_escape_string($email));
            header("location: ./login_pass.php");
            exit();
        }
        
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    $dati = $result->fetch_assoc();
    if($dati['tentativi']<=0){
        $error = $_SESSION['send_mail_value'] = "Hai esaurito i tentativi a disposizione. Contatta un amministratore. (errore #b02064)";
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    //CONTROLLO E CONFRONTO DATI DATABASE-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    if ($dati['data_voto'] != null && $dati['data_voto'] != "" && $dati['data_voto'] != "null" && $dati['data_voto'] != "NULL") {
        $error = $_SESSION['send_mail_value'] = "Il proprietario di questo indirizzo email ha gia' espresso il suo voto. E' possibile visualizzare il riepilogo del voto nella propria casella di posta elettonica. (errore #0205)";
        logError($error, $conn->real_escape_string($email));
        //echo "Il proprietario di questo indirizzo email ha gia' espresso il suo voto. E' possibile visualizzare il riepilogo del voto nella propria casella di posta elettonica.".$dati['data_voto'];
        header("location: ./index.php");
        exit();
    }
    
    $sql = "SELECT inizio_votazione,fine_votazione FROM informazioni;";
    $resultINFO= $conn->query($sql);
    $INFO=$resultINFO->fetch_assoc();
    $now = date('Y-m-d H:i:s');
    $start = $INFO['inizio_votazione'];
    $end = $INFO['fine_votazione'];

    if ($start == null || $start == null || $start == 'null' || $start == 'null') {
        $error=$_SESSION['send_mail_value'] = "Le votazione non sono ancora iniziate per questo utente. Non e' stata ancora definita la data di inizio nei nostri server. (errore #0304)";
        header("location: ./index.php");
        logError($error,$email);
        exit();
    }
    if (strcmp($now, $start) < 0) {
        $error=$_SESSION['send_mail_value'] = "Le votazione non sono ancora iniziate per questo utente. Torna qui il " . $start . ". (errore #0305)";
        header("location: ./index.php");
        logError($error,$email);
        exit();
    }
    if (strcmp($now, $end) > 0) {
        $error=$_SESSION['send_mail_value'] = "Le votazione per questo utente sono terminate il " . $end . ". (errore #0306)";
        header("location: ./index.php");
        logError($error,$email);
        exit();
    }
    $sql = "UPDATE account_votanti SET account_votanti.tentativi=5 WHERE email=".$email;
    $conn->query($sql);


    /*-------------------- DA TOGLIERE ------------------ SOLO PER I TEST----------------------*/
    if(strcmp($email, 'account.prova1@itismaglie.it')==0 || 
       strcmp($email, 'account.prova2@itismaglie.it')==0 || 
       strcmp($email, 'account.prova3@itismaglie.it')==0 || 
       strcmp($email, 'account.prova4@itismaglie.it')==0 || 
       strcmp($email, 'account.prova5@itismaglie.it')==0

       ){
        logError("Un vote-tester ha effettuato l'accesso... [".$_SERVER['REMOTE_ADDR']."]", $conn->real_escape_string($email));
    }   
     /*-----------------------------------------------------------------------*/


    //START SESSION---------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $_SESSION['AUT_email'] = $email;
    $_SESSION['AUT_password'] = $password;
    $error = $_SESSION['send_mail_value'] = "";
    $error = $_SESSION['send_pass_value'] = "";
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    mysqli_close($conn);
    //REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    header("location: ./termini_e_condizioni.php");
    exit();
?>