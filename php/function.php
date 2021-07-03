<?php
    
    function chkEmail($email)
    {
        // elimino spazi, "a capo" e altro alle estremità della stringa
        $email = trim($email);
    
        // se la stringa è vuota sicuramente non è una mail
        if (!$email) {
            return false;
        }
    
        // controllo che ci sia una sola @ nella stringa
        $num_at = count(explode('@', $email)) - 1;
        if ($num_at != 1) {
            return false;
        }
    
        // controllo la presenza di ulteriori caratteri "pericolosi":
        if (strpos($email, ';') || strpos($email, ',') || strpos($email, ' ')) {
            return false;
        }
    
        // la stringa rispetta il formato classico di una mail?
        if (!preg_match('/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email)) {
            return false;
        }
        return true;
    }

    function MAIL_VALIDATE($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL)  && endsWith($email,"@itismaglie.it") && chkEmail($email);
    }

    function endsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }
    
    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
        //return '123456';
    }

    function chkPassAMM($password)
    {
        // la stringa rispetta il formato classico di una mail?
        if(!preg_match( '/^([\w0-9]{12})$/', $password)) {//lettere maiuscole e minuscole, underscore,numeri (lunghezza fissa di 12)
            return false;
        }
        return true;
    }

    function chkPass($password)
    {
        // la stringa rispetta il formato classico di una mail?
        if(!preg_match( '/^([\w0-9]{6})$/', $password)) {//lettere maiuscole e minuscole, underscore,numeri (lunghezza fissa di 12)
            return false;
        }
        return true;
    }

    function randomPasswordAMM() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 12; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    function logError($IDerror,$email){
        $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
    
        if ($conn->connect_error) {
            return;
        }
        $IDerror= $conn->real_escape_string($IDerror);
        $email= $conn->real_escape_string($email);

        if($email=="null" || !$email){
            return 0;
        }
        $sql = "SELECT * FROM errori WHERE messaggio_errore='" . $IDerror . "' AND email='".$email."'";
        //echo $sql;
        $result=$conn->query($sql);
        if ($result->num_rows <= 0) {
            $sql = "INSERT INTO errori (messaggio_errore,email) VALUES ('$IDerror','$email')";
            //echo $sql;
            //exit();
            $conn->query($sql);
        }
        return;
    }


    function manageStart($conn){
        require './php/Period_manage.php';
        IPcheck("./");
        /*$conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
    
        // Check connection
        if ($conn->connect_error) {
            $error=$_SESSION['send_mail_value'] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #0300)";
            //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email'])) ? $_SESSION['AUT_email'] : "null"),-1);
            exit();
        }
        $conn->set_charset("utf8");*/

        //CONTROLLO DATI---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        if (!isset($_SESSION['AUT_email'])) {
            //cose
            //echo "AUTemail";
            header("location: ./sessione_scaduta.php");
            exit();
        }
        $email=$_SESSION['AUT_email'];
        if (!isset($_SESSION['AUT_password'])) {
            //cose
            //echo "AUTpass";
            header("location: ./sessione_scaduta.php");
            exit();
        }
        $password=$_SESSION['AUT_password'];
        $sql = "SELECT voti.data_voto, account_votanti.tentativi FROM voti,account_votanti WHERE account_votanti.email = '" . $conn->real_escape_string($email) . "' AND voti.email=account_votanti.email AND password='" . md5($password) . "'";
