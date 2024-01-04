<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    require __DIR__ . '/../backend/page.php';
    require __DIR__ . '/../backend/db.php';
    require __DIR__ . '/../backend/review.php';
    
    $db = connect_to_db ();
    cookie_check ($db);
    
    if (!isset ($_SESSION ["authenticated"])) {
        $db->close ();
        header ("Location: login_form.php");
    }

    // Se sto visualizzando il mio profilo, allora $user_profile = $_SESSION ["email"], altrimenti $user_profile = $_GET ["email"]
    $user_profile = isset($_GET["email"]) ? $_GET["email"] : $_SESSION["email"];
    $user_profile_info = select_user_email($db, $user_profile);

    if ($user_profile_info["propic"] !== NULL) {
        // Create a data URI for the image
        $imageData = base64_encode($user_profile_info["propic"]);
        $imageType = $user_profile_info["propic_type"];
        $dataUri = "data:image/{$imageType};base64,{$imageData}";
    } else {
        $dataUri = "../img/defaultUser.jpg";
    }

    $myprofile = $user_profile_info["email"] === $_SESSION["email"];

    // Aggiunta: ottieni la media delle recensioni se l'utente è un tutor
    $averageRating = ($user_profile_info["role"] === "tutor") ? getAverageRating($db, $user_profile_info["email"]) : null;
?>
<html lang="it">
<head>
    <title>ClassBuddy</title>
    <link rel="icon" href="img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/home.css">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/profile.css">
    <link rel="stylesheet" type="text/css" href="../style/modify_profile.css">
    <?php if ($myprofile)
        echo "<script type=\"module\" src=\"../scripts/modify_profile.js\" defer></script>";
    ?>
    <meta charset="utf-8">
</head>
<body>
    <?php echo print_header();?>
    <main>
    <div id="contact-card">
            <p><?php echo htmlentities($user_profile_info["firstname"] . " " . $user_profile_info["lastname"])?></p>

            <!-- Aggiunta: mostra la media delle recensioni -->
            <?php if ($user_profile_info["role"] === "tutor" && $averageRating !== null) : ?>
                <p>Media recensioni: <?php echo number_format($averageRating, 2); ?></p>
                <a href="../pages/tutor_reviews.php?tutor_email=<?php echo $user_profile_info["email"]; ?>" class="button">Visualizza tutte le recensioni</a>
            <?php endif; ?>

            <form id="form" action="../backend/modify_profile.php" method="POST" name="modify_profile" enctype="multipart/form-data">
                <div id="image_div">
                    <img id="image-preview" src=<?php echo $dataUri;?> alt="Profile picture">
                    <?php
                        if ($myprofile) {
                            echo <<<MODIFY_PIC
                                <div id="edit-button"></div>
                                <input type="file" id="propic" name="propic" accept="image/*">
                            MODIFY_PIC;
                        }
                    ?>
                </div>
                
                <?php
                    if ($_SESSION ["role"] === "tutor" and $myprofile) {
                        echo <<<INSEGNAMENTI
                                <div id="insegnamenti_container">
                                    <ul id="insegnamenti_list">
                        INSEGNAMENTI;
                            // Recupero gli insegnamenti del tutor
                            $insegnamenti = prepared_query (
                                $db,
                                "SELECT materia, tariffa FROM S5204959.insegnamento WHERE tutor=?;",
                                [$user_profile_info ["email"]]
                            )->fetch_all (MYSQLI_ASSOC);

                            foreach ($insegnamenti as $insegnamento) {
                                /*! Molto importante che il primo elemento di <li class="insegnamento" sia
                                la span che contiene la materia, se si cambia qusto, bisogna cambiare anche il js
                                nella gestione delle eliminazioni*/
                                echo <<<INSEGNAMENTI_PRESENTI
                                        <li class="insegnamento">
                                            <span name="materia[]">{$insegnamento ["materia"]}</span>
                                            <span>{$insegnamento ["tariffa"]}€/ora</span>
                                            <button type="button" class="remove_insegnamento"></button>
                                        </li>
                                    INSEGNAMENTI_PRESENTI;
                            }
                        echo <<<INSEGNAMENTI
                                    </ul>
                                    <label for="add_insegnamento">Aggiungi insegnamento</label>
                                    <button id="add_insegnamento" type="button"></button>
                                </div>
                        INSEGNAMENTI;
                    }
                ?>

                <?php 
                    if ($myprofile) {
                        echo <<<SUBMIT_FORM
                            <div id="submit_div">
                                <input type="submit" id="submit_button" name="Submit" value="Salva modifiche">
                            </div>
                            SUBMIT_FORM;
                    }?>
            </form>

            <!-- Chat button that calls chat.php with the recipient's email as GET parameter only if
                recipient is a tutor and I am a student-->
            <?php
                if ($user_profile_info ["role"] === "tutor" and $_SESSION ["role"] === "studente") {
                    echo <<<CHAT_BUTTON
                        <form action="chat.php" method="GET">
                            <input type="hidden" name="recipient" value="{$user_profile_info ["email"]}">
                            <label for="chat_button">Contatta</label>
                            <input id="chat_button" type="submit" value="">
                        </form>
                    CHAT_BUTTON;
                }
                
            ?>

            <?php
            // Verifica se lo studente può scrivere una recensione
            if ($_SESSION["role"] === "studente" && hasSentMessageToStudent($db, $user_profile_info["email"], $_SESSION["email"])) {
                // Verifica se lo studente ha già inviato una recensione
                $review = getStudentReview($db, $user_profile_info["email"], $_SESSION["email"]);

                if ($review) {
                    // Lo studente ha già inviato una recensione, quindi mostra la recensione invece del modulo
                    echo "<p>Recensione già inviata:</p>";
                    echo "<p>Valutazione: {$review['valutaz']}</p>";
                    echo "<p>Commento: {$review['commento']}</p>";
                } else {
                    // Lo studente non ha ancora inviato una recensione, mostra il modulo di invio
                    echo <<<REVIEW_FORM
                        <form action="../backend/submit_review.php" method="POST">
                            <label for="valutaz">Valutazione (da 1 a 5):</label>
                            <input type="number" name="valutaz" min="1" max="5" required>
                            <label for="commento">Commento:</label>
                            <textarea name="commento" required></textarea>
                            <input type="hidden" name="tutor" value="{$user_profile_info["email"]}">
                            <input type="hidden" name="studente" value="{$_SESSION["email"]}">
                            <input type="submit" value="Invia Recensione">
                        </form>
                    REVIEW_FORM;
                }
            }
            ?>
        </div>
    </main>
    <?php echo print_footer();?>
</body>
</html>