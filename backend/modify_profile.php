<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start ();

if (!isset ($_SESSION ["authenticated"])) {
    header ("Location: login.php");
}
 
require_once 'db.php'; 

$db = connect_to_db ();

$status = true;

$db->begin_transaction();

$insegnamenti = explode ("\n", file_get_contents("../res/insegnamenti.txt"));

try {
    if (isset ($_POST ["remove_insegnamento"])) {
        for ($i = 0; $i < count ($_POST ["remove_insegnamento"]); $i++) {
            // Cancella tutte le tuple (tutor, materia, tariffa) in cui tutor è l'utente corrente
            prepared_query ($db, "DELETE FROM S5204959.insegnamento WHERE tutor=? AND materia=?;", [$_SESSION["email"], $_POST ["remove_insegnamento"][$i]]);
        }
    }

    if (isset($_FILES["propic"]) and $_FILES["propic"]["error"] == UPLOAD_ERR_OK) {
        //Recupero le file info
        $propic = $_FILES["propic"];
        $propicTmpName = $propic["tmp_name"];
        // Leggo il contenuto del file
        $propicContent = file_get_contents($propicTmpName);
        // Recupera l'estensione del file
        $fileExtension = pathinfo($propic["name"], PATHINFO_EXTENSION);
        // Accetta solo alcune estensioni
        if ($fileExtension !== "jpg" and $fileExtension !== "jpeg" and $fileExtension !== "png" and $fileExtension !== "gif") {
            header ("Location: ../pages/error.php?error_type=invalid_file_extension");
        }
        
        prepared_query ($db, "UPDATE S5204959.utente SET propic=?, propic_type=? WHERE email=?;", [$propicContent, $fileExtension, $_SESSION["email"]]); 
    }

    if (
            $_SESSION ["role"] === "tutor" and 
            isset ($_POST ["materia"]) and isset ($_POST ["tariffa"]) and
            count ($_POST ["materia"]) == count ($_POST ["tariffa"])
        ) {
        
        $tutor = $_SESSION ["email"];
        for ($i = 0; $i < count ($_POST ["materia"]); $i++) {
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
    - non viene ricevuta nessuna immagine e non vengono passati array di insegnamento e tariffa
    - viene passata solo materia o solo tariffa
    - vengono passati sia materia che tariffa ma con lunghezze diverse
    - viene ricevuta un'immagine ma con errore
    - $status = false -> Query non andate a buon fine o materia non in $insegnamenti
   
*/
$rule1 = (!isset($_FILES["propic"]) and !isset ($_POST ["materia"]) and !isset ($_POST ["tariffa"]));
$rule2 = (isset ($_POST ["materia"]) xor (isset ($_POST ["tariffa"])));
$rule3 = (isset ($_POST["materia"]) and isset ($_POST ["tariffa"]) and count ($_POST ["materia"]) != count ($_POST ["tariffa"]));
$rule4 = (isset($_FILES["propic"]) and $_FILES["propic"]["error"] != UPLOAD_ERR_OK and $_FILES["propic"]["error"] != UPLOAD_ERR_NO_FILE);
$rule5 = !$status;

if ($rule1 or $rule2 or $rule3 or $rule4 or $rule5) {
    // Il profilo non viene modificato perchè ci sono stati errori
    $db->rollback();
    header ("Location: ../pages/error.php?error_type=generic_modify_profile_error");
} else {
    $db->commit();
    $db->close();
    header ("Location: ../pages/profile.php?email={$_SESSION["email"]}");
}
?>