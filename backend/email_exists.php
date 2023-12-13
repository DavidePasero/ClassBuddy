<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the email value from the POST body
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    // Do something with the $email variable (e.g., store it in a database, perform validation, etc.)
    require 'db.php';
    $db = connect_to_utenti ();
    $result = existing_email ($db, $email);
    // For demonstration purposes, let's just echo the email value
    echo $result ? 'true' : 'false';
} else {
    // If the request method is not POST, return an error
    http_response_code(405); // Method Not Allowed
    echo "Only POST requests are allowed.";
}
?>