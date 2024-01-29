<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require __DIR__ . '/../backend/page.php';
    require __DIR__ . '/../backend/db.php';

    $db = connect_to_db ();
    cookie_check ($db);
    $db->close ();
?>
<html lang="it">
<head>
    <title>
        ClassBuddy
    </title>
    <link rel="icon" href="../img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/tutor.css">
    <link rel="stylesheet" type="text/css" href="../style/home.css">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <meta charset="utf-8">
    <script type="module" src="../scripts/load_tutors.js"></script>
</head>
<body>
    <?php echo print_header();?>
    <main>
        <div id="page_title">Trova il tutor più adatto a te!</div>
        <div id="image-grid-container">
            <div id="image-grid"></div>
        </div>
        <div id="altri_tutor_div">
            <button type="button" class="btn icon-button form-element" id="altri_tutor">
                Carica altri tutor
                <img src="../res/icons/update.svg" alt="Carica">
            </button>
        </div>
    </main> 
    <?php echo print_footer()?>
</body>
</html>
