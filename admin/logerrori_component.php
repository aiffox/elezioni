<?php
    require '../php/classi.php';
    require '../php/function.php';
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
    session_start();

    manageStartAMM("../");
?>
            
            
            <br />
                <div class="w3-section" style="width:100%" >
                    <?php if($_SESSION['LV']>=1){?>
                        <button class="w3-button w3-border w3-section w3-light-grey w3-ripple w3-left w3-margin-left" onclick="asyncOpen('./actions/delete_errori.php', 'logerrori_component.php');"> Svuota</button>
                    <?php } ?>
                    <button class="w3-button w3-circle w3-section w3-right w3-margin-right " style="padding:0"><img class="" src="../img/refresh.png" alt="refresh" onclick="getComponent('logerrori_component.php');" width="40px"/></button>
                </div>
            <header class="w3-container w3-border-bottom">
                <h3>Log errori:</h3>
            </header>

            <br />
            <div style="overflow-x:scroll">
                <table class="w3-table w3-striped w3-border">
                    
                    <?php  
                        $conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
                        if ($conn->connect_error) {
                            $error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #8000)";
                            logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
                            header("location: ./index.php");
                            exit();
                        }
                        
                        $sql =  " SELECT data ,messaggio_errore, email as riscontrato_da FROM errori ORDER BY data DESC";
                        //echo $sql;
                    //exit();
                        $result = $conn->query($sql);
                        if ($result->num_rows <= 0) {
                            echo "<h5 class=\"w3-text-red\">Nessun errore al momento</h5>";
                        }
                        else{
                    ?>
                            <thead>
                                <tr class="w3-teal">
                                    <th class="w3-border-right">Data</th>
                                    <th class="w3-border-right">Errore</th>
                                    <th class="w3-border-right">Riscontrato da</th>
                                </tr>
                            </thead>
                    <?php
                            for($i=0;$errore=$result->fetch_assoc();$i++){
                    ?>       
                                <tr>
                                    <td class="w3-border-right"><?php echo $errore['data']; ?></td>
                                    <td class="w3-border-right"><?php echo $errore['messaggio_errore']; ?></td>
                                    <td class="w3-border-right"><?php echo $errore['riscontrato_da']; ?></td>
                                </tr>
                    <?php
                            }
                        }
                    ?>
                </table>
            </div>
            <br />

<?php

//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>