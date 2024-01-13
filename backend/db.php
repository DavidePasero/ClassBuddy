<?php
    function connect_to_db ($hostname='localhost', $user='S5204959', $password='c4c4p1p1', $database='S5204959') {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $db = new mysqli($hostname, $user, $password, $database);

        // Controllo se ci siamo connessi con successo
        if ($db->connect_errno) {
            throw new RuntimeException('mysqli connection error: ' . $db->connect_error);
        }
        
        // Impostiamo il charset da utilizzare con il database
        $db->set_charset('utf8mb4');
        // Controlliamo che set_charset sia andata a buon fine
        if ($db->errno) {
            throw new RuntimeException('mysqli error: ' . $db->error);
        }

        return $db;
    }

    // Esegue la query parametrica specificata
    function prepared_query ($db, $parametrized_query, $parameters) {
        $query = $db->prepare ($parametrized_query);
        if ($query === false)
            return false;

        $success = $query->execute ($parameters);
        if ($success === false)
            return false;

        $result = $query->get_result ();
        // Se la query era di tipo SELECT, SHOW, DESCRIBE o EXPLAIN, allora get_result restituisce un oggetto mysqli_result
        // Altrimenti restituisce true se la query è andata a buon fine, false altrimenti
        return $result === false ? $success : $result;
    }

    // Restituisce tutte le informazioni di un utente data la sua email
    function select_user_email ($db, $email) {
        return prepared_query ($db,
            "SELECT * FROM S5204959.utente WHERE email=?",
            [$email])->fetch_assoc ();
    }

    function existing_email ($db, $email) {
        $result_assoc = select_user_email ($db, $email);
        return isset ($result_assoc ["email"]);
    }

    // Restituisce true se la password inserita e quella salvata coincidono
    function verify_login ($db, $email, $pass) {
        $result_assoc = select_user_email ($db, $email);
        return password_verify ($pass, $result_assoc ["pass"]);
    }

    function check_login_code ($db, $hash_cookie_id) {
        $login_hash = hash ("sha256", $hash_cookie_id);
        $res = prepared_query ($db,
            "SELECT * FROM S5204959.utente WHERE cookie_id=?",
            [$login_hash]);
        return $res->num_rows == 0; 
    }

    function getAverageRating($db, $userEmail) {
        $avg = prepared_query ($db,
        "SELECT AVG(valutaz) as average FROM recensione WHERE tutor = ?",
        [$userEmail])->fetch_assoc() ["average"];

        return $avg;
    }
?>