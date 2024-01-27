<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
$db = connect_to_db ();

if (!isset ($_SESSION ["authenticated"])) {
    $db->close ();
    header ("Location: login_form.php");
}

// Se sto visualizzando il mio profilo, allora $user_profile = $_SESSION ["email"], altrimenti $user_profile = $_GET ["email"]
$user_profile = isset($_GET["email"]) ? $_GET["email"] : $_SESSION["email"];
$user_profile_info = select_user_email($db, $user_profile);

// Funzione per verificare se il tutor ha inviato almeno un messaggio allo studente
function hasSentMessageToStudent($db, $tutorEmail, $studentEmail) {
    $result = prepared_query($db, "SELECT COUNT(*) as count FROM messaggio WHERE mittente = ? AND destinatario = ? LIMIT 1", [$tutorEmail, $studentEmail]);
    $row = $result->fetch_assoc();
    $count = $row['count'];

    return $count > 0;
}

function getStudentReview($db, $tutorEmail, $studentEmail) {
    $result = prepared_query($db, "SELECT valutaz, commento FROM recensione WHERE tutor = ? AND studente = ? LIMIT 1", [$tutorEmail, $studentEmail]);
    $row = $result->fetch_assoc();

    return $row;
}

function hasStudentReviewedTutor($db, $student_email, $tutor_email) {
    $review = getStudentReview($db, $tutor_email, $student_email);
    return $review != null;
}
?>