<?php
    require '../php/classi.php';
    require '../php/function.php';
    include '../php/Period_manage.php';
    mb_internal_encoding( "UTF-8" );
    header( "cache-control: no-store,no-cache,must-revalidate" );
    session_start();
    IPcheck( "../" );
    //CONNESSIONE---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $_SESSION[ 'EMAIL' ] = strtolower( $_POST[ 'email' ] );
    $email               = strtolower( $_POST[ 'email' ] );
    $conn                = new mysqli( DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname() );
    // Check connection
    if ( $conn->connect_error ) {
        $error = $_SESSION[ 'send_mail_value' ] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #0100)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError( $error, $conn->real_escape_string( $email ) );
        header( "location: ./index.php" );
        exit( );
    }
    $conn->set_charset( "utf8" );
    //CONTROLLO DATI INSERITI---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    if ( !isset( $_POST[ 'email' ] ) ) {
        $error = $_SESSION[ 'send_mail_value' ] = "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail. (errore #0101)";
        //echo "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail";
        logError( $error, $conn->real_escape_string( $email ) );
        header( "location: ./index.php" );
        exit( );
    }
    if ( !chkEmail( $email ) ) {
        $error = $_SESSION[ 'send_mail_value' ] = "Controlla di avere inserito una mail valida. (errore #0102)"; //par fermare eventuali comandi mysql posti al posto dell'email
        //echo "Controlla di avere inserito una mail valida".$email;
        logError( $error, $conn->real_escape_string( $email ) );
        header( "location: ./index.php" );
        exit( );
    }
    //é amministratore? se si, reindirizza alla sezione amministratori
    $sql    = "SELECT * FROM account_amministratori WHERE email='" . $conn->real_escape_string( $email ) . "'";
    $result = $conn->query( $sql );
    if ( $result->num_rows > 0 ) {

        /*-------------------- DA TOGLIERE ------------------ SOLO PER I TEST----------------------*/
        if(strcmp($email, 'ammi.prova1@itismaglie.it')==0 || 
           strcmp($email, 'ammi.prova2@itismaglie.it')==0 
           ){
            logError("Un admin-tester tenta l'accesso... [".$_SERVER['REMOTE_ADDR']."]", $conn->real_escape_string($email));
        }   
         /*-----------------------------------------------------------------------*/


        $_SESSION[ 'AMM_email' ] = $email;
        $_SESSION[ 'AMM_ALERT' ] = ".";
        header( "location: ./login_pass.php" );
        exit( );
    } else {
        $error = $_SESSION[ 'send_mail_value' ] = "Controlla le credenziali e riprova. (errore #0103)";
        //echo "Un errore del browser ha impedito la ricezione della mail, prova a digitare nuovamente la mail";
        logError( $error, $conn->real_escape_string( $email ) );
        header( "location: ./index.php" );
        exit( );
    }
?>