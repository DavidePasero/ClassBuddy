<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start ();

if (!isset ($_SESSION ["authenticated"]) or $_SERVER["REQUEST_METHOD"] != "POST") {
    header ("Location: login.php");
}
  
require_once 'db.php';
require_once 'utils.php';

$db = connect_to_db ();

$status = true;

$db->begin_transaction();

$insegnamenti = explode ("\n", file_get_contents("../res/insegnamenti.txt"));

try {
    // Modifica della foto profilo
    if (isset($_FILES["propic"]) and $_FILES["propic"]["error"] == UPLOAD_ERR_OK) {
        // Recupera le info del file
        $propic = $_FILES["propic"];
        $propicTmpName = $propic["tmp_name"];
        // Legge il contenuto del file
        $propicContent = file_get_contents($propicTmpName);
        // Recupera l'estensione del file
        $fileExtension = pathinfo($propic["name"], PATHINFO_EXTENSION);
        // Controlla che l'estensione sia tra quelle consentite
        if ($fileExtension !== "jpg" and $fileExtension !== "jpeg" and $fileExtension !== "png" and $fileExtension !== "gif")
            $status = false;
        else
            prepared_query ($db, "UPDATE S5204959.utente SET propic=?, propic_type=? WHERE email=?;", [$propicContent, $fileExtension, $_SESSION["email"]]);
    }

    // Rimozione degli insegnamenti
    if ($_SESSION ["role"] === "tutor" and isset ($_POST ["remove_insegnamento"])) {
        for ($i = 0; $i < count ($_POST ["remove_insegnamento"]); $i++) {
            // Elimina tutte le tuple (tutor, materia, tariffa) dove tutor è l'utente corrente
            prepared_query ($db, "DELETE FROM S5204959.insegnamento WHERE tutor=? AND materia=?;", [$_SESSION["email"], $_POST ["remove_insegnamento"][$i]]);
        }
    }

    // Aggiunta degli insegnamenti
    if (
        $_SESSION ["role"] === "tutor" and 
        isset ($_POST ["materia"]) and isset ($_POST ["tariffa"]) and
        count ($_POST ["materia"]) == count ($_POST ["tariffa"])
        ) {
        // Aggiunge le tuple (tutor, materia, tariffa) dove tutor è l'utente corrente
        $tutor = $_SESSION ["email"];
        for ($i = 0; $i < count ($_POST ["materia"]); $i++) {
            $_POST["tariffa"][$i] = clamp ($_POST["tariffa"][$i], 1, 1000);
            // Cotrolla che la materia sia tra quelle consentite
            if (!in_array ($_POST ["materia"][$i], $insegnamenti)) {
                $status = false;
                break;
            }
            prepared_query ($db,
                "INSERT INTO S5204959.insegnamento (tutor, materia, tariffa) VALUES (?, ?, ?);",
                [$tutor, $_POST ["materia"][$i], $_POST ["tariffa"][$i]]);
        }
    }
} catch (mysqli_sql_exception $exception) {
    $status = false;
}

/* 
    Casi di errore:
    1. non viene ricevuta nessuna immagine e non vengono passati insegnamenti e tariffe
    2. viene passata solo materia o solo tariffa
    3. viene passata sia materia che tariffa ma con lunghezze diverse
    4. viene ricevuta un'immagine ma con errore
    5. $status = false -> Query non andate a buon fine o materia non presente in $insegnamenti
*/
$rule1 = (!isset($_FILES["propic"]) and !isset ($_POST ["materia"]) and !isset ($_POST ["tariffa"]));
$rule2 = (isset ($_POST ["materia"]) xor (isset ($_POST ["tariffa"])));
$rule3 = (isset ($_POST["materia"]) and isset ($_POST ["tariffa"]) and count ($_POST ["materia"]) != count ($_POST ["tariffa"]));
$rule4 = (isset($_FILES["propic"]) and $_FILES["propic"]["error"] != UPLOAD_ERR_OK and $_FILES["propic"]["error"] != UPLOAD_ERR_NO_FILE);
$rule5 = !$status;

if ($rule1 or $rule2 or $rule3 or $rule4 or $rule5) {
    // Il profilo non viene modificato se si verifica uno dei casi di errore
    $db->rollback();
    echo_back_json_data (create_error_msg ("Errore durante la modifica del profilo"));
} else {
    $db->commit();
    $db->close();
    echo_back_json_data (["status" => "Modifiche avvenute con successo"]);
}

?>