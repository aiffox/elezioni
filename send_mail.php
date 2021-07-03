<?php
    require './php/classi.php';
    require './php/function.php';
    include './php/Period_manage.php';
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    session_start();
    IPcheck("./");
    //CONNESSIONE---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $_SESSION['EMAIL']=strtolower($_POST['email']);   
    $email = strtolower($_POST['email']);    
    $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
    // Check connection
    if ($conn->connect_error) {
        $error = $_SESSION['send_mail_value'] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #0100)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    $conn->set_charset("utf8");
    //CONTROLLO DATI INSERITI---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    if (!isset($_POST['email'])) {
        $error = $_SESSION['send_mail_value'] = "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail. (errore #0101)";
        //echo "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail";
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    if (!chkEmail($email)) {
        $error = $_SESSION['send_mail_value'] = "Controlla di avere inserito una mail valida. (errore #0102)"; //par fermare eventuali comandi mysql posti al posto dell'email
        //echo "Controlla di avere inserito una mail valida".$email;
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    
    if (!endsWith($email, "@itismaglie.it")) {
        $error = $_SESSION['send_mail_value'] = "L'indirizzo email non e' autorizzato a votare. Verifica di aver inserito un'indirizzo email appartenente al dominio della scuola ('COGNOME.NOME@itismaglie.it'). (errore #0103)";
        //echo "Controlla di avere inserito una mail valida".$email;
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    //QUERY---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //il suo account esiste nel database? se no, crea un account per quella mail
    




    /*-------------------- DA TOGLIERE ------------------ SOLO PER I TEST----------------------*/
    if(strcmp($email, 'account.prova1@itismaglie.it')==0 || 
       strcmp($email, 'account.prova2@itismaglie.it')==0 || 
       strcmp($email, 'account.prova3@itismaglie.it')==0 || 
       strcmp($email, 'account.prova4@itismaglie.it')==0 || 
       strcmp($email, 'account.prova5@itismaglie.it')==0

       ){
        logError("Un vote-tester tenta di accedere... [".$_SERVER['REMOTE_ADDR']."]", $conn->real_escape_string($email));
        $_SESSION['EMAIL'] = $email;
        header("location: ./login_pass.php");
        exit();
    }   
     /*-----------------------------------------------------------------------*/





$sql = "SELECT * FROM account_votanti WHERE account_votanti.email = '" . $conn->real_escape_string($email) . "'";
    $result = $conn->query($sql);

    $password=randomPassword();

    //RIMUOVI L'INSERIMENTO DELL'IP

    if ($result->num_rows <= 0) {
        $sqlCREATE = "INSERT INTO account_votanti (email,password,tentativi)  " . "VALUES ('" . $conn->real_escape_string($email) . "','" . md5($password) . "',5)";
        $conn->query($sqlCREATE);

        $sqlGETID = "SELECT * FROM account_votanti WHERE email='" . $conn->real_escape_string($email) . "'";
        $result = $conn->query($sqlGETID);
        $dati = $result->fetch_assoc();

        $sqlCREATE = "INSERT INTO voti (email) VALUES ('" . $conn->real_escape_string($dati['email']) . "')";

        $result = $conn->query($sqlCREATE);
    }
    else{
        $sql = "UPDATE account_votanti SET password='". md5($password) ."' WHERE email = '" . $conn->real_escape_string($email) . "'";   
        $result = $conn->query($sql);
    }

    $sql = "SELECT voti.data_voto,account_votanti.tentativi FROM voti,account_votanti WHERE account_votanti.email = '" . $conn->real_escape_string($email) . "' AND account_votanti.email=voti.email";
  
$result = $conn->query($sql);

    

    $dati = $result->fetch_assoc();
    $data_voto = $dati['data_voto'];

        
    $sql = "SELECT inizio_votazione,fine_votazione FROM informazioni;";
    $resultINFO= $conn->query($sql);
    $INFO=$resultINFO->fetch_assoc();
    $now = date('Y-m-d H:i:s');
    $start = $INFO['inizio_votazione'];
    $end = $INFO['fine_votazione'];

    //CONTROLLO E CONFRONTO DATI DATABASE-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    if($dati['tentativi']<=0){
        if($dati['tentativi']==0){
            $error = $_SESSION['send_mail_value'] = "Hai esaurito i tentativi a disposizione. Contatta un amministratore. (errore #g02064)";
        }
        else{
            $error = $_SESSION['send_mail_value'] = "Un amministratore ha bloccato la votazione per questo utente. (errore #f02064)";
        }
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }    
    if ($data_voto != null && $data_voto != "" && $data_voto != "null" && $data_voto != "NULL") {
        $error = $_SESSION['send_mail_value'] = "Il proprietario di questo indirizzo email ha gia' espresso il suo voto. E' possibile visualizzare il riepilogo del voto nella propria casella di posta elettonica. (errore #0104)";
        logError($error, $conn->real_escape_string($email));
        //echo "Il proprietario di questo indirizzo email ha gia' espresso il suo voto. E' possibile visualizzare il riepilogo del voto nella propria casella di posta elettonica.".$dati['data_voto'];
        header("location: ./index.php");
        exit();
    }/*
    echo $start;
    echo $now;
    echo $end;
    exit();*/
    if ($start == null || $start == null || $start == 'null' || $start == 'null') {
        $error = $_SESSION['send_mail_value'] = "Le votazione non sono ancora iniziate per questo utente. Non e' stata ancora definita la data di inizio nei nostri server. (errore #0105)";
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    if (strcmp($now, $start) < 0) {
        $error = $_SESSION['send_mail_value'] = "Le votazione non sono ancora iniziate per questo utente. Torna qui il " . $start . ". (errore #0106)";
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    if (strcmp($now, $end) > 0) {
        $error = $_SESSION['send_mail_value'] = "Le votazione per questo utente sono terminate il " . $end . ". (errore #0107)";
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    //INVIO EMAIL---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $mittente = 'From: Elezioni <help@elezioni.it>';
    $titolo = "Password per l'accesso alle elezioni";
    $messaggio = "La tua password temporanea per poter accedere alle elezioni è '$password'. Non condividere la password con nessuno.\n\nSe non avevi richiesto una password, ignora questa mail.";
    $send_result = mail($email, $titolo, $messaggio, $mittente);
    //CONTROLLO INVIO EMAIL-------------------------------------------------------------------------------------------------------------------------------------------------------------
    if (!$send_result) {
        $error = $_SESSION['send_mail_value'] = "Non e' stato possibile inviare l'email. Riprova. (errore #0108)";
        logError($error, $conn->real_escape_string($email));
        header("location: ./index.php");
        exit();
    }
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    mysqli_close($conn);
    //SET Session------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $_SESSION['EMAIL'] = $email;
    //REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $_SESSION['SENT'] = "SENT";
    header("location: ./login_pass.php");
?>