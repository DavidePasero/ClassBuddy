<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require __DIR__ . '/../backend/page.php';
    require __DIR__ . '/../backend/db.php';
    
    $db = connect_to_db ();
    cookie_check ($db);
    
    if (!isset ($_SESSION ["authenticated"])) {
        $db->close ();
        header ("Location: login_form.php");
    }

    // Se sto visualizzando il mio profilo, allora $user_profile = $_SESSION ["email"], altrimenti $user_profile = $_GET ["email"]
    $user_profile = isset($_GET ["email"]) ? $_GET ["email"] : $_SESSION ["email"];
    $user_profile_info = select_user_email ($db, $user_profile);

    if ($user_profile_info ["propic"] !== NULL) {
        // Create a data URI for the image
        $imageData = base64_encode($user_profile_info["propic"]);
        $imageType = $user_profile_info["propic_type"];
        $dataUri = "data:image/{$imageType};base64,{$imageData}";
    } else {
        $dataUri = "../img/defaultUser.jpg";
    }

    $myprofile = $user_profile_info ["email"] === $_SESSION ["email"];
?>
<html lang="it">
<head>
    <title>ClassBuddy</title>
    <link rel="icon" href="img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/home.css">
    <link rel="stylesheet" type="text/css" href="../style/profile.css">
    <link rel="stylesheet" type="text/css" href="../style/modify_profile.css">
    <?php if ($myprofile)
        echo "<script type=\"module\" src=\"../scripts/modify_profile.js\" defer></script>";
    ?>
    <meta charset="utf-8">
</head>
<body>
    <header>
        <h1 id="main_title">ClassBuddy</h1>
        <?php echo navbar();?>
    </header>
    <main>
        <p>Profilo di: <?php echo htmlentities ($user_profile_info ["firstname"] . " " . $user_profile_info ["lastname"])?></p>
        <form id="form" action = "../backend/modify_profile.php" method="POST" name="modify_profile" enctype="multipart/form-data">
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
                            <input type="submit" id="submit" name="Submit" value="Salva modifiche">
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
    </main>
    <?php echo footer();?>
</body>
</html>