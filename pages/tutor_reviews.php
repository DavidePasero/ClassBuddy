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
    $reviews = prepared_query($db, "SELECT * FROM recensione WHERE tutor = ?", [$tutorEmail])->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Nessun tutor specificato.";
    exit();
}

$user_profile = $_SESSION["email"];
$user_profile_info = select_user_email($db, $user_profile);

$tutor_email = $_GET["tutor_email"];
$tutor_info = select_user_email($db, $tutor_email);

// Se l'utente non esiste, allora lo reindirizzo alla pagina principale
if ($tutor_info === null) {
    $db->close ();
    header ("Location: index.php");
}

// Uno studente può scrivere una recensione ad un tutor solo se il tutor gli ha inviato almeno un messaggio e se lo studente non gli ha ancora scritto una recensione
$can_write_review = $user_profile_info["role"] == "studente" &&
    hasSentMessageToStudent($db, $tutor_email, $_SESSION["email"]) &&
    !hasStudentReviewedTutor($db, $_SESSION["email"], $tutor_email)
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>ClassBuddy</title>
    <link rel="icon" href="img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/review.css">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/stelline.css">
    <script type="module" src="../scripts/reviews.js"></script>
    <?php if ($can_write_review)
        echo "<script src=\"../scripts/stelline.js\" defer></script>";
    ?>
    <meta charset="utf-8">
</head>
<body>
    <main>
        <?php echo print_header();?>
        <h1>Recensioni</h1>
        <div id="reviews-container">
        <?php
        if (isset($reviews) && !empty($reviews)) {
            foreach ($reviews as $review) {
                echo "<div class='review'>";
                echo "<div class='parameter'>Valutazione</div>";
                echo "<div class='rating'>";
                for ($i = 0; $i < 5; $i++) {
                    if ($i < round($review['valutaz']))
                        echo '<span class="star active">&#9733;</span>';
                    else
                        echo '<span class="star">&#9733;</span>';
                }
                echo "</div>";
                echo "<div class='parameter'>Commento</div><p>" . htmlentities($review['commento']) . "</p>";
                echo "<div class='parameter'>Scritta da</div><p>" . htmlentities($review['studente']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p id=\"no-rev\">Nessuna recensione disponibile per questo tutor.</p>";
        }
        ?>
        </div>

        <?php if ($can_write_review): ?>
            <h2>Inserisci una valutazione</h2>
            <form action="../backend/submit_review.php" name="valutazione" method="post">
                <div class="form-content">
                    <input type="hidden" name="tutor" value=<?php echo $tutor_email;?>>
                    <div class="rating" id="rating">
                        <!-- 5 stelle disponibili per votare-->
                        <span class="star active" data-value="1">&#9733;</span>
                        <span class="star" data-value="2">&#9733;</span>
                        <span class="star" data-value="3">&#9733;</span>
                        <span class="star" data-value="4">&#9733;</span>
                        <span class="star" data-value="5">&#9733;</span>
                        <input type="hidden" name="valutaz" id="valutaz" value="1">
                    </div>
                    <textarea id="commento" name="commento" placeholder="Inserisci un commento alla tua recensione..." required></textarea><br>
                    <input type="submit" class="btn submit" value="Invia">
                </div>
            </form>
        <?php endif; ?>
    </main>
<?php echo print_footer();?>
</body>
</html>