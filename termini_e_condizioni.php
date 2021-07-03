<?php
    mb_internal_encoding( "UTF-8" );
    header( "cache-control: no-store,no-cache,must-revalidate" );
    require './php/classi.php';
    require './php/function.php';
    session_start();
    $conn = new mysqli( DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname() );
    // Check connection
    if ( $conn->connect_error ) {
        $error = $_SESSION[ 'send_mail_value' ] = "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #0300)";
        //echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
        logError( $error, $conn->real_escape_string( ( isset( $_SESSION[ 'AUT_email' ] ) ? $_SESSION[ 'AUT_email' ] : "null" ) ) );
        header( "location: ./index.php" );
        exit( );
    }
    
    
    
    
    //RIMUOVI
    //logError("accesso",$conn->real_escape_string((isset($_SESSION['AUT_email']) ? $_SESSION['AUT_email'] : "null")));
    
    
    
    
    
    $conn->set_charset( "utf8" );
    manageStart( $conn );
    //CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    mysqli_close( $conn );
?>
<html>
    <head>
        <title>Elezioni</title>
        <link rel="shortcut icon" href="./img/logo1.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Andrea Cazzato">
        <link rel="stylesheet" href="./css/w3.css">
        <script>
            /*function Initialize() {
                if (getCookie('err_consenso') != '') {
                    //alert("");
                    document.getElementById('err_consenso').innerHTML = getCookie('err_consenso').replaceAll("+"," ");
                    eraseCookie('err_consenso');
                }
            }*/

            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                var expires = "expires="+ d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
            function eraseCookie(name) {
                document.cookie = name+'=; Max-Age=-99999999;';
            }
            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                      c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
        </script>
    <meta charset="UTF-8"></head>
    <body onload="update();setInterval(update, 1000);">

        <?php
    include './php/header.php';
?>
       <div id="BarraUtente" class="w3-bar w3-border w3-light-grey">
            <span id="barra_chiave" class="w3-bar-item w3-border-right w3-left" style="padding-top:3px;padding-bottom:3px">
                 <img alt="utente" src="./img/utente.png" style="width: 32px;margin-right:15px"/><?php
    echo $_SESSION[ 'AUT_email' ];
?>
          </span >
            <button class="w3-button w3-border-left w3-right" onclick="location.href = './esci.php';">Esci</button>
        </div>
    
        <div class="w3-modal-content w3-margin-top">
            <div class="w3-container w3-card-4">
                <header class="w3-container w3-border-bottom">
                    <h3>Accetta le condizioni per proseguire</h3>
                </header>
                <div class="w3-margin" style="width: 95% !important;  height: 190px !important; padding: 4px; border: 1px solid #EEE; border-right: 0 solid;overflow: auto">
                    <h3>Regolamento</h3>
                
                    il contenuto qui<br />
                    il contenuto qui<br />
                    il contenuto qui<br />
                    il contenuto qui<br />
                    il contenuto qui<br />
                    il contenuto qui<br />
                    il contenuto qui<br />
                    il contenuto qui<br />
                    il contenuto qui<br />
                    <hr />
                    <h3>Termini e Condizioni</h3>
                    <br />
                    Questo sito web è di proprietà e gestito dall'istituto IISS "E.Mattei"-Maglie. I presenti Termini stabiliscono i termini e le condizioni in base ai quali è possibile utilizzare il nostro sito Web e i servizi da noi offerti. Accedendo o utilizzando il sito Web per il nostro servizio, l'utente accetta di aver letto, compreso e accettato di essere vincolato dai seguenti Termini.
                    <br />
                    Chiunque sia stato fornito di un account autorizzato all'accesso di questo sistema informatico ha il diritto di:
                    <br /> 
                    -Esprimere da zero a una preferenza di lista;
                    <br /> 
                    -Esprimere da zero a due preferenze riguardo ai candidati della lista votata. Nel caso l'utente non abbia votato alcuna lista, non potrà votare alcun candidato;
                    <br />
                    All'utente verra' inviata una mail contenente il riepilogo delle sue preferenze, una volta aver finito la procedura di votazione.
                    <br />
                    Il voto che l'utente dara', sara' associato al suo account, tuttavia nessuno, a parte l'amministratore, avra' accesso a tale informazione. L'amministratore sara' il solo ad avere accesso alla lista dei voti. L'amministratore si impegnera' a non far trapelare alcuna informazione riguardo l'identita' dei votanti. Il voto resterà segreto e custodito nel nostro database. Nessun ente di terze parti avrà accesso alla lista dei voti.
                    <br />
                    <br />
                    Qualsiasi tentativo di manomissine delle votazione sara' punito dalle leggi vigenti. 
                    <br />
                    -L'accesso ad un account destinato ad un'altra persona sara' punito dalle leggi vigenti sul reato di sostituzione di persona
                    <br />
                    -Qualsiasi tentativo di manomissione di questo sistema informatico sara' punito delle leggi vigenti sul reato di accesso abusivo ad un sistema informatico.
                    <br />
                    -Qualsiasi tentativo di manomissine o di accesso non autorizzato al database contenente i dati della votazione sara' punito delle leggi vigenti sul reato di accesso abusivo ad un sistema informatico.
                    <br />
                </div>
                <form action="./acc_regolamento.php">
                    <div class="w3-container" style="margin:0;padding:0">
                    <label class="w3-small w3-text-orange" id="err_consenso" style="margin:0">
                        <?php
    mb_internal_encoding( "UTF-8" );
    header( "cache-control: no-store,no-cache,must-revalidate" );
    session_start();
    if ( $_SESSION[ 'err_consenso' ] != '' && isset( $_SESSION[ 'err_consenso' ] ) ) {
        echo $_SESSION[ 'err_consenso' ];
        $_SESSION[ 'err_consenso' ] = "";
    }
?>
                  </label>
                        </div>
                    <p>
                        <input class="w3-check" type="checkbox" required>
                        <label>Ho letto il regolamento</label>
                    </p>
                    <p>
                        <input class="w3-check" type="checkbox" required>
                        <label>Accetto tutte le condizioni sopra presenti</label>
                    </p>
                    <p>
                        <button class="w3-button w3-section w3-teal w3-ripple w3-right" onclick=""> Continua </button>
                    </p>
                </form>
            </div>

        </div>
    </body>
</html>