/*echo $sql;
    exit();*/        
        $result = $conn->query($sql);
        if ($result->num_rows <= 0) {
            $error = $_SESSION['send_mail_value'] = "Sessione scaduta. (errore #0204)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./index.php");
            exit();
        }
        $dati = $result->fetch_assoc();
        if($dati['tentativi']<=0){
            if($dati['tentativi']==0){
                $error = $_SESSION['send_mail_value'] = "Hai esaurito i tentativi a disposizione. Contatta un amministratore. (errore #c02064)";
            }
            else{
                $error = $_SESSION['send_mail_value'] = "Un amministratore ha bloccato la votazione per questo utente. (errore #d02064)";
            }
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./index.php");
            exit();
        }    
        if ($dati['data_voto'] != null && $dati['data_voto'] != "" && $dati['data_voto'] != "null" && $dati['data_voto'] != "NULL") {
            $error=$_SESSION['send_mail_value'] = "Il proprietario di questo indirizzo email ha gia' espresso il suo voto. E' possibile visualizzare il riepilogo del voto nella propria casella di posta elettonica. (errore #0302)";
            //echo "Il proprietario di questo indirizzo email ha gia' espresso il suo voto. E' possibile visualizzare il riepilogo del voto nella propria casella di posta elettonica.".$dati['data_voto'];
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
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
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./index.php");
            exit();
        }
        if (strcmp($now, $start) < 0) {
            $error=$_SESSION['send_mail_value'] = "Le votazione non sono ancora iniziate per questo utente. Torna qui il " . $start . ". (errore #0305)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./index.php");
            exit();
        }
        if (strcmp($now, $end) > 0) {
            $error=$_SESSION['send_mail_value'] = "Le votazione per questo utente sono terminate il " . $end . ". (errore #0306)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./index.php");
            exit();
        }
        //mysqli_close($conn);
    }

    function manageConsent($conn){
        if (strcmp($_SESSION['consenso'], "1") != 0 || strcmp($_SESSION['letto_regole'], "1") != 0) {
            $error=$_SESSION['err_consenso'] = "Sembra che tu non abbia ancora accettato i termini e il regolamento. Accettali per continuare. (errore #1007)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./termini_e_condizioni.php");
            exit();
        }
    }

    function manageData($conn){
        $ret=array();
        /*$conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
    
        // Check connection
        if ($conn->connect_error) {
            $error=$_SESSION['send_mail_value'] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #0300)";
            //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email'])) ? $_SESSION['AUT_email'] : "null"),-1);
            exit();
        }
        $conn->set_charset("utf8");*/

        $ret['nome_lista']         = "ASTENUTO";
        $ret['nome_candidato1']    = "ASTENUTO";
        $ret['nome_candidato2']    = "ASTENUTO";
        $ret['cognome_candidato1'] = "";
        $ret['cognome_candidato2'] = "";

        if(isset($_SESSION['IDlista'])){
            if ( $_SESSION[ 'IDlista' ] != -1 ) {
                if(isset($_SESSION['IDcandidato1'])){
                    if ( $_SESSION[ 'IDcandidato1' ] != -1 ) {
                        $n_cand     = 1;
                        $lista_cand = array(
                             0 => $_SESSION[ 'IDcandidato1' ] 
                        );
                    }
                }
                else{
                    $error=$_SESSION['vota_lista_value'] = "Errore nell'acquisizione del voto! Riprova o contatta l'amministratore. (errore #0700)";
                    logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
                    header("location: ./vota_lista.php");
                    exit();
                }
                if(isset($_SESSION['IDcandidato2'])){
                    if ( $_SESSION[ 'IDcandidato2' ] != -1 ) {
                        $n_cand     = 2;
                        $lista_cand = array(
                             0 => $_SESSION[ 'IDcandidato1' ],
                            1 => $_SESSION[ 'IDcandidato2' ] 
                        );
                    }
                }
                else{
                    $error=$_SESSION['vota_lista_value'] = "Errore nell'acquisizione del voto! Riprova o contatta l'amministratore. (errore #0700)";
                    logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
                    header("location: ./vota_lista.php");
                    exit();
                }
                $sql    = "SELECT nome FROM liste WHERE nome = '" . $conn->real_escape_string($_SESSION[ 'IDlista' ])."'";
                $result = $conn->query( $sql );
                if ( $result->num_rows <= 0 ) {
                    $error=$_SESSION[ 'vota_lista_value' ] = "Voto non valido! Riprova o contatta l'amministratore. (errore #0908)";
                    logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
                    header( "location: ./vota_lista.php" );
                    exit( );
                }
                $dati       = $result->fetch_assoc();
                $ret['nome_lista'] = $dati[ 'nome' ];
                for ( $i = 0; $i < $n_cand; $i++ ) {
                    $sql    = "SELECT nome, cognome FROM candidati WHERE codice_fiscale = '" . $conn->real_escape_string($lista_cand[ $i ]) . "' AND candidati.nome_lista='" . $conn->real_escape_string($_SESSION[ 'IDlista' ])."'";
                    $result = $conn->query( $sql );
                    if ( $result->num_rows <= 0 ) {
                        $error=$_SESSION[ 'vota_candidati_value' ] = "Errore nel passaggio dei dati! Riprova o contatta l'amministratore. (errore #0909-$i)";
                        logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
                        header( "location: ./vota_candidati.php" );
                        exit( );
                    }
                    $dati = $result->fetch_assoc();
                    /*
                    print_r($dati);
                    echo $_SESSION['IDlista'];
                    exit();
                    */
                    if ( $i == 0 ) {
                        $ret['nome_candidato1']    = $dati[ 'nome' ];
                        $ret['cognome_candidato1'] = $dati[ 'cognome' ];
                    } else {
                        $ret['nome_candidato2']    = $dati[ 'nome' ];
                        $ret['cognome_candidato2'] = $dati[ 'cognome' ];
                    }
                }
            }
        }
        else{
            $error=$_SESSION['vota_lista_value'] = "Errore nell'acquisizione del voto! Riprova o contatta l'amministratore. (errore #0700)";
            logError($error,$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
            header("location: ./vota_lista.php");
            exit();
        }

        
        return $ret;
        //mysqli_close($conn);
    }


    function manageStartAMM($root){
        require $root.'php/Period_manage.php';
        //CONTROLLO DATI INSERITI---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        if(!isset($_SESSION['AMM_email'])){
            unset($_SESSION['AMM_password']);
            header("location: ".$root."sessione_scaduta.php");
            exit();
        }

        if(!isset($_SESSION['AMM_password'])){
            unset($_SESSION['AMM_email']);
            header("location: ".$root."sessione_scaduta.php");
            exit();
        }
        if(!chkPassAMM($_SESSION['AMM_password'])){
            unset($_SESSION['AMM_email']);
            unset($_SESSION['AMM_password']);
            header("location: ".$root."sessione_scaduta.php");
            exit();
        }
        if(!MAIL_VALIDATE($_SESSION['AMM_email'])){
            unset($_SESSION['AMM_email']);
            unset($_SESSION['AMM_password']);
            header("location: ".$root."sessione_scaduta.php");
            exit();
        }

        //CONNESSIONE---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


        $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
        if ($conn->connect_error) {
            $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
            //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            if($root=='../'){
                header("location: ./index.php");
            }
            else{
                header("location: ../index.php");
            }
            
            exit();
        }

        //QUERY + CONTROLLO DATI DATABASE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $sql = "SELECT email, livello_privilegi FROM account_amministratori WHERE email = '" . $conn->real_escape_string($_SESSION['AMM_email']) . "' AND password='" . md5($_SESSION['AMM_password']) . "'";
        $result = $conn->query($sql);
        if ($result->num_rows <= 0) {
            $error=$_SESSION['AMM_pass_value']= "Credenziali errate. (errore #806)";
            //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
            if($root=='../'){
                header("location: ./index.php");
            }
            else{
                header("location: ../index.php");
            }
            exit();
        }
        $dati=$result->fetch_assoc();

        //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        mysqli_close($conn);
        if($dati['livello_privilegi']>=2){
            IPrestore();
        }
    }
?>