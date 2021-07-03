<?php
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    require './php/classi.php';
    require './php/function.php';
    session_start();
    $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
    // Check connection
    if ($conn->connect_error) {
        $error = $_SESSION['send_mail_value'] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #0300)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
        header("location: ./index.php");
        exit();
    }
    $conn->set_charset("utf8");
    manageStart($conn);
    manageConsent($conn);
    $ret = manageData($conn);
    //QUERY UPDATE--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $timestamp = date('Y-m-d H:i:s');
    echo $timestamp;
    if($_SESSION['IDlista']==-1){
        $sql = "UPDATE voti,account_votanti SET voti.data_voto='" . $timestamp . "',voti.nome_lista=NULL ,voti.CF_primo_candidato=NULL ,voti.CF_secondo_candidato=NULL WHERE account_votanti.email = '" . $conn->real_escape_string($_SESSION['AUT_email']) . "' AND voti.email=account_votanti.email";
    }
    else if($_SESSION['IDcandidato2']==-1 && $_SESSION['IDcandidato1']==-1){
        $sql = "UPDATE voti,account_votanti SET voti.data_voto='" . $timestamp . "',voti.nome_lista='" . $conn->real_escape_string($_SESSION['IDlista']) . "' ,voti.CF_primo_candidato=NULL ,voti.CF_secondo_candidato=NULL WHERE account_votanti.email = '" . $conn->real_escape_string($_SESSION['AUT_email']) . "' AND voti.email=account_votanti.email";
    }
    else if($_SESSION['IDcandidato2']==-1){
         $sql = "UPDATE voti,account_votanti SET voti.data_voto='" . $timestamp . "',voti.nome_lista='" . $conn->real_escape_string($_SESSION['IDlista']) . "' ,voti.CF_primo_candidato='" . $conn->real_escape_string($_SESSION['IDcandidato1']) . "' ,voti.CF_secondo_candidato=NULL WHERE account_votanti.email = '" . $conn->real_escape_string($_SESSION['AUT_email']) . "' AND voti.email=account_votanti.email";
    }
    else{
        $sql = "UPDATE voti,account_votanti SET voti.data_voto='" . $timestamp . "',voti.nome_lista='" . $conn->real_escape_string($_SESSION['IDlista']) . "',voti.CF_primo_candidato='" . $conn->real_escape_string($_SESSION['IDcandidato1']) . "',voti.CF_secondo_candidato='" . $conn->real_escape_string($_SESSION['IDcandidato2']) . "' WHERE account_votanti.email = '" . $conn->real_escape_string($_SESSION['AUT_email']) . "' AND voti.email=account_votanti.email";
    
    }
    /*echo $sql;
    exit();*/
    $result = $conn->query($sql);
    //INVIO EMAIL---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $mittente = 'From: Elezioni <help@elezioni.it>';
    $titolo = "Grazie per il tuo voto";
    $messaggio = "Il tuo voto e' stato registrato con successo! \nLista votata: " . $ret['nome_lista'] . "\nPrimo candidato: " . $ret['nome_candidato1'] . " " . $ret['cognome_candidato1'] . "\nSecondo candidato:" . $ret['nome_candidato2'] . " " . $ret['cognome_candidato2'];
    $send_result = mail($_SESSION['AUT_email'], $titolo, $messaggio, $mittente);
    //CONTROLLO INVIO EMAIL-------------------------------------------------------------------------------------------------------------------------------------------------------------
    if (!$send_result) {
        $error = $_SESSION['send_mail_value'] = "Non e' stato possibile inviare l'email. Il voto è stato comunque registrato. (errore #1010)";
        //echo "Non e' stato possibile inviare l'email. Riprova";
        logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
        header("location: ./index.php");
        exit();
    }
    //CLOSE SESSION--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    session_unset();
    session_destroy();
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    mysqli_close($conn);
    //REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    header("location: ./fatto.php");
?>