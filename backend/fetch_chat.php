<?php
    require 'db.php';

    $db = connect_to_db();
    session_start();

    if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }

    if (!isset($_POST["recipient"])) {
        header("HTTP/1.1 400 Bad Request");
        exit();
    }

    $recipient = $_POST["recipient"];
    $sender = $_SESSION["email"];

    // Recupera tutti i messaggi tra mittente e destinatario dal database
    $chatMessages = prepared_query($db,
        "SELECT mittente, testo FROM S5204959.messaggio WHERE (mittente = ? AND destinatario = ?) OR (mittente = ? AND destinatario = ?) ORDER BY timestamp ASC",
        [$sender, $recipient, $recipient, $sender])->fetch_all(MYSQLI_ASSOC);

    echo json_encode($chatMessages);
?>