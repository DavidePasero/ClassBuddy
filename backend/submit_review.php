<?php
session_start();

require "db.php";
require "utils.php";
require "review.php";
$db = connect_to_db();

if (
    $_SERVER["REQUEST_METHOD"] == "POST" and
    isset($_SESSION["authenticated"]) and
    $_SESSION["authenticated"] and
    isset($_SESSION["email"]))
    {
    if (!empty ($_POST ["valutaz"]) and !empty ($_POST["commento"]) and !empty ($_POST["tutor"])) {
        // Valutazione da 1 a 5
        $valutaz = clamp(1, 5, intval(trim($_POST["valutaz"])));
        $commento = trim($_POST["commento"]);
        $tutorEmail = trim($_POST["tutor"]);
        
        /* 
            Controllo che lo studente possa mandare la recensione (ci deve essere stata uno scambio di messaggi e 
            non deve aver già mandato una recensione)
        */
        if (!hasSentMessageToStudent ($db, $tutorEmail, $_SESSION["email"]))
            echo_back_json_data (create_error_msg ("Non puoi inviare una recensione a questo tutor."));
            
        if (hasStudentReviewedTutor ($db, $_SESSION["email"], $tutorEmail))
            echo_back_json_data (create_error_msg ("Hai già inviato una recensione a questo tutor."));

        $query = "INSERT INTO recensione (studente, tutor, valutaz, commento) VALUES (?, ?, ?, ?)";
        $result = prepared_query($db, $query, [$_SESSION["email"], $tutorEmail, $valutaz, $commento]);

        if ($result) {
            $review = array(
                "studente" => $_SESSION["email"],
                "tutor" => $tutorEmail,
                "valutaz" => $valutaz,
                "commento" => $commento
            );

            echo_back_json_data ($review);
        } else
            echo_back_json_data (create_error_msg ("Errore durante l'invio della recensione."));
    } else
        echo_back_json_data (create_error_msg ("Tutti i campi devono essere compilati."));
}
?>