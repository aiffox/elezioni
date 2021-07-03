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
    //SET SEIION------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $_SESSION['consenso'] = '1';
    $_SESSION['letto_regole'] = '1';
    /*
        //QUERY UPDATE----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $sql = "UPDATE dati SET consenso='1' WHERE email = '" . $_COOKIE['AUT_email'] . "'";
        if (!$conn->query($sql)) {
        setcookie("err_consenso", "Errore di comunicazione con il server. Prova ad aggiornare la pagina oppure contatta l'amministratore. (errore #9020)");
        header("location: ./termini_e_condizioni.php");
        exit();
        }

        $sql = "UPDATE dati SET letto_regole='1' WHERE email = '" . $_COOKIE['AUT_email'] . "'";
        if (!$conn->query($sql)) {
        setcookie("err_consenso", "Errore di comunicazione con il server. Prova ad aggiornare la pagina oppure contatta l'amministratore. (errore #9021)");
        header("location: ./termini_e_condizioni.php");
        exit();
        }
    */
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    mysqli_close($conn);
    //REDIRECT------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    header("location: ./vota_lista.php");
?>