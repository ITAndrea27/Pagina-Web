<?php
//PER VISUALIZZARE GLI ERRORI IN MODO DETTAGLIATO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include "/Classi/Utente.php";

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $db = connectToDatabase();

    if (isset($_POST["login_button"])) { //PARTE LOGIN

        // Effettua il login
        // Valori ricevuti dal form
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Esegui la query per recuperare l'utente dal database
        $stmt = $db->prepare("SELECT * FROM P_Utente WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $risultato = $stmt->get_result();

        if ($risultato->num_rows == 1) {

            // Utente trovato, verifica la password
            $row = $risultato->fetch_assoc();

            if (password_verify($password, $row["password"])) {

                // Password corretta, effettua l'accesso
                header("Location: nuova_pagina.php");
                exit;

            } else {

                // Password errata
                echo "Password errata.";
            }
        } else {

            // Utente non trovato
            
            echo "Utente non trovato.";
        }

        $stmt->close();

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

            header("Location: nuova_pagina.php"); // Reindirizza dopo la registrazione
            
            exit;
        } else {
            echo "Errore durante la registrazione dell'utente: " . $stmt->error;
        }
        $stmt->close();

    } else {
        header("Location: login.html");
        exit;
    }

    $db->close();

} else {
    header("Location: login.html");
    exit;
}
?>
