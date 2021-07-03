<?php
    require '../../php/classi.php';
    require '../../php/function.php';
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    session_start();

    manageStartAMM("../../");


//CREATE FILE----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
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
    
   header ( "Content-type: application/vnd.ms-excel" );
   header ( "Content-Disposition: attachment; filename=Votanti.xls" );

    echo "EMAIL\tDATA VOTO\n";

    $sql = "SELECT account_votanti.email,voti.data_voto FROM voti,account_votanti WHERE account_votanti.email=voti.email ORDER BY account_votanti.email";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        echo $row['email']."\t". $row['data_voto']."\n";
    }

    

//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    mysqli_close($conn);


?>
