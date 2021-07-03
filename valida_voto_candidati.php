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
    if (isset($_SESSION['IDlista'])) {
        if ($_SESSION['IDlista'] == - 1) {
            mysqli_close($conn);
            header("location: ./riepilogo.php");
            exit();
        }
    } else {
        $error=$_SESSION['vota_lista_value'] = "Errore nell'acquisizione del voto! Riprova o contatta l'amministratore. (errore #0700)";
        logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
        header("location: ./vota_lista.php");
        exit();
    }
    $_SESSION['IDcandidato1'] = - 1;
    $_SESSION['IDcandidato2'] = - 1;
    if (isset($_POST['cand'])) {
        $n_cand = count($_POST['cand']);
        if ($n_cand < 1 || $n_cand > 2) {
            $error=$_SESSION['vota_candidati_value'] = "Hai votato $n_cand candidati. Puoi votare da 1 a 2 candidati, oppure puoi non esprimere un voto. (errore #0808)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./vota_candidati.php");
            exit();
        }
    } else {
        mysqli_close($conn);
        header("location: ./riepilogo.php");
        exit();
    }
    for ($i = 0;$i < count($_POST['cand']);$i++) {
        $_POST['cand'][$i] = $conn->real_escape_string($_POST['cand'][$i]);
        echo $_POST['cand'][$i];
    }
    //exit();
    //QUERY SELECT + CONTROLLO CANDIDATI--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $sql = "SELECT nome FROM liste WHERE nome = '" . $conn->real_escape_string($_SESSION['IDlista']) . "'";
    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $error=$_SESSION['vota_lista_value'] = "Voto non valido! Riprova o contatta l'amministratore. (errore #0809)";
        logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
        header("location: ./vota_lista.php");
        exit();
    }
    for ($i = 0;$i < count($_POST['cand']);$i++) {
        $sql = "SELECT nome,cognome FROM candidati WHERE candidati.codice_fiscale = '" . $conn->real_escape_string($_POST['cand'][$i]) . "' AND candidati.nome_lista='" . $conn->real_escape_string($_SESSION['IDlista'])."'";
        $result = $conn->query($sql);
        if ($result->num_rows <= 0) {
            $error=$_SESSION['vota_candidati_value'] = "Errore nel passaggio dei dati! Riprova o contatta l'amministratore. (errore #0810-$i)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./vota_candidati.php");
            exit();
        }
        $dati = $result->fetch_assoc();
        /*
            print_r($dati);
            echo $_SESSION['IDlista'];
            exit();
        */
        if ($i == 0) {
            $_SESSION['IDcandidato1'] = $_POST['cand'][$i];
        } else {
            $_SESSION['IDcandidato2'] = $_POST['cand'][$i];
        }
    }
    header("location: ./riepilogo.php");
    exit();
?>