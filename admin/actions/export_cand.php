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
header ( "Content-Disposition: attachment; filename=Candidati.xls" );


echo "NOME\tCOGNOME\tCODICE FISCALE\tLISTA APPARTENENZA\n";

$sql = "SELECT * FROM candidati ORDER BY nome_lista";
$result = $conn->query($sql);
$chiave=null;
while($row = $result->fetch_assoc()){
    if($chiave!=null && $chiave!=$row['nome_lista']){
        echo "\n";
    }
    $chiave=$row['nome_lista'];

    echo $row['nome']."\t".$row['cognome']."\t".$row['codice_fiscale']."\t".$row['nome_lista']."\n";

}


/*
echo "<table>";

$sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".DB_Credentials::getDBname()."' AND `TABLE_NAME`='candidati'";
$result = $conn->query($sql);

$names=array();
echo "<tr>";  
while ($row = $result->fetch_assoc()){
    array_push($names, $row['COLUMN_NAME']);
    echo "<th>".$row['COLUMN_NAME']."</th>";
}
echo "</tr>";

$sql = "SELECT * FROM candidati";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    echo "<tr>";
    for($i=0;$i<count($row);$i++){
        echo "<th>".$row[$names[$i]]."</th>";
    }
    echo "</tr>";
}
echo "</table>";*/

//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>
