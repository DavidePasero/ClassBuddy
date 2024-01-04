<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "db.php";
$db = connect_to_db();

// ! Verifica che l'utente sia autenticato

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Controlla che tutti i dati siano stati inviati
    if (isset($_POST["valutaz"]) && isset($_POST["commento"]) && isset($_POST["tutor"]) && isset($_POST["studente"])) {
        $valutaz = $_POST["valutaz"];
        $commento = $_POST["commento"];
        $tutorEmail = $_POST["tutor"];
        $studenteEmail = $_POST["studente"];

        // Esegui la query per inserire la recensione nel database
        $query = "INSERT INTO recensione (studente, tutor, valutaz, commento) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssis", $studenteEmail, $tutorEmail, $valutaz, $commento);

        if ($stmt->execute()) {
            echo "<script src='../scripts/loadReview.js'></script>";
        } else {
            echo "Errore durante l'invio della recensione.";
        }

        $stmt->close();
    } else {
        echo "Tutti i campi devono essere compilati.";
    }
} else {
    echo "Metodo non valido per l'accesso a questa pagina.";
}
?>