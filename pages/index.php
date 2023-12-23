<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require 'page.php';
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
    <link rel="stylesheet" type="text/css" href="../style/home.css">
    <meta charset="utf-8">
</head>
<body>
    <header>
        <h1 id="main_title">ClassBuddy</h1>
        <?php echo navbar();?>
    </header>
    <main>
        <p>VR</p>
        <img src="../img/quest3.jpg" alt="Immagine che ritrae il nuovo visore di Meta: Quest 3" class="container main-img">
    </main> 
    <?php echo footer()?>
</body>
</html>