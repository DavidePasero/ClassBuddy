<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require __DIR__ . '/../backend/page.php';
    require __DIR__ . '/../backend/db.php';
    require __DIR__ . '/../backend/utils.php';
    
    $db = connect_to_db ();
    cookie_check ($db);

    if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
        header("Location: login_form.php");
        exit();
    }

    $get_user = null;

    // Controlla se c'è un destinatario passato come GET, se è valido bene altrimenti redirect a chat.php
    if (isset($_GET["recipient"])) {
        $get_user = select_user_email ($db, $_GET["recipient"]);
        if (!$get_user or $get_user["email"] == $_SESSION["email"] or $get_user["role"] == $_SESSION["role"]) {
            header("Location: chat.php");
            exit();
        }
        else
            $get_user ["propic"] = get_data_uri ($get_user["propic"], $get_user["propic_type"]);
    }

    $this_user = select_user_email ($db, $_SESSION['email']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>ChatBuddy</title>
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/chat.css">
    <script type="module" src="../scripts/chat.js"></script>
</head>
<body>
    <?php echo print_header();?>
    <main id="page-content">
        <div id="sidebar">
        </div>
        
        <div id="no-recipient-selected" <?php if (!is_null($get_user)) echo "hidden"?>>
            <img src="../res/icons/chat.svg" alt="Nessun destinatario selezionato">
            <p>Seleziona un utente per iniziare a scrivere</p>
        </div>

        <div id="chat" <?php if (is_null($get_user)) echo "hidden"?>>
            <div id="chat-user-info">
                <a id="profile-link" href="profile.php?email=<?php if (!empty($get_user)) echo $get_user["email"]?>">
                    <img id="chat-propic" src="<?php if (!empty($get_user)) echo $get_user["propic"]?>" alt="Foto profilo">
                </a>
                <h4 id="chat-username"><?php if (!empty($get_user)) echo htmlentities ($get_user["firstname"] . " " . $get_user["lastname"])?></h4>
                <div id="search-container">
                    <button id="search-button" class="btn only-icon-button">
                        <img src="../res/icons/search.svg" alt="Search">
                    </button>
                    <div id="search-box-container">
                        <input type="search" id="search-box" placeholder="Scrivi la tua ricerca">
                        <button id="send-search" class="btn submit">Cerca</button>
                    </div>
                </div>
            </div>
            <div id="chat-container">
                <div id="chat-messages"></div>
            </div>

            <form id="scrivi_messaggio" method="post" action="../backend/send_message.php">
                <input type="text" id="message" name="message" placeholder="Scrivi un messaggio..." required>
                <!-- Uso il valore del sender nel js-->
                <input type="hidden" name="recipient" id="recipient" value="<?php echo isset($_GET["recipient"]) ? $_GET["recipient"] : ""?>">
                <button type="submit" id="send-button" class="btn submit">Invia</button>
            </form>
        </div>
    </main>
    <?php echo print_footer();?>
</body>
</html>
