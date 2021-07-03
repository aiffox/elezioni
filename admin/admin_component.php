<?php
    require '../php/classi.php';
    require '../php/function.php';
    mb_internal_encoding( "UTF-8" );
    header( "cache-control: no-store,no-cache,must-revalidate" );
    session_start();
    
    manageStartAMM( "../" );
    $conn = new mysqli( DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname() );
    if ( $conn->connect_error ) {
        $error = $_SESSION[ 'AMM_pass_value' ] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError( $error, $conn->real_escape_string( ( isset( $_SESSION[ 'AMM_email' ] ) ? $_SESSION[ 'AMM_email' ] : "null" ) ) );
        //header( "location: ./index.php" );
        exit( );
    }
    if ( $_SESSION[ 'LV' ] < 1 ) {
        $error = $_SESSION[ 'panel_info' ] = "Non disponi dei permessi necessari per accedere a questa sezione. (errore #8025)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError( $error, $conn->real_escape_string( $_SESSION[ 'AUT_email' ] ), -1 );
        //header( "location: ./pannello_di_controllo.php" );
        exit( );
    }
    
    
?>
         
            <br />

            <div class="w3-section" style="text-align: center;" >
                <a href="./actions/export_admin.php" class="w3-light-grey w3-button w3-border w3-round">Scarica elenco admin</a>
            </div>
             <header class="w3-container w3-border-bottom">
                <h3>Elenco Amministratori:</h3>
            </header>
            
            <br />
            <div class="w3-panel">
                <table class="w3-table w3-bordered w3-striped w3-border">
                    
                    <?php
    $conn = new mysqli( DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname() );
    if ( $conn->connect_error ) {
        $error = $_SESSION[ 'AMM_pass_value' ] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #8000)";
        logError( $error, $conn->real_escape_string( ( isset( $_SESSION[ 'AMM_email' ] ) ? $_SESSION[ 'AMM_email' ] : "null" ) ) );
        //header( "location: ./index.php" );
        exit( );
    }
    $sql    = "SELECT * FROM account_amministratori";
    $result = $conn->query( $sql );
    /*print_r($result);
    exit();*/
    if ( $result->num_rows <= 0 ) {
        echo "NESSUN AMMINISTRATORE";
    } else {
?>
                           <thead>
                                <tr class="">
                                    <th class="w3-border-right">Email</th>
                                    <th class="w3-border-right">LV</th>
                                    <?php
        if ( $_SESSION[ 'LV' ] >= 2 && false ) { //&& false MOMENTANEO  
?>
                                       <th class="w3-border-right">Hash Password</th>
                                    <?php
        }
?>
                               </tr>
                            </thead>
                        <?php
        while ( $admin = $result->fetch_assoc() ) {
?>
                               <tr>
                                    <td class="w3-light-grey w3-button w3-border w3-round"><?php
            echo $admin[ 'email' ];
?></td>
                                    <td class="w3-light-grey w3-button w3-border w3-round"><?php
            echo $admin[ 'livello_privilegi' ] == 2 ? "ADMIN" : ($admin[ 'livello_privilegi' ] == 0 ? "Viewer" : "Editor");
?></td>
                                    <?php
            if ( $_SESSION[ 'LV' ] >= 2 ) {
?>
                                   <?php
                if ( false ) { //MOMENTANEA 
?>
                                       
                                            <td class="w3-light-grey w3-button w3-border w3-round"><?php
                    echo $admin[ 'password' ];
?></td>
                                        <?php
                }
?>
                                   <td class="w3-light-grey w3-button w3-border w3-round" style="margin:0;padding:0;width:40px;"><button class="w3-white w3-button" style="margin:0;width:100%;padding:0;height:35px;" onclick="document.getElementById('del_admin').innerText = 'L\'amministratore \'<?php echo $admin[ 'email' ]; ?>\' verra\' eliminato.';
                                                                                                                                                                                                                                                    document.getElementById('ID_adminn').onclick=function (){ asyncOpen('actions/delete_admin.php?ID=<?php echo $admin[ 'email' ]; ?>' ,'admin_component.php');};
                                                                                                                                                                                                                                                    document.getElementById('AdminDelete').style.display = 'block';">X</button></td>
                                    <?php } ?>
                               </tr>
                                <?php
        }
?>
                           </table>
                            <br />
                            <?php
                            if ( $_SESSION[ 'LV' ] >= 2 ) {
?>
                               <form name="frmAdmin" >
                                    <table class="w3-table  w3-striped ">
                                        <tr class="">
                                            <td ><label for="nome">Email admin:</label><input name="nome" class="w3-input w3-border w3-round" type="email"  required placeholder="Email..."/></td>
                                            <td ><label for="livello">LV:</label>
                                                <select name="livello" class="w3-input w3-border w3-round">
                                                  <option value="0">Viewer</option>
                                                  <option value="1">Editor</option>
                                                </select>
                                                <!--input name="livello1" class="w3-input w3-border w3-round" type="number" value="0" max="1" min="0"  required placeholder="Livello privilegio..."/-->
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="w3-section" style="text-align: center;" >
                                        <input type="button" class="w3-light-grey w3-button w3-border w3-round" onclick="if (lvValidate(document.frmAdmin.livello.value)) { asyncOpen('actions/insert_admin.php?livello=' + document.frmAdmin.livello.value +'&nome=' + document.frmAdmin.nome.value,'admin_component.php') } " value="Aggiungi amministratore">
                                    </div>
                                </form>
                    <?php
        }
    }
?>
           </div>
    <br />
    
    <div id="AdminDelete" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Eliminare l'amministratore?</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('AdminDelete').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/trash_mail.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 id="del_admin"></h3>
                <hr />
                <button id="ID_adminn" onclick="" class="w3-button w3-teal w3-right"> Elimina</button>
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
    <div id="infoInsert" class="w3-modal" style="display:<?php
        if ( isset( $_SESSION[ 'insert_info' ] ) ) {
            echo "block";
        } else {
            echo "none";
        }
    ?>">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-teal">
                <h1 style="text-align:center">Informazioni sull'aggiunta</h1>
            </header>
            <div class="w3-center">
                <br />
                <span onclick="document.getElementById('infoInsert').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
                <img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
            </div>
            <div class="w3-container">
                <h3 ><?php
                    echo $_SESSION[ 'insert_info' ];
                    unset( $_SESSION[ 'insert_info' ] );
                ?></h3>
            </div>
            <br />
        </div>
        <br />
    </div>

<?php
    
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    
    mysqli_close( $conn );
    
    
?>