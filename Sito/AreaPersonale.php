<?php
session_start(); // Avvia la sessione se non è già stata avviata

// Verifica se l'utente è loggato
// if (!isset($_SESSION['username'])) {
//     // Se l'utente non è loggato, reindirizzalo alla pagina di login o alla pagina di registrazione
//     header("Location: accedi.html"); // Modifica "login.php" con la tua pagina di login
//     exit;
// }

// Includi il file di connessione al database
//include_once 'connessione.php'; // Modifica "connessione.php" con il nome del tuo file di connessione al database

// Recupera informazioni sull'utente dal database
$username = $_SESSION['username'];
$query = "SELECT * FROM utenti WHERE username = '$username'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Recupera i veicoli acquistati dall'utente dal database
$query_veicoli = "SELECT * FROM veicoli WHERE id_utente = {$row['id']}"; // Supponendo che ci sia un campo "id_utente" nella tabella veicoli
$result_veicoli = mysqli_query($conn, $query_veicoli);

// Recupera i noleggi disponibili dal database (esempio)
$query_noleggi = "SELECT * FROM noleggi WHERE disponibile = 1"; // Supponendo che ci sia un campo "disponibile" nella tabella noleggi
$result_noleggi = mysqli_query($conn, $query_noleggi);

// Recupera le statistiche (esempio)
// Puoi calcolare le statistiche in base ai dati nel database
$totale_veicoli = mysqli_num_rows($result_veicoli);
$totale_noleggi_disponibili = mysqli_num_rows($result_noleggi);
// Altre statistiche che potresti voler visualizzare

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Personale</title>
</head>
<body>
    <h1>Benvenuto, <?php echo $row['nome']; ?>!</h1>
    
    <h2>Veicoli Acquistati:</h2>
    <ul>
        <?php while ($row_veicolo = mysqli_fetch_assoc($result_veicoli)): ?>
            <li><?php echo $row_veicolo['marca'] . ' ' . $row_veicolo['modello']; ?></li>
        <?php endwhile; ?>
    </ul>
    
    <h2>Noleggi Disponibili:</h2>
    <ul>
        <?php while ($row_noleggio = mysqli_fetch_assoc($result_noleggi)): ?>
            <li><?php echo $row_noleggio['marca'] . ' ' . $row_noleggio['modello']; ?></li>
        <?php endwhile; ?>
    </ul>
    
    <h2>Statistiche:</h2>
    <p>Totale veicoli acquistati: <?php echo $totale_veicoli; ?></p>
    <p>Totale noleggi disponibili: <?php echo $totale_noleggi_disponibili; ?></p>
    <!-- Altre statistiche -->

    <!-- Link per il logout -->
    <a href="logout.php">Logout</a> <!-- Modifica "logout.php" con il nome del tuo script di logout -->
</body>
</html>

<?php
// Chiudi la connessione al database
mysqli_close($conn);
?>
