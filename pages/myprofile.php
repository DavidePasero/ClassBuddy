<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require 'page.php';
    require __DIR__ . '/../backend/db.php';
    
    $db = connect_to_db ();
    cookie_check ($db);
    
    if (!isset ($_SESSION ["authenticated"])) {
        $db->close ();
        header ("Location: login.php");
    }

?>
<html lang="it">
<head>
    <title>VR space</title>
    <link rel="icon" href="img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/home.css">
    <meta charset="utf-8">
</head>
<body>
    <header>
        <h1 id="main_title">VR space</h1>
        <?php echo navbar();?>
    </header>
    <main>
        <p>Welcome <?php echo htmlentities (select_user_email ($db, $_SESSION["email"]) ["firstname"])?>!</p>
    </main>
    <?php echo footer();?>
</body>
</html>