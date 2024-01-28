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
    require __DIR__ . '/../backend/utils.php';
    
    $db = connect_to_db ();
    cookie_check ($db);
    
    if (!isset ($_SESSION ["authenticated"])) {
        $db->close ();
        header ("Location: login_form.php");
    }

    // Se sto visualizzando il mio profilo, allora $user_profile = $_SESSION ["email"], altrimenti $user_profile = $_GET ["email"]
    $user_profile = isset($_GET["email"]) ? $_GET["email"] : $_SESSION["email"];
    $user_profile_info = select_user_email($db, $user_profile);

    $dataUri = get_data_uri($user_profile_info["propic"], $user_profile_info["propic_type"]);

    $myprofile = $user_profile_info["email"] === $_SESSION["email"];

    // Media delle recensioni se l'utente è un tutor
    $averageRating = ($user_profile_info["role"] === "tutor") ? getAverageRating($db, $user_profile_info["email"]) : null;
?>
<html lang="it">
<head>
    <title>ClassBuddy</title>
    <link rel="icon" href="img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/profile.css">
    <link rel="stylesheet" type="text/css" href="../style/update_profile.css">
    <link rel="stylesheet" type="text/css" href="../style/stelline.css">
    <?php if ($myprofile)
        echo "<script type=\"module\" src=\"../scripts/update_profile.js\"></script>";
    ?>
    <meta charset="utf-8">
</head>
<body>
    <?php echo print_header();?>
    <main>
        <div id="contact-card">
            <form id="update_profile" action="../backend/update_profile.php" method="POST" name="update_profile" enctype="multipart/form-data">
                <div id="info-container">
                    <?php if ($myprofile): ?>
                        <button id="edit-info-btn" class="btn only-icon-button">
                            <img src="../res/icons/edit.svg" alt="Edit icon">
                        </button>
                    <?php endif; ?>

                    <div id="show_info">
                        <span id="name-span"><?php echo htmlentities($user_profile_info["firstname"] . " " . $user_profile_info["lastname"])?></span>
                        <span id="email-span"><?php echo htmlentities($user_profile_info["email"])?></span>
                    </div>

                    <div id="edit_info">
                        <input type="text" id="firstname" name="firstname" value="<?php echo htmlentities($user_profile_info["firstname"])?>">
                        <input type="text" id="lastname" name="lastname" value="<?php echo htmlentities($user_profile_info["lastname"])?>">
                    </div>
                </div>

                <?php 
                if ($user_profile_info["role"] === "tutor" && $averageRating !== null) {
                    echo '<div class="rating" id="rating">';
                    for ($i = 0; $i < 5; $i++) {
                        if ($i < round($averageRating))
                            echo '<span class="star active">&#9733;</span>';
                        else
                            echo '<span class="star">&#9733;</span>';
                    }
                    $number = number_format($averageRating, 1, ",");
                    echo '<span class=text>' . $number . ' su 5</span>';
                    echo '</div>';
                }
                ?>

                <div id="image_div">
                    <img id="image-preview" src="<?php echo $dataUri;?>" alt="Profile picture">
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
                                            <span class="materia">{$insegnamento ["materia"]}</span>
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

            <!--Chat button che chiama chat.php con l'email del destinatario come parametro della GET
                solo se il destinatario è un tutor e io sono uno studente-->
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
        </div>
    </main>
    <?php echo print_footer();?>
</body>
</html>