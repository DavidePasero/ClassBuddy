<?php
    // Questo script è chiamato da chat.js tramite fetch per recuperare i messaggi dopo un certo timestamp (il timestamp dell'ultimo messaggio ricevuto dal client).
    
    session_start ();
    require 'db.php';
    $db = connect_to_db ();
    if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
        header("Location: ../pages/login_form.php");
        exit();
    }

    if (!isset($_POST["recipient"])) {
        header ("Location: ../pages/error.php?error_type=missing_recipient");
    }

    $messages = prepared_query($db, 
        "SELECT mittente, testo, timestamp FROM S5204959.messaggio WHERE (mittente = ? AND destinatario = ?) AND timestamp > ? ORDER BY timestamp ASC",
        [$_POST["recipient"], $_SESSION["email"], $_POST["timestamp"]])->fetch_all (MYSQLI_ASSOC);
    
    echo json_encode($messages);
?>