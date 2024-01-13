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
    * Il codice seguente serve a creare un oggetto JSON che contiene tutte le informazioni dei tutor.
    * La struttura dell'oggetto JSON è:
    *
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
            // Crea un data URI per la propic
            $imageData = base64_encode($tutor["propic"]);
            $imageType = $tutor["propic_type"];
            $dataUri = "data:image/{$imageType};base64,{$imageData}";
            $tutor["propic"] = $dataUri;
        } else {
            // Se la propic è NULL, usa quella di default
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
?>
