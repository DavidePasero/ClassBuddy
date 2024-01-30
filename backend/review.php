<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once 'utils.php';

$db = connect_to_db ();

if (!isset ($_SESSION ["authenticated"])) {
    $db->close ();
    header ("Location: login_form.php");
}

// Funzione per verificare se il tutor ha inviato almeno un messaggio allo studente
function hasSentMessageToStudent($db, $tutorEmail, $studentEmail) {
    $result = prepared_query($db, "SELECT COUNT(*) as count FROM messaggio WHERE mittente = ? AND destinatario = ? LIMIT 1", [$tutorEmail, $studentEmail]);
    $row = $result->fetch_assoc();
    if ($row === null)
        return false;
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