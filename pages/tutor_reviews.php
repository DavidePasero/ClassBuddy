<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../backend/db.php';
require __DIR__ . '/../backend/page.php';
require __DIR__ . '/../backend/review.php';

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
    echo "Nessun tutor specificato.";
    exit();
}

$user_profile = isset($_GET["email"]) ? $_GET["email"] : $_SESSION["email"];
$user_profile_info = select_user_email($db, $user_profile);

$tutor_email = $_GET["tutor_email"];
$tutor_info = select_user_email($db, $tutor_email);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>ClassBuddy</title>
    <link rel="icon" href="img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/review.css">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/stelline.css">
    <script src="../scripts/loadReview.js" defer></script>
    <meta charset="utf-8">
</head>
<body>
    <?php echo print_header();?>
    <h1>Recensioni</h1>
    <div id="reviews">
    <?php
    if (isset($reviews) && !empty($reviews)) {
        foreach ($reviews as $review) {
            echo "<div class='review'>";
            echo "<div class='parameter'>Valutazione</div>";
            echo '<div class';
            for ($i = 0; $i < 5; $i++) {
                if ($i < round($review['valutaz']))
                    echo '<span class="star active">&#9733;</span>';
                else
                    echo '<span class="star">&#9734;</span>';
            }
            echo '</div>';
            echo "<div class='parameter'>Commento</div><p>" . htmlentities($review['commento']) . "</p>";
            echo "<div class='parameter'>Scritta da</div><p>" . $review['studente'] . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Nessuna recensione disponibile per questo tutor.</p>";
    }
    ?>
    </div>

    <?php
    if ($user_profile_info["role"] == "studente" && hasSentMessageToStudent($db, $tutor_email, $_SESSION["email"]) && !hasStudentReviewedTutor($db, $_SESSION["email"], $tutor_email)) {
        echo '<h2>Inserisci una valutazione</h2>';
        echo '<form action="../backend/submit_review.php" name="valutazione" method="post">';
        echo '<input type="hidden" name="tutor" value="' . htmlspecialchars($tutor_email) . '">';
        echo '<input type="hidden" name="studente" value="' . htmlspecialchars($_SESSION["email"]) . '">';
        echo '<label for="valutaz">Valutazione: </label>';
        echo '<select id="valutaz" name="valutaz" required>';
        for ($i = 1; $i <= 5; $i++) {
            echo "<option value=\"$i\">$i</option>";
        }
        echo '</select><br>';
        echo '<label for="commento">Commento: </label>';
        echo '<textarea id="commento" name="commento" required></textarea><br>';
        echo '<input type="submit" value="Invia">';
        echo '</form>';
    }
    ?>
<?php echo print_footer();?>
</body>
</html>