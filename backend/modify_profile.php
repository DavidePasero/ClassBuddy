<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start ();

if (!isset ($_SESSION ["authenticated"])) {
    header ("Location: login.php");
}

// Include the database configuration file  
require_once 'db.php'; 
$db = connect_to_db ();

$status = true;

$db->begin_transaction();

$insegnamenti = explode ("\n", file_get_contents("../res/insegnamenti.txt"));

try {
    if (isset ($_POST ["remove_insegnamento"])) {
        for ($i = 0; $i < count ($_POST ["remove_insegnamento"]); $i++) {
            // Delete all the tuples (tutor, materia, tariffa) where tutor is the current user
            prepared_query ($db, "DELETE FROM S5204959.insegnamento WHERE tutor=? AND materia=?;", [$_SESSION["email"], $_POST ["remove_insegnamento"][$i]]);
        }
    }

    if (isset($_FILES["propic"]) and $_FILES["propic"]["error"] == UPLOAD_ERR_OK) {
        // Get file info
        $propic = $_FILES["propic"];
        $propicTmpName = $propic["tmp_name"];
        // Read the file content
        $propicContent = file_get_contents($propicTmpName);
        // Get the file extension
        $fileExtension = pathinfo($propic["name"], PATHINFO_EXTENSION);
        // Allow certain file formats
        if ($fileExtension !== "jpg" and $fileExtension !== "jpeg" and $fileExtension !== "png" and $fileExtension !== "gif") {
            header ("Location: ../pages/error.php?error_type=invalid_file_extension");
        }
        
        // Insert image content into database 
        prepared_query ($db, "UPDATE S5204959.utente SET propic=?, propic_type=? WHERE email=?;", [$propicContent, $fileExtension, $_SESSION["email"]]); 
    }

    // Check if insegnamento array and tariffa array are passed by POST
    if (
            $_SESSION ["role"] === "tutor" and 
            isset ($_POST ["materia"]) and isset ($_POST ["tariffa"]) and
            count ($_POST ["materia"]) == count ($_POST ["tariffa"])
        ) {
        // Insert into insegnamento table all the tuples (tutor, materia, tariffa) where tutor is the current user
        // and materia and tariffa are each elements of the arrays passed by POST
        $tutor = $_SESSION ["email"];
        for ($i = 0; $i < count ($_POST ["materia"]); $i++) {
            $_POST["tariffa"][$i] = clamp ($_POST["tariffa"][$i], 1, 1000);
            // Check if materia is in $insegnamenti
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
    Error cases:
    1. no image received and no insegnamento array and tariffa array passed by POST
    2. materia passed but not tariffa or viceversa
    3. materia and tariffa passed but with different lengths
    4. image received but with error
    5. $status = false -> Queries not successful or materia not in $insegnamenti
*/
$rule1 = (!isset($_FILES["propic"]) and !isset ($_POST ["materia"]) and !isset ($_POST ["tariffa"]));
$rule2 = (isset ($_POST ["materia"]) xor (isset ($_POST ["tariffa"])));
$rule3 = (isset ($_POST["materia"]) and isset ($_POST ["tariffa"]) and count ($_POST ["materia"]) != count ($_POST ["tariffa"]));
$rule4 = (isset($_FILES["propic"]) and $_FILES["propic"]["error"] != UPLOAD_ERR_OK and $_FILES["propic"]["error"] != UPLOAD_ERR_NO_FILE);
$rule5 = !$status;

if ($rule1 or $rule2 or $rule3 or $rule4 or $rule5) {
    // Profile doesn't get modified if there are errors
    $db->rollback();
    header ("Location: ../pages/error.php?error_type=generic_modify_profile_error");
} else {
    $db->commit();
    $db->close();
    header ("Location: ../pages/profile.php?email={$_SESSION["email"]}");
}

function clamp ($val, $min, $max) {
    return max ($min, min ($max, $val));
}
?>