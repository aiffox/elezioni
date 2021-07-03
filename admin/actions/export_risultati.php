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

 
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=Risultati.xls" );





$sql = "SELECT nome FROM liste ORDER BY nome";
$liste = $conn->query($sql);
while($lista = $liste->fetch_assoc()){
    echo "NOME\tVOTI\n";
    $sql = "SELECT COUNT(*) as voti FROM voti WHERE nome_lista='".$lista['nome']."'";
    $voti_lista = $conn->query($sql)->fetch_assoc();
    echo $lista['nome']."\t".$voti_lista['voti']."\n\n";

    $sql = "SELECT * FROM candidati WHERE nome_lista='".$lista['nome']."' ORDER BY nome_lista";
    $candidati = $conn->query($sql);
    echo "\tNOME\tCOGNOME\tCODICE FISCALE\tVOTI\n";
    while($candidato = $candidati->fetch_assoc()){
        $sql = "SELECT COUNT(*) as voti FROM voti WHERE CF_primo_candidato='".$candidato['codice_fiscale']."' OR CF_secondo_candidato='".$candidato['codice_fiscale']."'";
        $voti_candidato = $conn->query($sql)->fetch_assoc();
        echo "\t".$candidato['nome']."\t".$candidato['cognome']."\t".$candidato['codice_fiscale']."\t".$voti_candidato['voti']."\n";
    }
    echo "\n\n";
}

//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>
