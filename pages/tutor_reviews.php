<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../backend/db.php';

$db = connect_to_db();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["tutor_email"])) {
    $tutorEmail = $_GET["tutor_email"];

    // Recupera tutte le recensioni per il tutor specificato
    $query = "SELECT * FROM recensione WHERE tutor = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $tutorEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviews = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
} else {
    echo "Parametro 'tutor_email' mancante.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>Recensioni Tutor</title>
    <link rel="stylesheet" type="text/css" href="../style/review.css">
    <!-- Aggiungi altri stili o script necessari -->
</head>
<body>
    <h1>Recensioni</h1>

    <?php
    if (isset($reviews) && !empty($reviews)) {
        foreach ($reviews as $review) {
            echo "<div class='review'>";
            echo "<p>Valutazione: " . $review['valutaz'] . "/5</p>";
            echo "<p>Commento: " . htmlentities($review['commento']) . "</p>";
            echo "<p>Scritta da: " . $review['studente'] . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Nessuna recensione disponibile per questo tutor.</p>";
    }
    ?>

    <!-- Aggiungi altri elementi della pagina o collegamenti necessari -->
</body>
</html>