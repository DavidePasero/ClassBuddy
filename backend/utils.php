<?php
function create_error_msg ($error) {
    $error_msg = array ();
    $error_msg ["error"] = $error;
    return json_encode ($error_msg);
}

function clamp ($min, $max, $value) {
    return max ($min, min ($max, $value));
}

function echo_back_json_data ($data) {
    header ("Content-Type: application/json");
    echo json_encode ($data);
    exit();
}
?>