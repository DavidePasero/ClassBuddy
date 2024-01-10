<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require "db.php";
require "review.php";
$db = connect_to_db();

if (
    $_SERVER["REQUEST_METHOD"] == "POST" and
    isset($_SESSION["authenticated"]) and
    $_SESSION["authenticated"] and
    isset($_SESSION["email"]))
    {
    if (isset($_POST["valutaz"]) && isset($_POST["commento"]) && isset($_POST["tutor"])) {
        $valutaz = trim($_POST["valutaz"]);
        $valutaz = intval($valutaz);
        // Check if valutaz is an integer between 1 and 5, if not return an error
        if ($valutaz < 1 || $valutaz > 5) {
            header('Content-Type: application/json');
            echo json_encode(array("error" => "La valutazione deve essere un numero intero compreso tra 1 e 5."));
            exit();
        }

        if (!hasSentMessageToStudent ($db, $_POST["tutor"], $_SESSION["email"])) {
            header('Content-Type: application/json');
            echo json_encode(array("error" => "Non puoi inviare una recensione a questo tutor."));
            exit();
        }
            
        if (hasStudentReviewedTutor ($db, $_SESSION["email"], $_POST["tutor"])) {
            header('Content-Type: application/json');
            echo json_encode(array("error" => "Hai già inviato una recensione a questo tutor."));
            exit();
        }

        $commento = trim($_POST["commento"]);
        $tutorEmail = trim($_POST["tutor"]);

        $query = "INSERT INTO recensione (studente, tutor, valutaz, commento) VALUES (?, ?, ?, ?)";
        $result = prepared_query($db, $query, [$_SESSION["email"], $tutorEmail, $valutaz, $commento]);

        if ($result) {
            $review = array(
                "studente" => $_SESSION["email"],
                "tutor" => $tutorEmail,
                "valutaz" => $valutaz,
                "commento" => $commento
            );

            header('Content-Type: application/json');
            echo json_encode($review);
        } else {
            header('Content-Type: application/json');
            echo json_encode(array("error" => "Errore durante l'invio della recensione."));
        }

    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Tutti i campi devono essere compilati."));
    }
}
?>