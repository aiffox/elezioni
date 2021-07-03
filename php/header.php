<header class="w3-container w3-teal">
    <h2 style="text-align:center;">
        <span class="w3-left w3-margin-bottom">
            <img src="./img/logo_scuola.png" alt="logo scuola" style="height:50px;width:50px;" />
        </span>
        <?php
            $conn = new mysqli(DB_Credentials::getHost(), DB_Credentials::getUsername(), DB_Credentials::getPassword(), DB_Credentials::getDBname());
            // Check connection
            if ($conn->connect_error) {
                echo "Elezioni";
                logError("Impossibile caricare nome votazione. Nessuna connessione al database.","null");
            }
            
            $sql = "SELECT nome_votazione FROM informazioni";
            $result = $conn->query($sql);
            if ($result->num_rows <= 0) {
                echo "Elezioni";
                logError("Impossibile caricare nome votazione. Nessun nome nel database.","null");
            }
            else{
                $dati = $result->fetch_assoc();
                echo $dati['nome_votazione'];
            }
        ?>
        <span class="w3-right w3-large w3-section" id="Orario"></span>
    </h2>
    <script> 
        var update = function () {
            date = new Date();
            let min = date.getMinutes();
            let sec = date.getSeconds();
            if (min<10) {
                min = '0'+min;
            }/*
            if (sec<10) {
                sec = '0'+sec;
            }*/
            document.getElementById('Orario').innerHTML = date.getHours() + ":" + min/*+ ":" + sec*/;
        };
    </script>
</header>