<?php
    // fetch_chat.php

    require 'db.php';

    $db = connect_to_db();
    session_start();

    // Check if the user is authenticated
    if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }

    // Check if the recipient is specified in the GET parameters
    if (!isset($_POST["recipient"])) {
        header("HTTP/1.1 400 Bad Request");
        exit();
    }

    $recipient = $_POST["recipient"];
    $sender = $_SESSION["email"];

    // Retrieve all messages between the sender and the recipient from the database
    $chatMessages = prepared_query($db,
        "SELECT mittente, testo FROM S5204959.messaggio WHERE (mittente = ? AND destinatario = ?) OR (mittente = ? AND destinatario = ?) ORDER BY timestamp ASC",
        [$sender, $recipient, $recipient, $sender])->fetch_all(MYSQLI_ASSOC);

    // Return the chat messages as JSON
    echo json_encode($chatMessages);
?>