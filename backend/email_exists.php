<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    require 'db.php';

    $db = connect_to_db ();

    $result = existing_email ($db, $email);
    echo $result ? 'true' : 'false';
} else {
    http_response_code(405);
    echo "Only POST requests are allowed.";
}
?>