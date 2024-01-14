<?php
function create_error_msg ($error) {
    return ["error" => $error];
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