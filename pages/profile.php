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
    <link rel="stylesheet" type="text/css" href="../style/modify_propic.css">
    <?php if ($myprofile)
        echo "<script src=\"../scripts/modify_propic.js\" defer></script>"
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
        <form action = "../backend/modify_profile.php" method="POST" name="modify_profile" enctype="multipart/form-data">
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
                if ($myprofile) {
                    echo <<<SUBMIT_FORM
                        <div id="submit_div">
                            <input type="submit" id="submit" name="Submit" value="Invia">
                        </div>
                        SUBMIT_FORM;
                }?>
        </form>
    </main>
    <?php echo footer();?>
</body>
</html>