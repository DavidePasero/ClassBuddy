<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require __DIR__ . '/../backend/page.php';

    // array associativo che mappa il tipo di errore con il messaggio da mostrare
    $error_msgs = array(
        "upload_propic_failed" => "Upload della propic fallito. Prova più tardi.",
        "no_img_received" => "Please select an image file to upload."
    );

    $msg = "Errore sconosciuto.";

    if (isset ($_GET ["error_type"])) {
        $msg = $error_msgs[$_GET ["error_type"]];
    }
?>
<html lang="it">
<head>
    <title>ClassBuddy</title>
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
        <div>OOOPS! Qualcosa è andato storto. Riprova più tardi.</div>
        <div>Messaggio di errore: <?php echo $msg;?></div>
    </main>
    <?php echo footer();?>
</body>
</html>