<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require 'page.php';
    require __DIR__ . '/../backend/db.php';

    $db = connect_to_db ();
    cookie_check ($db);
    
    if (!(isset ($_SESSION ["authenticated"]) and $_SESSION ["admin"])) {
        $db->close ();
        header ("Location: login.php");
    }

?>
<html lang="it">
<head>
    <title>All users</title>
    <link rel="icon" href="img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/home.css">
    <meta charset="utf-8">
</head>
<body>
    <header>
        <h1 id="main_title">ClassBuddy</h1>
        <?php echo navbar();?>
    </header>
    <main>
        <?php 
            $users = prepared_query ($db,
            "SELECT firstname, lastname, email FROM S5204959.utente",
            array());
            while ($row = $users->fetch_assoc ()) {
                echo $row ["firstname"] . " | " . $row ["lastname"] . " | " . $row ["email"] . "<br>";
            }

            
        ?>
    </main> 
</body>
</html>