<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

if (session_status() === PHP_SESSION_NONE)
    session_start ();

require 'db.php';
require 'utils.php';
$db = connect_to_db ();

// Questo script mette insieme tutti i pezzi della chat: fetch_chat, new_msgs, search_msgs, send_msg.
if (!isset($_SESSION["authenticated"]) || $_SERVER["REQUEST_METHOD"] != "POST")
    echo_back_json_data (create_error_msg ("Devi essere autenticato per proseguire, inoltre, bad request"));

// action determina quale funzione chiamare: fetch_chat, new_msgs, search_msgs, send_msg
if (!isset($_POST["action"]) or empty($_POST["action"]))
    echo_back_json_data (create_error_msg ("Devi specificare un'azione"));
else {
    switch ($_POST["action"]) {
        case "fetch_chat":
            fetch_chat($db);
            break;
        case "new_msgs":
            new_msgs($db);
            break;
        case "search_msgs":
            search_msgs($db);
            break;
        case "send_msg":
            send_msg($db);
            break;
        default:
            echo_back_json_data (create_error_msg ("Azione non riconosciuta"));
    }
}

// Prende tutti i messaggi della chat
function fetch_chat($db) {
    filter_messages ($db, 
        "SELECT mittente, testo FROM S5204959.messaggio WHERE (mittente = ? AND destinatario = ?) OR (mittente = ? AND destinatario = ?) ORDER BY timestamp ASC",
        [$_POST["recipient"], $_SESSION["email"], $_SESSION["email"], $_POST["recipient"]]);
}

// Prende i nuovi messaggi arrivati dopo un certo timestamp (il timestamp dell'ultimo messaggio ricevuto)
function new_msgs($db) {
    filter_messages ($db, 
        "SELECT mittente, testo, timestamp FROM S5204959.messaggio WHERE (mittente = ? AND destinatario = ?) AND timestamp > ? ORDER BY timestamp ASC",
        [$_POST["recipient"], $_SESSION["email"], $_POST["timestamp"]]);
}

// Cerca i messaggi che contengono una certa stringa specificata come POST parameter
function search_msgs ($db) {
    if (!isset($_POST["ricerca"]))
        echo_back_json_data (create_error_msg ("Devi specificare una stringa da cercare"));

    filter_messages ($db, 
        "SELECT mittente, testo, timestamp FROM S5204959.messaggio WHERE ((mittente = ? AND destinatario = ?) OR (mittente = ? AND destinatario = ?)) AND testo LIKE ? ORDER BY timestamp ASC",
        [$_POST["recipient"], $_SESSION["email"], $_SESSION["email"], $_POST["recipient"], "%".$_POST["ricerca"]."%"]);
}

// Funzione generica per filtrare i messaggi
function filter_messages ($db, $query, $params) {
    check_destinatario();
    $messages = prepared_query ($db, $query, $params)->fetch_all (MYSQLI_ASSOC);
    if (!$messages)
        echo_back_json_data (create_error_msg ("Errore nel recupero dei messaggi"));
    echo json_encode($messages);
    exit();
}

// Invia un messaggio
function send_msg ($db) {
    check_destinatario();
    if (!isset ($_POST ["message"]) or empty ($_POST ["message"]))
        echo_back_json_data (create_error_msg ("Devi inserire un messaggio"));

    // Controlla se la chat è valida (Tutor a Studente o Studente a Tutor)
    $recipient = select_user_email ($db, $_POST ["recipient"]);

    if (!$recipient)
        echo_back_json_data (create_error_msg ("Destinatario non trovato"));
    
    // Controlla se il destinatario ha un ruolo diverso dal mittente
    if ($recipient["role"] !== $_SESSION ["role"]) {
        // Send message prende i seguenti dati: mittente, destinatario, messaggio.
        // Il mittente è l'utente che è loggato, il destinatario è l'utente con cui il mittente sta chattando.
        $insert_msg = prepared_query ($db, 
        "INSERT INTO S5204959.messaggio (mittente, destinatario, timestamp, testo) VALUES (?, ?, ?, ?)",
        [$_SESSION ["email"], $_POST ["recipient"], date("Y-m-d H:i:s"), $_POST ["message"]]);

        if (!$insert_msg)
            echo_back_json_data (create_error_msg ("Invio fallito, aggiona la pagina e riprova"));
        else
            echo_back_json_data (json_encode(["status" => "OK"]));
    }
    else
    echo_back_json_data (create_error_msg ("I messaggi possono essere inviati solo tra tutor e studenti"));
}

function check_destinatario () {
    if (!isset($_POST["recipient"]) or empty($_POST["recipient"]))
        echo_back_json_data (create_error_msg ("Devi specificare un destinatario"));
}
?>  