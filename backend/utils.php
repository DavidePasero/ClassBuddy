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

function get_data_uri ($image, $image_type) {
    if ($image !== NULL) {
        // Crea un data uri per la propic
        $imageData = base64_encode($image);
        $imageType = $image_type;
        return "data:image/{$imageType};base64,{$imageData}";
    }
    return "../img/defaultUser.jpg";
}
?>