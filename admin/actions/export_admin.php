<?php
    require '../../php/classi.php';
    require '../../php/function.php';
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    session_start();

    manageStartAMM("../../");
    $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
    if ($conn->connect_error) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../index.php");
        exit();
    }
    if ($_SESSION['LV']<1) {
        $error=$_SESSION['panel_info']= "Non disponi dei permessi necessari per accedere a questa sezione. (errore #8025)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../pannello_di_controllo.php");
        exit();
    }


//CREATE FILE----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=Admin.xls" );

   $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
    if ($conn->connect_error) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        header("location: ../index.php");
        exit();
    }

echo "email\tlivello_privilegi\n";

$sql = "SELECT * FROM account_amministratori ORDER BY livello_privilegi";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    echo $row['email']."\t" . ($row['livello_privilegi'] == 2 ? "ADMIN" : ($row['livello_privilegi'] == 0 ? "Viewer" : "Editor"))."\n";
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
