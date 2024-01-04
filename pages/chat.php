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

    $get_user = "";

    if (isset($_GET["recipient"]))
        $get_user = $_GET["recipient"];

    $this_user = select_user_email ($db, $_SESSION['email']);

    // Query that retrieves the last message in all the conversations the user is involved in
    $conversations = prepared_query($db,
    "SELECT m1.mittente, m1.destinatario, m1.testo, m1.timestamp
    FROM messaggio m1
    INNER JOIN (
        SELECT
            LEAST(mittente, destinatario) AS user1,
            GREATEST(mittente, destinatario) AS user2,
            MAX(timestamp) AS latest_timestamp
        FROM messaggio
        WHERE mittente = ? OR destinatario = ?
        GROUP BY user1, user2
    ) m2 ON
        (m1.mittente = m2.user1 AND m1.destinatario = m2.user2 AND m1.timestamp = m2.latest_timestamp)
        OR
        (m1.mittente = m2.user2 AND m1.destinatario = m2.user1 AND m1.timestamp = m2.latest_timestamp)
    ORDER BY m1.timestamp DESC;",
    [$_SESSION['email'], $_SESSION['email']]
    )->fetch_all(MYSQLI_ASSOC);
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
    <main id="page-content">
        <div id="sidebar">
            <?php foreach ($conversations as $conv):
                $recipient = ($conv['mittente'] == $_SESSION['email']) ? $conv['destinatario'] : $conv['mittente'];
                $user = select_user_email($db, $recipient);
                $dataUri = "";
                if ($user["propic"] !== NULL) {
                    // Create a data URI for the image
                    $imageData = base64_encode($user["propic"]);
                    $imageType = $user["propic_type"];
                    $dataUri = "data:image/{$imageType};base64,{$imageData}";
                } else {
                    $dataUri = "../img/defaultUser.jpg";
                }
                ?>
                <div class="user-item" data-recipient="<?php echo $user['email']; ?>">
                    <div>
                        <img class="profile-pic" src="<?php echo $dataUri?>" alt="Profile Picture">
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></div>
                        <div class="last-message">
                            <?php echo strlen($conv["testo"]) < 13 ? $conv["testo"] : substr($conv["testo"], 0, 12) . "..."; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div id="no-recipient-selected" <?php if (!empty($get_user)) echo "hidden"?>>
            <img src="../res/icons/chat.svg" alt="No recipient selected">
            <p>Seleziona un utente per iniziare a scrivere</p>
        </div>

        <div id="chat" <?php if (empty($get_user)) echo "hidden"?>>
            <div id="chat-user-info">
                <img id="chat-propic" src="<?php if (!empty($get_user)) echo $get_user["propic"]?>" alt="Profile picture">
                <h4 id="chat-username"><?php if (!empty($get_user)) echo $get_user["firstname"] . " " . $get_user["lastname"]?></h4>
                <div id="search-container">
                    <button id="search-button"></button>
                    <div id="search-box-container">
                        <input type="search" id="search-box" placeholder="Scrivi la tua ricerca">
                        <button id="send-search" class="submit">Cerca</button>
                    </div>
                </div>
            </div>
            <div id="chat-container">
                <div id="chat-messages"></div>
            </div>

            <form id="scrivi_messaggio" method="post" action="../backend/send_message.php">
                <input type="text" id="message" name="message" placeholder="Scrivi un messaggio..." required>
                <!-- Uso il valore del sender nel js-->
                <input type="hidden" name="sender" id="sender" value="<?php $this_user["email"]?>">
                <input type="hidden" name="recipient" id="recipient" value="<?php echo $get_user?>">
                <button type="submit" id="send-button" class="submit">Invia</button>
            </form>
        </div>
    </main>
    <?php echo print_footer();?>
</body>
</html>
