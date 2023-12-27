<?php
    // This script is called by chat.js via fetch call to retrieve the messages from recipient
    // after a certain timestamp (the timestamp of the last message received by the client).

    // Check if the user is authenticated
    session_start ();
    require 'db.php';
    $db = connect_to_db ();
    if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
        header("Location: ../pages/login_form.php");
        exit();
    }

    // Check if the recipient is specified in the GET parameters
    if (!isset($_POST["recipient"])) {
        header ("Location: ../pages/error.php?error_type=missing_recipient");
    }

    $messages = prepared_query($db, 
        "SELECT mittente, testo, timestamp FROM S5204959.messaggio WHERE (mittente = ? AND destinatario = ?) AND timestamp > ? ORDER BY timestamp ASC",
        [$_POST["recipient"], $_SESSION["email"], $_POST["timestamp"]])->fetch_all (MYSQLI_ASSOC);
    
    echo json_encode($messages);
?>