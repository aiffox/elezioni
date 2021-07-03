<?php
    require '../php/classi.php';
    require '../php/function.php';
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    session_start();

    manageStartAMM("../");
    $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
    if ($conn->connect_error) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        //header("location: ./index.php");
        exit();
    }

    $sql = "SELECT inizio_votazione as start,fine_votazione as end FROM informazioni";

    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #12805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        //header("location: ./index.php");
        exit();
    }
    $periodo=$result->fetch_assoc();
    //print_r( $periodo);
    //exit();
    $PREPARAZIONE=($periodo['end']==null || $periodo['start']==null || (date('Y-m-d H:i')<$periodo['end'] && date('Y-m-d H:i')<$periodo['start']));
    $DURANTE=(date('Y-m-d H:i')>=$periodo['start'] && date('Y-m-d H:i')<=$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
    $TERMINE=(date('Y-m-d H:i')>$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
    

    $sql = "SELECT nome_votazione,inizio_votazione as start,fine_votazione as end FROM informazioni";
    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #12805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
        //header("location: ./index.php");
        exit();
    }
    $dati=$result->fetch_assoc();
?>

        <?php if($TERMINE){ ?>
            <header class="w3-container w3-border-bottom w3-section">
                <h3>Generali:</h3>
            </header>
            
            <div class="w3-margin-bottom">
                <a href="./actions/export_risultati.php" class="w3-margin-top w3-light-grey w3-button w3-border w3-round">Scarica risultato votazioni</a>
                
                <?php if($_SESSION['LV']>=2){ ?>
                    <a onclick="asyncOpen('actions/delete_votazione.php?conferma=SI','gestione_component.php');" class="w3-margin-top w3-red w3-button w3-border w3-round" >  Elimina votazione</a>
                <?php } ?>
            </div>
            <br />
        <?php } ?>

    <header class="w3-container w3-border-bottom w3-section">
        <h3>Nome votazione:</h3>
    </header>
    <?php
    
    //echo "b".$PREPARAZIONE."b".$DURANTE."b".$TERMINE;
    ?>
    <?php if($_SESSION['LV']>=2 && $PREPARAZIONE){ ?>
     <form name="frmNome" >
    <?php } ?>
        <input class="w3-input w3-border w3-round" maxlength="200" name="nome" type="text" <?php echo "value=\"". htmlentities($dati['nome_votazione'])."\"" ; 
                                                                    if($_SESSION['LV']<2 || !$PREPARAZIONE){
                                                                        echo "readonly ";
                                                                    }
                                                                ?> required/>  
     <?php if($_SESSION['LV']>=2 && $PREPARAZIONE){ ?>    
        <div class="w3-margin-top" style="text-align: center;" >
            <input type="button" class="w3-light-grey w3-button w3-border w3-round" value="Aggiorna" onclick="asyncOpen('actions/update_nome_votazione.php?nome=' + encodeURIComponent(document.frmNome.nome.value),'gestione_component.php');">
        </div>
    </form>
    <?php } ?>
<header class="w3-container w3-border-bottom w3-section">
    <h3>Periodo votazione:</h3>
</header>
<div class="w3-panel w3-margin-bottom">
    <?php if($_SESSION['LV']>=1 && $PREPARAZIONE){ ?>
        <form name="frmData" >
    <?php } ?>
    <!--div class="w3-margin-bottom">
        <a href="./export_risultati.php" class="w3-margin-top w3-light-grey w3-button w3-border w3-round" > Inizia votazione</a>
        <a href="./liste/export_cand.php" class="w3-margin-top w3-light-grey w3-button w3-border w3-round" >  Termina votazione</a>
                
    </div-->
    <table class="w3-table  w3-striped ">
        <tr class="">
            <td ><label for="inizio">Inizio:</label><input name="inizio" class="w3-input w3-border w3-round" onchange="endSet();" type="datetime-local" <?php 
                                                                                                                            if($_SESSION['LV']<1 || !$PREPARAZIONE){
                                                                                                                                echo "readonly ";
                                                                                                                            }
                                                                                                                            if($dati['start']!=null){
                                                                                                                                $today=date("Y-m-dTH:i");
                                                                                                                                echo "value=\"".str_replace(" ","T",$dati['start'])."\"";
                                                                                                                                if($today<$dati['start']){
                                                                                                                                    echo "min=\"".$today."\"";
                                                                                                                                }
                                                                                                                                else{
                                                                                                                                    echo "min=\"".str_replace(" ","T",$dati['start'])."\"";
                                                                                                                                }
                                                                                                                            }
                                                                                                                        ?> required/></td>
            <td ><label for="fine">Termine:</label><input name="fine" class="w3-input w3-border w3-round" type="datetime-local" <?php 
                                                                                                                            if($_SESSION['LV']<1 || !$PREPARAZIONE){
                                                                                                                                echo "readonly ";
                                                                                                                            }
                                                                                                                            if($dati['end']!=null){
                                                                                                                                echo "value=\"".str_replace(" ","T",$dati['end'])."\"";
                                                                                                                            }
                                                                                                                        ?> required/></td>
        </tr>
    </table>
        <?php if($_SESSION['LV']>=1 && $DURANTE){ ?>  
            <div class="w3-margin-top" style="text-align: center;" >
                <input type="button" class="w3-red w3-button w3-border w3-round" value="Termina" onclick="asyncOpen('actions/update_periodo.php?termina=SI','gestione_component.php');">
            </div>
        <?php } ?>
    <?php if($_SESSION['LV']>=1 && $PREPARAZIONE){ ?>    
        <div class="w3-margin-top" style="text-align: center;" >
            <input type="button" class="w3-light-grey w3-button w3-border w3-round" value="Aggiorna" onclick="asyncOpen('actions/update_periodo.php?inizio=' + document.frmData.inizio.value +'&fine=' + document.frmData.fine.value,'gestione_component.php');">
        </div>
    </form>
    <?php } ?>
</div>
<?php if( !$PREPARAZIONE){ ?>    
    <header class="w3-container w3-border-bottom w3-section">
        <h3>Classifica liste:</h3>
    </header>
    <div class="w3-panel w3-margin-bottom">
        <?php 
            $sql = " SELECT COUNT(*) AS votii, liste.nome".
                    " FROM voti,liste".
                    " WHERE voti.nome_lista=liste.nome".
                    " GROUP BY liste.nome".
                    " ORDER BY votii DESC".
                    " LIMIT 3";
            //echo $sql;
            //echo "<br>";
            //echo "<br>";
            $result = $conn->query($sql);
                
            //print_r($result);
            //echo "<br>";
            //echo "<br>";
            //print_r($conn);
            if ($result->num_rows <= 0) {
                echo "<h5 class=\"w3-text-red\">Ancora nessun voto espresso!</h5>";
            }
            else{
        ?>
                    
                <table class="w3-table w3-striped w3-border">
                    <thead>
                        <tr class="w3-teal">
                            <th class="w3-border-right" style="width:90px">PODIO</th>
                            <th class="w3-border-right">NOME</th>
                            <th class="w3-border-right">VOTI</th>
                        </tr>
                    </thead>
        <?php
                        for($i=1;$row=$result->fetch_assoc();$i++){
                            if($i==1){
                                $IDlista=$nome_lista=$row['nome'];
                            }
                            //print_r($row);
            ?>
                            <tr class="">
                                <td class="w3-border-right"><?php echo $i; ?></td>
                                <td class="w3-border-right"><?php echo $row['nome']; ?></td>
                                <td class="w3-border-right"><?php echo $row['votii']; ?></td>
                            </tr>
        <?php
                        }
        ?>
                </table>
        <?php
            }
        ?>
    </div>
    <br />
    <?php if($nome_lista){  ?>
        <header class="w3-container w3-border-bottom  w3-section">
            <h3>Classifica candidati <?php echo "di '$nome_lista'"; ?>:</h3>
        </header>
        <div class="w3-panel w3-margin-bottom">
            <?php 
                if($IDlista){
                    $sql =  " SELECT candidati.nome,candidati.cognome,COUNT(*) AS votii ".
                            " FROM candidati,voti".
                            " WHERE (voti.CF_primo_candidato=candidati.codice_fiscale OR voti.CF_secondo_candidato=candidati.codice_fiscale) AND candidati.nome_lista= '".$IDlista."' ".
                            " GROUP BY candidati.codice_fiscale ".
                            " ORDER BY votii DESC".
                            " LIMIT 3 ";
                    //echo $sql;
                    //echo "<br>";
                    //echo "<br>";
                    $result = $conn->query($sql);
                
                    //print_r($result);
                    //echo "<br>";
                    //echo "<br>";
                    //print_r($conn);
                        if ($result->num_rows <= 0) {
                            echo "<h5 class=\"w3-text-red\">Ancora nessun voto espresso!</h5>";
                        }
                        else{
                ?>
                    
                            <table class="w3-table w3-striped w3-border">
                                <thead>
                                    <tr class="w3-teal">
                                        <th class="w3-border-right">PODIO</th>
                                        <th class="w3-border-right">NOME</th>
                                        <th class="w3-border-right">COGNOME</th>
                                        <th class="w3-border-right">VOTI</th>
                                    </tr>
                                </thead>
                <?php
                                for($i=1;$row=$result->fetch_assoc();$i++){
                ?>
                                    <tr class="">
                                        <td class="w3-border-right"><?php echo $i; ?></td>
                                        <td class="w3-border-right"><?php echo $row['nome']; ?></td>
                                        <td class="w3-border-right"><?php echo $row['cognome']; ?></td>
                                        <td class="w3-border-right"><?php echo $row['votii']; ?></td>
                                    </tr>
                <?php
                                }
                ?>
                            </table>
                <?php
                        }
                    }
                ?>  
        </div>
    <?php } ?>
<?php } ?>

<div id="infoUpdate_" class="w3-modal" style="display:<?php if(isset($_SESSION['update_info'])){ echo "block"; }else{ echo "none"; } ?>">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Informazioni sull'aggiornamento</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('infoUpdate_').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 ><?php echo $_SESSION['update_info']; unset($_SESSION['update_info']); ?></h3>
            </div>
            <br />
        </div>
        <br />
    </div>
<div id="infoDelete" class="w3-modal" style="display:<?php
            if ( isset( $_SESSION[ 'delete_info' ] ) ) {
                echo "block";
            } else {
                echo "none";
            }
        ?>">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Informazioni sull'eliminazione</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('infoDelete').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 ><?php
                    echo $_SESSION[ 'delete_info' ];
                    unset( $_SESSION[ 'delete_info' ] );
                ?></h3>
            </div>
            <br />
        </div>
        <br />
    </div>
<?php

//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>