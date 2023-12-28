<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

$db = connect_to_db();

function getTutorsData($db) {
    $query = "SELECT tutor.email, tutor.citta, tutor.online, tutor.presenza, utente.firstname, utente.lastname, utente.propic, utente.propic_type
              FROM S5204959.tutor
              INNER JOIN S5204959.utente ON tutor.email = utente.email";
              
    $result = prepared_query($db, $query, []);

    $data = $result->fetch_all(MYSQLI_ASSOC);

    return $data;
}

$data = getTutorsData($db);

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
}

echo json_encode($data);
?>
