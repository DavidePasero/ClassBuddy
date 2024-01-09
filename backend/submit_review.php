<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "db.php";
$db = connect_to_db();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["valutaz"]) && isset($_POST["commento"]) && isset($_POST["tutor"]) && isset($_POST["studente"])) {
        $valutaz = $_POST["valutaz"];
        $commento = $_POST["commento"];
        $tutorEmail = $_POST["tutor"];
        $studenteEmail = $_POST["studente"];

        $query = "INSERT INTO recensione (studente, tutor, valutaz, commento) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssis", $studenteEmail, $tutorEmail, $valutaz, $commento);

        if ($stmt->execute()) {
            $review = array(
                "studente" => $studenteEmail,
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

        $stmt->close();
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Tutti i campi devono essere compilati."));
    }
}
?>