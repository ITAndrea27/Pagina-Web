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

function GetIdUtente() {

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

function Noleggio()
{


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

        if ($risultato->num_rows === 1) 
        {
            // Utente trovato, verifica la password
            $row = $risultato->fetch_array(MYSQLI_ASSOC);

            if (password_verify($password, $row["password"])) { // Password corretta, effettua l'accesso
                // Visualizza i veicoli disponibili
                $sql_veicoli = $db->query("SELECT * FROM P_Veicolo WHERE P_Veicolo.seriale NOT IN 
                (SELECT P_Noleggio.fk_auto FROM P_Noleggio)");
                
                if ($sql_veicoli->num_rows > 0) {
                    echo "<br><h1>Veicoli Disponibili</h1>";
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

                    // Gestione dell'aggiunta del noleggio
                    echo "<form method='GET'>Scegli il veicolo da noleggiare inserendo il seriale<br>";
                    echo "<input type='text' name='tb_seriale'/><br>";
                    echo "<input type='submit' name='bt_cerca' value='Cerca'/></form>";
                } else {
                    echo "Nessun veicolo disponibile.";
                }
                
                // Chiusura del risultato della query
                $risultato->close();
                echo "CIAO";

            } else {

                // Password errata
                echo "Password errata.";
            }
        } else {

            // Utente non trovato
            echo "Utente non trovato.";
        }

    } elseif (isset($_POST["register_button"])) { //PARTE REGISTRAZIONE  

        // Registrazione nuovo utente
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Hash della password -> cripta password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO P_Utente (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashed_password);

        if ($stmt->execute()) {
            echo "Utente registrato con successo!";
            header("Location: accedi.html"); // Reindirizza dopo la registrazione
            
        } else {
            echo "Errore durante la registrazione dell'utente: " . $stmt->error;
        }

    } else {
        header("Location: accedi.html");
        exit;
    }

    // Chiusura del database e delle istruzioni
    $stmt->close();
    $db->close();

} else {
    header("Location: accedi.html");
    exit;
}


if(isset($_GET["tb_cerca"]) && !empty($_GET["tb_seriale"]))
{
    if(isset($_GET["bt_cerca"]) && !empty($_GET["tb_seriale"]))
{
    $db = connectToDatabase();
    $codiceUtente = GetIdUtente($_POST["email"]); // Passa l'email corrente

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

        //AGGIUNGO NOLEGGIO
        $currentDate = date("Y-m-d");
        $stmt = $db->prepare("INSERT INTO P_Noleggio(fk_utente, fk_auto, data) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $codiceUtente, $codiceVeicolo, $currentDate);
        $stmt->execute();
        
        echo "Fatto";
    } else {
        echo "Veicolo non trovato.";
    }
}


}
?>
