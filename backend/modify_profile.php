<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start ();

if (!isset ($_SESSION ["authenticated"])) {
    header ("Location: login.php");
}

// Include the database configuration file  
require_once 'db.php'; 
 
// If file upload form is submitted 
$status = $statusMsg = ''; 
if (isset($_FILES["propic"]) and $_FILES["propic"]["error"] == UPLOAD_ERR_OK) {
    // Get file info
    $propic = $_FILES["propic"];
    $propicTmpName = $propic["tmp_name"];
    // Read the file content
    $propicContent = file_get_contents($propicTmpName);
    // Get the file extension
    $fileExtension = pathinfo($propic["name"], PATHINFO_EXTENSION);
    
    $db = connect_to_db ();
    // Insert image content into database 
    $insert = prepared_query ($db, "UPDATE S5204959.utente SET propic=?, propic_type=? WHERE email=?;", [$propicContent, $fileExtension, $_SESSION["email"]]); 
        
    if ($insert)
        header ("Location: ../pages/myprofile.php"); 
    else
        header ("Location: ../pages/error.php?error_type=upload_propic_failed");
} else
    header ("Location: ../pages/error.php?error_type=no_img_received");
?>