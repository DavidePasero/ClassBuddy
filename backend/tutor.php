<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

$db = connect_to_db();

function getTutorsData($db) {
    $query = "SELECT
        tutor.email,
        tutor.citta,
        tutor.online,
        tutor.presenza,
        utente.firstname,
        utente.lastname,
        utente.propic,
        utente.propic_type,
        insegnamento.materia,
        insegnamento.tariffa
    FROM
        S5204959.tutor
    INNER JOIN
        S5204959.utente ON tutor.email = utente.email
    LEFT JOIN
        S5204959.insegnamento ON tutor.email = insegnamento.tutor";

    $result = prepared_query($db, $query, []);
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Modify the data to include a data URI for the image
    foreach ($data as &$tutor) {
        if ($tutor["propic"] !== NULL) {
            // Create a data URI for the image
            $imageData = base64_encode($tutor["propic"]);
            $imageType = $tutor["propic_type"];
            $dataUri = "data:image/{$imageType};base64,{$imageData}";
            $tutor["propic"] = $dataUri;
        } else {
            // Use a default image if propic is NULL
            $tutor["propic"] = "../img/defaultUser.jpg";
        }

        // Convert the comma-separated string to an array of objects
        if ($tutor["materia"] !== null) {
            $materieArray = explode(",", $tutor["materia"]);
            $tariffeArray = explode(",", $tutor["tariffa"]);
            $tutor["insegnamento"] = array();

            foreach ($materieArray as $index => $materia) {
                $tutor["insegnamento"][] = array("materia" => $materia, "tariffa" => $tariffeArray[$index]);
            }
        } else {
            $tutor["insegnamento"] = null;
        }
    }

    return $data;
}

echo json_encode(getTutorsData($db));
?>
