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
        logError($error, $conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
        header("location: ./index.php");
        exit();
    }
    $conn->set_charset("utf8");
    manageStart($conn);
    manageConsent($conn);
    /*
        if(strcmp($dati['consenso'], "1")!=0 || strcmp($dati['letto_regole'], "1")!=0){
        setcookie("err_consenso", "Sembra che tu non abbia ancora accettato i termini e il regolamento. Accettali per continuare. (errore #9025)");
        header("location: ./termini_e_condizioni.php");
        exit();
        }*/
    if (!isset($_POST['lista'])) {
        $error=$_SESSION['vota_lista_value'] = "Errore nell'acquisizione del voto! Riprova o contatta l'amministratore. (errore #0609)";
        logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
        header("location: ./vota_lista.php");
        exit();
    }
    $IDlista = $conn->real_escape_string($_POST['lista']);
    //QUERY SELECT--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    if ($IDlista != 'null') {
        $sql = "SELECT nome FROM liste WHERE nome = '" . $conn->real_escape_string($IDlista) . "'";
        $result = $conn->query($sql);
        if ($result->num_rows <= 0) {
            $error= $_SESSION['vota_lista_value'] = "Voto non valido! Riprova o contatta l'amministratore. (errore #0610)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            mysqli_close($conn);
            header("location: ./vota_lista.php");
            exit();
        } else {
            $_SESSION['IDlista'] = $IDlista;
            mysqli_close($conn);
            header("location: ./vota_candidati.php");
            exit();
        }
    } else {
        $_SESSION['IDlista'] = - 1;
        $_SESSION['IDcandidato1'] = - 1;
        $_SESSION['IDcandidato2'] = - 1;
        mysqli_close($conn);
        header("location: ./riepilogo.php");
        exit();
    }
?>