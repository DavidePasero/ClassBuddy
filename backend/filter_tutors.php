<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require 'db.php';

$db = connect_to_db();

$error = array();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $error["error"] = "Richiesta non valida";
    echo json_encode($error);
    exit;
}

if (!isset($_POST["luogo"]) or $_POST["luogo"] === "" or
    !isset($_POST["materia"]) or $_POST["materia"] === "") {
    $error["error"] = "Parametri mancanti";
    echo json_encode($error);
    exit;
}

// Input cleaning
$luogo = strtolower(trim($_POST["luogo"]));
$citta = $luogo === "online" ? null : $luogo;
$online = $luogo === "online" ? $luogo : null;
$materia = strtolower(trim($_POST["materia"]));
//! 1000 = max tariffa
if (isset($_POST["prezzo"]) and $_POST["prezzo"] !== "")
    $prezzo = max (1, min ($_POST["prezzo"]), 1000);
else
    $prezzo = 1000;

// Filters the tutors based on the user's input
$query = <<<QUERY
            SELECT DISTINCT tutor.email FROM S5204959.tutor
            JOIN S5204959.insegnamento ON tutor.email = insegnamento.tutor
            LEFT JOIN S5204959.recensione ON tutor.email = recensione.tutor
            WHERE insegnamento.materia = ? AND insegnamento.tariffa <= ? AND (false
            QUERY;
            if (!is_null($online)) 
                $query .= " OR tutor.online = 1 ";
            if (!is_null($citta))
                $query .= " OR tutor.citta = ? ";
            $query .= <<<QUERY
                )
                GROUP BY tutor.email
                ORDER BY AVG(recensione.valutaz) DESC;
            QUERY;

$filtered_tutors = prepared_query ($db, $query, is_null($citta) ? [$materia, $prezzo] : [$materia, $prezzo, $citta])->fetch_all (MYSQLI_ASSOC);

if ($filtered_tutors === false or count($filtered_tutors) === 0) {
    $error["error"] = "Nessun tutor trovato";
    echo json_encode($error);
    exit;
}

// Selects all the informations of the tutors that match the filters
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
    S5204959.insegnamento ON tutor.email = insegnamento.tutor
WHERE insegnamento.materia = ? AND insegnamento.tariffa <= ? AND
tutor.email IN (" . implode (", ", array_map (function ($tutor) {return "'" . $tutor["email"] . "'";}, $filtered_tutors)) . ")";

$data = prepared_query ($db, $query, [$materia, $prezzo])->fetch_all (MYSQLI_ASSOC);

if ($data === false) {
    $error["error"] = "Errore nella query di selezione delle informazioni";
    echo json_encode($error);
    exit;
}

// Encodes tutor propic in base64 and creates a data URI
$tutors_data = array();
foreach ($data as $tutor) {
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
    $tutors_data[$tutor["email"]] = $tutor;
}

// Ultimi controlli
$json_data = json_encode($tutors_data);
if (json_last_error() != JSON_ERROR_NONE) {
    $error["error"] = "Errore nella codifica JSON";
    echo json_encode($error);
    exit;
}

echo $json_data;
?>
