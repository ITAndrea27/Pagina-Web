<?php
// Funzione per la connessione al database (da personalizzare con i tuoi dati)
function connectToDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nome_database";

    // Creo la connessione
    $db = new mysqli($servername, $username, $password, $dbname);

    // Verifico la connessione
    if ($db->connect_error) {
        die("Connessione al database fallita: " . $db->connect_error);
    }

    return $db;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera i dati inviati dal modulo
    $username_or_email = $_POST["username_or_email"];
    $password = $_POST["password"];

    // Verifica quale pulsante è stato premuto
    if (isset($_POST["login_button"])) {
        // Reindirizzamento dell'utente a una nuova pagina dopo il login
        header("Location: nuova_pagina.php");
        exit;
    } else if (isset($_POST["register_button"])) {
        // Salvataggio utente nel database
        $db = connectToDatabase();

        //Codice per salvare l'utente nel database
        $sql = "INSERT INTO utenti (username_or_email, password) VALUES ('$username_or_email', '$password')";
        if ($db->query($sql) === TRUE) {
            echo "Utente salvato con successo!";
        } else {
            echo "Errore durante il salvataggio dell'utente: " . $db->error;
        }

        $db->close();
    } else {
        // In caso di provenienza sconosciuta, reindirizza alla pagina di login
        header("Location: login.html");
        exit;
    }
} else {
    // Se non ci sono dati inviati tramite POST, reindirizza alla pagina di login
    header("Location: login.html");
    exit;
}
?>
