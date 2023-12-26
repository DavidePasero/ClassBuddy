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
    
    $db = connect_to_db ();
    // Insert image content into database 
    $insert = prepared_query ($db, "UPDATE S5204959.utente SET propic=? WHERE email=?;", [$propicContent, $_SESSION["email"]]); 
        
    echo $insert; 
} else{
    $statusMsg = 'Please select an image file to upload.'; 
    echo $statusMsg;
} 
#header ("Location: ../pages/myprofile.php");
?>