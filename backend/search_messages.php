<?php
    // This script is called by chat.js via fetch call to retrieve the messages from recipient
    // where the text contains a certain string (the string searched by the client).

    // Check if the user is authenticated
    session_start ();
    require 'db.php';
    $db = connect_to_db ();
    if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
        header("Location: ../pages/login_form.php");
        exit();
    }

    // Check if the recipient is specified in the GET parameters
    if (!isset($_POST["recipient"]) or !isset($_POST["ricerca"])) {
        header ("Location: ../pages/error.php?error_type=missing_recipient");
    }

    $messages = prepared_query($db, 
        "SELECT mittente, testo, timestamp FROM S5204959.messaggio WHERE ((mittente = ? AND destinatario = ?) OR (mittente = ? AND destinatario = ?)) AND testo LIKE ? ORDER BY timestamp ASC",
        [$_POST["recipient"], $_SESSION["email"], $_SESSION["email"], $_POST["recipient"], "%".$_POST["ricerca"]."%"])->fetch_all (MYSQLI_ASSOC);
    
    echo json_encode($messages);
?>