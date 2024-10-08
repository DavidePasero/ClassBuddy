<?php
if (PHP_SESSION_NONE == session_status())
    session_start ();

require_once 'db.php';
require_once 'utils.php';

$db = connect_to_db();

$get_all_tutor_info_query = "SELECT
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

// Controllo che il method sia valido 
if ($_SERVER["REQUEST_METHOD"] !== "POST" or empty($_POST["action"]) or empty($_POST["i"]))
    echo_back_json_data (create_error_msg ("Richiesta non valida"));

$tutors = array();

$i = intval($_POST["i"]) * 3;

// Se l'azione è quella di caricare tutti i tutor, eseguo $get_all_tutor_info_query
if ($_POST["action"] === "get_all_tutor_info") {
    $result = prepared_query($db, $get_all_tutor_info_query, []);
    if ($result === false)
        echo_back_json_data (create_error_msg ("Errore del database, invitiamo a riprovare più tardi"));
    
    $tutors = $result->fetch_all(MYSQLI_ASSOC);
}
// Se l'azione è il filtraggio, pulisco l'input e filtro i tutor
else if ($_POST["action"] === "filter_tutors") {
    if (empty($_POST["luogo"]) or empty($_POST["materia"]))
        echo_back_json_data (create_error_msg ("Parametri mancanti"));

    // Input cleaning
    $luogo = strtolower(trim($_POST["luogo"]));
    $citta = $luogo === "online" ? null : $luogo;
    $online = $luogo === "online" ? $luogo : null;
    $materia = strtolower(trim($_POST["materia"]));
    $prezzo = 1000; // Default

    if (isset($_POST["prezzo"]) and $_POST["prezzo"] !== "")
        $prezzo = clamp (1, 1000, intval(trim($_POST["prezzo"])));
    
    // Filtra i tutor in base ai parametri
    $filter_tutors_query = <<<QUERY
                SELECT DISTINCT tutor.email FROM S5204959.tutor
                JOIN S5204959.insegnamento ON tutor.email = insegnamento.tutor
                LEFT JOIN S5204959.recensione ON tutor.email = recensione.tutor
                WHERE insegnamento.materia = ? AND insegnamento.tariffa <= ? AND (false
                QUERY;
                if (!is_null($online)) 
                    $filter_tutors_query .= " OR tutor.online = 1 ";
                if (!is_null($citta))
                    $filter_tutors_query .= " OR tutor.citta = ? ";
                $filter_tutors_query .= <<<QUERY
                    )
                    GROUP BY tutor.email
                    ORDER BY AVG(recensione.valutaz) DESC;
                QUERY;
    
    $filtered_tutors = prepared_query ($db, $filter_tutors_query, is_null($citta) ? [$materia, $prezzo] : [$materia, $prezzo, $citta])->fetch_all (MYSQLI_ASSOC);
    
    if ($filtered_tutors === false or count($filtered_tutors) === 0)
        echo_back_json_data (create_error_msg ("Nessun tutor trovato"));

    // Eseguo una versione modificata di $get_info_and_filter_query per farla lavorare solo sui tutor che corrispondono al filtro
    $get_info_and_filter_query = $get_all_tutor_info_query . 
        "\nWHERE insegnamento.materia = ? AND insegnamento.tariffa <= ? AND
        tutor.email IN (" . implode (", ", array_map (function ($tutor) {return "'" . $tutor["email"] . "'";}, $filtered_tutors)) . ")
        LIMIT ?";
        
    $tutors = prepared_query ($db, $get_info_and_filter_query, [$materia, $prezzo, $i])->fetch_all (MYSQLI_ASSOC);
}
else 
    echo_back_json_data (create_error_msg ("Richiesta non valida"));

// Per ogni tutor eseguo l'encoding della propic e creo un array associativo con le informazioni del tutor
// questo serve per evitare di avere più volte lo stesso tutor nel json (quando hanno più insegnamenti nel get_all_tutor_info)
$tutors_data = array();
foreach ($tutors as $tutor) {
    $email = $tutor["email"];
    if (!isset($tutors_data[$email])) {

        $tutors_data[$email] = [
            'email' => $email,
            'citta' => $tutor['citta'],
            'online' => $tutor['online'],
            'presenza' => $tutor['presenza'],
            'firstname' => $tutor['firstname'],
            'lastname' => $tutor['lastname'],
            'propic' => get_data_uri($tutor["propic"], $tutor["propic_type"]),
            'materia' => [],
            'tariffa' => [],
        ];
    }

    if ($tutor["materia"] !== NULL) {
        array_push($tutors_data[$email]["materia"], $tutor["materia"]);
        array_push($tutors_data[$email]["tariffa"], $tutor["tariffa"]);
    }
}

$tutors_data = array_slice($tutors_data, $i-3, 3);

if (count($tutors_data) === 0)
    echo_back_json_data (create_error_msg ("Non ci sono tutor da caricare"));
else
    echo_back_json_data ($tutors_data);
?>
