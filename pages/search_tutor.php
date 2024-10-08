<?php 
    if (PHP_SESSION_NONE == session_status())
        session_start ();
    require_once __DIR__ . '/../backend/page.php';
    require_once __DIR__ . '/../backend/db.php';

    $db = connect_to_db ();
    cookie_check ($db);
    $db->close ();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/home.css">
    <link rel="stylesheet" type="text/css" href="../style/tutor.css">
    <link rel="stylesheet" type="text/css" href="../style/search_tutor.css">
    <link rel="stylesheet" type="text/css" href="../style/form.css">
    <script type="module" src="../scripts/filter_tutor.js"></script>
    <title>ClassBuddy</title>
</head>
<body>
    <?php echo print_header(); ?>
    <main>
        <form id="filter-form">
            <div id="parameters-container">
                <input list="cittaDropdown" class="parameter" id="cittaInput" name="luogo" placeholder="Filtra per luogo" required>
                
                <datalist id="cittaDropdown">
                    <option value="Online">Online</option>
                </datalist>

                <select id="materia" class="parameter" name="materia" required>
                </select>

                <input type="number" id="prezzo" class="parameter" name="prezzo" min=1 max=1000 placeholder="Tariffa oraria massima">
                <button id="submit-button" class="btn submit">Cerca</button>
            </div>
        </form>

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

    <?php echo print_footer(); ?>
</body>
</html>
