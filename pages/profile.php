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
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/profile.css">
    <link rel="stylesheet" type="text/css" href="../style/modify_profile.css">
    <link rel="stylesheet" type="text/css" href="../style/stelline.css">
    <?php if ($myprofile)
        echo "<script src=\"../scripts/modify_profile.js\" defer></script>";
    ?>
    <meta charset="utf-8">
</head>
<body>
    <?php echo print_header();?>
    <main>
    <div id="contact-card">
            <p id="name"><?php echo htmlentities($user_profile_info["firstname"] . " " . $user_profile_info["lastname"])?></p>

            <!-- Aggiunta: mostra la media delle recensioni -->
            <?php 
            if ($user_profile_info["role"] === "tutor" && $averageRating !== null) {
                echo '<div class="rating" id="rating">';
                for ($i = 0; $i < 5; $i++) {
                    if ($i < round($averageRating))
                        echo '<span class="star active">&#9733;</span>';
                    else
                        echo '<span class="star">&#9734;</span>';
                }
                $number = number_format($averageRating, 1, ",");
                echo '<span class=text>' . $number . ' su 5</span>';
                echo '</div>';
            }
            ?>

            <form id="modify-profile" action="../backend/modify_profile.php" method="POST" name="modify_profile" enctype="multipart/form-data">
                <div id="image_div">
                    <img id="image-preview" src=<?php echo $dataUri;?> alt="Profile picture">
                    <?php
                        if ($myprofile) {
                            echo <<<MODIFY_PIC
                                <button id="edit-button" class="btn only-icon-button">
                                    <img src="../res/icons/edit.svg" alt="Edit icon">
                                </button>
                                <input type="file" id="propic" name="propic" accept="image/*">
                            MODIFY_PIC;
                        }
                    ?>
                </div>
                
                <?php
                    if ($user_profile_info ["role"] === "tutor") {
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
                                        INSEGNAMENTI_PRESENTI;
                                    if ($myprofile) {
                                        echo <<<REMOVE_INSEGNAMENTO
                                            <button type="button" class="remove_insegnamento btn only-icon-button">
                                                <img src="../res/icons/remove.svg" alt="Remove icon">
                                            </button>
                                        REMOVE_INSEGNAMENTO;
                                    }
                                    echo '</li>';
                            }
                        
                        echo "</ul>";
                        if ($myprofile) {
                            echo <<<ADD_INSEGNAMENTO
                                <button id="add_insegnamento" class="btn icon-button" type="button">
                                    Aggiungi insegnamento
                                    <img src="../res/icons/add.svg" alt="Add icon">
                                </button>
                            ADD_INSEGNAMENTO;
                        }
                        echo "</div>";
                    }
                ?>

                <?php 
                    if ($myprofile) {
                        echo <<<SUBMIT_FORM
                            <div id="submit_div">
                                <input class="btn submit" type="submit" id="submit_button" name="Submit" value="Salva modifiche">
                            </div>
                            SUBMIT_FORM;
                    }?>
            </form>

            <!-- Chat button that calls chat.php with the recipient's email as GET parameter only if
                recipient is a tutor and I am a student-->
            <div id="action-container">
                <?php
                    if ($user_profile_info ["role"] === "tutor" and $_SESSION ["role"] === "studente") {
                        echo <<<CHAT_BUTTON
                            <a href="chat.php?recipient={$user_profile_info["email"]}" id="chat_button" class="btn icon-button">
                                Contatta
                                <img src="../res/icons/mail.svg" alt="Chat icon">
                            </a>
                        CHAT_BUTTON;
                    }
                ?>
                <?php if ($user_profile_info["role"] === "tutor"): ?>
                <a href="tutor_reviews.php?tutor_email=<?php echo $user_profile_info["email"]?>" id="reviews_button" class="btn icon-button">
                    Recensioni
                    <img src="../res/icons/review.svg" alt="Chat icon">
                </a>
                <?php endif; ?>
            </div>

            <?php
            // Verifica se lo studente può scrivere una recensione
            /*if ($_SESSION["role"] === "studente" && hasSentMessageToStudent($db, $user_profile_info["email"], $_SESSION["email"])) {
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
            }*/
            ?>
        </div>
    </main>
    <?php echo print_footer();?>
</body>
</html>