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
    <link rel="stylesheet" type="text/css" href="../style/home.css">
    <meta charset="utf-8">
</head>
<body>
    <header>
        <h1 id="main_title">ClassBuddy</h1>
        <?php echo navbar();?>
    </header>
    <main>
        <div class="text">Trova il tutor più adatto a te!</div>
        <div class="image-grid">
                    <img src="../img/defaultUser.jpg" alt="Immagine di default" class="image">
                    <img src="../img/defaultUser.jpg" alt="Immagine di default" class="image">
                    <img src="../img/defaultUser.jpg" alt="Immagine di default" class="image">
                    <img src="../img/defaultUser.jpg" alt="Immagine di default" class="image">
                    <img src="../img/defaultUser.jpg" alt="Immagine di default" class="image">
                    <img src="../img/defaultUser.jpg" alt="Immagine di default" class="image">
        </div>
    </main> 
    <?php echo footer()?>
</body>
</html>
