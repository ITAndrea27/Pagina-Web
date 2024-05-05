<?php
//PER VISUALIZZARE GLI ERRORI IN MODO DETTAGLIATO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Funzione per la connessione al database
function connectToDatabase()
{
    $username = "marianiandrea";
    $password = "";
    $server = "localhost";
    $dbserver = "my_marianiandrea";

    $db = new mysqli($server, $username, $password, $dbserver);

    if ($db->connect_error) {
        die("Connessione al database fallita: " . $db->connect_error);
    }

    return $db;
}

function GetIdUtente($email) {

    $db = connectToDatabase(); 
    // RICAVO ID DELL'UTENTE
    $stmt = $db->prepare("SELECT codice FROM P_Utente WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

     // Verifica se la query ha restituito dei risultati
    if ($result->num_rows > 0) {
        //estrazione il codice dell'utente dalla riga risultante
        $row = $result->fetch_assoc();
        return $row['codice'];
    }

    return null;
}

function GetSerialeVeicolo()
{

}

function UtenteExist($codiceUtente)
{
    // Verifica se l'utente esiste nella tabella degli utenti
    $db = connectToDatabase();
    $stmt = $db->prepare("SELECT * FROM P_Utente WHERE codice = ?");
    $stmt->bind_param("i", $codiceUtente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "L'utente non esiste.";
        exit;
    }
}

// Inizio della gestione della richiesta POST
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $db = connectToDatabase();

    if (isset($_POST["login_button"])) //PARTE LOGIN
    { 
        // Valori ricevuti dal form
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Esegui la query per recuperare l'utente dal database
        $stmt = $db->prepare("SELECT * FROM P_Utente WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $risultato = $stmt->get_result();

        $codiceUtente = GetIdUtente($email); // Passa l'email corrente

        if ($risultato->num_rows === 1) 
        {
            // Utente trovato, verifica la password
            $row = $risultato->fetch_array(MYSQLI_ASSOC);

            if (password_verify($password, $row["password"])) { // Password corretta, effettua l'accesso

                

                // Visualizza i veicoli disponibili
                $sql_veicoli = $db->query("SELECT * FROM P_Veicolo WHERE P_Veicolo.seriale NOT IN 
                (SELECT P_Noleggio.fk_auto FROM P_Noleggio)");
                
                if ($sql_veicoli->num_rows > 0) 
                {
                    echo "<br><h1>Veicoli Noleggiabili</h1>";
                    echo "<table border='1'>";
                    echo "<tr>
                            <th>Seriale</th>
                            <th>Tipo</th>
                            <th>Potenza</th>
                            <th>Consumo</th>
                            <th>CO2</th>
                            <th>Costo</th>
                            <th>Foto</th>
                        </tr>";
                    // Output dei dati di ogni riga
                    while ($row = $sql_veicoli->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["seriale"] . "</td>";
                        echo "<td>" . $row["tipo"] . "</td>";
                        echo "<td>" . $row["potenza"] . "</td>";
                        echo "<td>" . $row["consumo"] . "</td>";
                        echo "<td>" . $row["Co2"] . "</td>";
                        echo "<td>" . $row["costo"] . "</td>";
                        echo "<td>" . $row["foto"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    $stmt->close();
                    $db->close();

                    // Gestione dell'aggiunta del noleggio
                    echo "<div id='noleggio_form'>
                            <h3>VEICOLI DISPONIBILI AL NOLEGGIO</h3>";
                    echo "  <form method='get'>";
                    echo "      <p>Scegli il veicolo da noleggiare inserendo il seriale</p>";
                    echo "      <input type='text' id='tb_seriale' name='tb_seriale'/><br>";
                    echo "      <input type='hidden' name='user_id' value='" . $codiceUtente . "'>";
                    echo "      <input type='submit' value='Noleggia'></input>";
                    echo "  </form>";
                    echo "</div>";

                    //PARTE DI VISUALIZZAZIONE DEL ACCOUNT
                    echo "<div id='account_form'>
                            <h3>ACCEDI ALLA TUA AREA PERSONALE</h3>";
                    echo "  <form method='get'>";
                    echo "      <input type='hidden' name='user_id' value='" . $codiceUtente . "'>";
                    echo "      <input type='submit' value='Area Personale'></input>";
                    echo "  </form>";
                    echo "</div>";

                } else {
                    echo "Nessun veicolo disponibile.";
                }
                
                // Chiusura del risultato della query


            } else {

                // Password errata
                echo "Password errata.";
            }
        } else {

            // Utente non trovato
            echo "Utente non trovato.";
        }

        exit;

    } elseif (isset($_POST["register_button"])) { //PARTE REGISTRAZIONE  

        // Acquisizione dei valori dal modulo di registrazione
        $nome = $_POST["first_name"];
        $cognome = $_POST["last_name"];
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
    
        // Hash della password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Connessione al database
        $db = connectToDatabase();
    
        // Preparazione della query per l'inserimento dei dati dell'utente
        $stmt = $db->prepare("INSERT INTO P_Utente (nome, cognome, username, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $cognome, $username, $email, $hashed_password);
    
        // Esecuzione della query
        if ($stmt->execute()) {
            echo "Utente registrato con successo!";
            header("Location: accedi.html"); // Reindirizzamento dopo la registrazione
            exit;
        } else {
            echo "Errore durante la registrazione dell'utente: " . $stmt->error;
        }
    
        // Chiusura della query e della connessione al database
        $stmt->close();
        $db->close();
    } else {
        header("Location: accedi.html");
        exit;
    }
    

} else if(isset($_GET["tb_seriale"])) { // X NOLEGGIO

    if(isset($_GET["tb_seriale"]) && !empty($_GET["tb_seriale"]) && 
    isset($_GET["user_id"]) && !empty($_GET["user_id"])) 
    {
        $db = connectToDatabase();
    
        // RICAVO ID DELL'VEICOLO
        $stmt = $db->prepare("SELECT seriale FROM P_Veicolo WHERE seriale = ?");
        $stmt->bind_param("s", $_GET["tb_seriale"]);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Verifica se la query ha restituito dei risultati
        if ($result->num_rows > 0) {
            //estrazione il codice dell'utente dalla riga risultante
            $row = $result->fetch_assoc();
            $codiceVeicolo = $row['seriale'];
            $codiceUtente = $_GET["user_id"];
            $currentDate = date("Y-m-d");
            UtenteExist($codiceUtente);

            //AGGIUNGO NOLEGGIO
            $stmt = $db->prepare("INSERT INTO P_Noleggio(fk_utente, fk_auto, data_noleggio) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $codiceUtente, $codiceVeicolo, $currentDate);
            
            if (!$stmt->execute()) {
                echo "Errore durante l'esecuzione della query: " . $stmt->error;
            }

            $stmt->close();
            
            // Creazione della fattura
            $stmt_fattura = $db->prepare("INSERT INTO P_Fattura (data_fattura, fk_utente, fk_veicolo) VALUES (?, ?, ?)");
            $stmt_fattura->bind_param("sii", $currentDate, $codiceUtente, $codiceVeicolo);

            if ($stmt_fattura->execute()) {
                echo "Fattura creata con successo!";
                
                // Recupero del codice della fattura appena inserita
                $codiceFattura = $stmt_fattura->insert_id;

                // Recupero e visualizzazione dei dettagli della fattura
                $stmt_dettagli_fattura = $db->prepare("SELECT * FROM P_Fattura WHERE codice = ?");
                $stmt_dettagli_fattura->bind_param("i", $codiceFattura);
                $stmt_dettagli_fattura->execute();
                $result_dettagli_fattura = $stmt_dettagli_fattura->get_result();

                if ($result_dettagli_fattura->num_rows > 0) {
                    $row_fattura = $result_dettagli_fattura->fetch_assoc();

                    echo "<br>Dati della Fattura:<br>";
                    echo "Codice Fattura: " . $row_fattura['codice'] . "<br>";
                    echo "Data Fattura: " . $row_fattura['data_fattura'] . "<br>";
                    echo "Codice Utente: " . $row_fattura['fk_utente'] . "<br>";
                    echo "Codice Veicolo: " . $row_fattura['fk_veicolo'] . "<br>";
                } else {
                    echo "Nessuna fattura trovata.";
                }

                $stmt_dettagli_fattura->close();
            } else {
                echo "Errore durante la creazione della fattura: " . $stmt_fattura->error;
            }


            $stmt_fattura->close();
            $db->close();
            
            echo "<br>NOLEGGIO EFFETTUATO CON SUCCESSO:<br>
            Codice utente: " . $codiceUtente . "<br>";
            echo "Codice veicolo: " . $codiceVeicolo . "<br>";
            echo "Data noleggio: " . $currentDate . "<br>";
            
            
        } else {
            echo "Veicolo non trovato.";
        }
    }
    exit;

} else if(isset($_GET["user_id"])) { //AREA PERSONALE

    if(isset($_GET["user_id"]) && !empty($_GET["user_id"])) 
    {
        $db = connectToDatabase();

        // Query per recuperare i veicoli noleggiati dall'utente con il codice specificato
        $stmt_noleggi = $db->prepare("SELECT P_Noleggio.codice, P_Noleggio.data_noleggio, P_Veicolo.tipo, 
                                      P_Veicolo.seriale, P_Veicolo.costo, P_Fattura.codice AS codice_fattura, 
                                      P_Fattura.data_fattura
                                      FROM P_Noleggio
                                      JOIN P_Veicolo ON P_Noleggio.fk_auto = P_Veicolo.seriale
                                      JOIN P_Fattura ON P_Noleggio.fk_utente = P_Fattura.fk_utente
                                                    AND P_Noleggio.fk_auto = P_Fattura.fk_veicolo
                                      WHERE P_Noleggio.fk_utente = ?");
        $stmt_noleggi->bind_param("i", $_GET["user_id"]);
        $stmt_noleggi->execute();
        $result_noleggi = $stmt_noleggi->get_result();

        if ($result_noleggi->num_rows > 0) {
            echo "<h2>Area Personale - Noleggi Effettuati</h2>";
            echo "<table border='1'>";
            echo "<tr>
                    <th>Codice Noleggio</th>
                    <th>Data Noleggio</th>
                    <th>Tipo Veicolo</th>
                    <th>Seriale Veicolo</th>
                    <th>Costo Veicolo</th>
                    <th>Codice Fattura</th>
                    <th>Data Fattura</th>
                </tr>";

            $costo_totale = 0; // Inizializzazione del costo totale

            while ($row_noleggio = $result_noleggi->fetch_assoc()) {
                $costo_totale += $row_noleggio['costo']; // Aggiunta del costo del veicolo al costo totale

                echo "<tr>";
                echo "<td>" . $row_noleggio['codice'] . "</td>";
                echo "<td>" . $row_noleggio['data_noleggio'] . "</td>";
                echo "<td>" . $row_noleggio['tipo'] . "</td>";
                echo "<td>" . $row_noleggio['seriale'] . "</td>";
                echo "<td>" . $row_noleggio['costo'] . "</td>";
                echo "<td>" . $row_noleggio['codice_fattura'] . "</td>";
                echo "<td>" . $row_noleggio['data_fattura'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";

            // Visualizzazione del costo totale dei noleggi
            echo "<p>Costo totale dei noleggi: " . $costo_totale . "</p>";
        } else {
            echo "<p>Nessun noleggio trovato.</p>";
        }

        $stmt_noleggi->close();
    }
}






?>
