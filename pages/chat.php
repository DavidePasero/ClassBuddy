<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require __DIR__ . '/../backend/page.php';
    require __DIR__ . '/../backend/db.php';
    
    $db = connect_to_db ();
    cookie_check ($db);

    // Check if the user is authenticated
    if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
        header("Location: login_form.php");
        exit();
    }

    // Check if the recipient is specified in the GET parameters
    if (!isset($_GET["recipient"])) {
        header ("Location: error.php?error_type=missing_recipient");
    }

    // Check if the chat is valid (Tutor to Student or Student to Tutor)
    $recipient_role = select_user_email ($db, $_GET ["recipient"]) ["role"];

	// Check if the recipient has the same role as the sender
	if ($recipient_role === $_SESSION ["role"]) {
		header ("Location: ../pages/error.php?error_type=invalid_chat");
	}

    $recipient = $_GET["recipient"];
    $recipient_info = select_user_email ($db, $recipient);
    $sender = $_SESSION["email"];

    // Retrieve all messages between the sender and the recipient from databse
    $chatMessages = prepared_query($db, 
        "SELECT mittente, testo FROM S5204959.messaggio WHERE (mittente = ? AND destinatario = ?) OR (mittente = ? AND destinatario = ?) ORDER BY timestamp ASC",
        [$sender, $recipient, $recipient, $sender])->fetch_all (MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatBuddy</title>
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/chat.css">
    <script src="../scripts/chat.js" defer></script>
</head>
<body>
    <?php echo print_header();?>
    <div id="page_title">Chat con <?php echo $recipient_info["firstname"] . " " . $recipient_info ["lastname"]; ?></div>

    <div id="chat-container">
        <div id="search-container">
            <button id="search-button"></button>
            <div id="search-box-container">
                <input type="search" id="search-box" placeholder="Scrivi la tua ricerca">
                <button id="send-search" class="submit">Cerca</button>
            </div>
        </div>
        <div id="chat-messages">
            <?php foreach ($chatMessages as $message): ?>
                <div class="message <?php echo ($message["mittente"] === $sender) ? 'sent' : 'received'; ?>">
                    <?php echo $message["testo"]; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <form id="scrivi_messaggio" method="post" action="../backend/send_message.php">
        <input type="text" id="message" name="message" placeholder="Scrivi un messaggio..." required>
        <!-- Uso il valore del sender nel js-->
        <input type="hidden" name="sender" id="sender" value=<?php echo $sender?>>
        <input type="hidden" name="recipient" id="recipient" value=<?php echo $recipient?>>
        <button type="submit" id="send-button" class="submit">Invia</button>
    </form>

    <?php echo print_footer();?>
</body>
</html>
