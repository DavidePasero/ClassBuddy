<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    require __DIR__ . '/../backend/page.php';

    // array associativo che mappa il tipo di errore con il messaggio da mostrare
    $error_msgs = array(
        "upload_propic_failed" => "Upload della propic fallito. Prova più tardi.",
        "no_img_received" => "Nessuna immagine è stata selezionata per l'update.",
        "missing_recipient" => "Nessun destinatario è stato specificato per la chat.",
        "invalid_chat" => "La chat è ammessa tra studenti e tutor, non tra studenti o tra tutor.",
        "invalid_request" => "La richiesta non è soddisfacibile dal server.",
        "invalid_file_extension" => "Estensione del file non valida. Sono ammessi solo file .jpg, .jpeg, .png e .gif.",
        "generic_modify_profile_error" => "Errore generico durante la modifica del profilo."
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
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <meta charset="utf-8">
</head>
<body>
    <?php echo print_header();?>
    <main>
        <div>OOOPS! Qualcosa è andato storto. Riprova più tardi.</div>
        <div>Messaggio di errore: <?php echo $msg;?></div>
    </main>
    <?php echo print_footer();?>
</body>
</html>