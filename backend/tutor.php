<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require 'db.php';

$db = connect_to_db();

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

$tutors_data = array();

/*
    * The following code is used to create a JSON object that contains all the
    * tutors' data. The structure of the JSON object is the following:
    * {
    *     "email": {
    *         "email": "email",
    *         "citta": "citta",
    *         "online": "online",
    *         "presenza": "presenza",
    *         "firstname": "firstname",
    *         "lastname": "lastname",
    *         "propic": "propic",
    *         "insegnamento": [
    *             "materia1",
    *             "materia2",
    *             ...
    *         ],
    *         "tariffa": [
    *             "tariffa1",
    *             "tariffa2",
    *             ...
    *         ]
    *     },
    *     ...
    * }
    *
*/
foreach ($data as $tutor) {
    $email = $tutor['email'];

    if (!isset($tutors_data[$email])) {

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

        $tutors_data[$email] = [
            'email' => $email,
            'citta' => $tutor['citta'],
            'online' => $tutor['online'],
            'presenza' => $tutor['presenza'],
            'firstname' => $tutor['firstname'],
            'lastname' => $tutor['lastname'],
            'propic' => $tutor['propic'],
            'materia' => [],
            'tariffa' => [],
        ];
    }

    if ($tutor["materia"] !== NULL) {
        array_push($tutors_data[$email]["materia"], $tutor["materia"]);
        array_push($tutors_data[$email]["tariffa"], $tutor["tariffa"]);
    }
}

echo json_encode($tutors_data);
/*
$email = $tutor["email"];
    if (!in_array ($email, $tutors_data)) {
        $tutors_data[$email][$email] = $email;
        $tutors_data[$email]["citta"] = $tutor["citta"];
        $tutors_data[$email]["online"] = $tutor["online"];
        $tutors_data[$email]["presenza"] = $tutor["presenza"];
        $tutors_data[$email]["firstname"] = $tutor["firstname"];
        $tutors_data[$email]["lastname"] = $tutor["lastname"];

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

        $tutors_data[$email]["propic"] = $tutor["propic"];
        $tutors_data[$email]["insegnamento"] = array();
        $tutors_data[$email]["tariffa"] = array();
    }

    if ($tutor["materia"] !== NULL) {
        array_push($tutors_data[$email]["insegnamento"], $tutor["materia"]);
        array_push($tutors_data[$email]["tariffa"], $tutor["tariffa"]);
    }
*/
?>
