<?php
    function connect_to_db ($hostname='localhost', $user='my_user', $password='pass', $database='db') {
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

    function connect_to_utenti () {
        return connect_to_db ("localhost", "root", "cinquanta50cinque5", "esercizi_saw");
    }

    // Executes on $db and returns the result of the specified parametrized query
    function prepared_query ($db, $parametrized_query, $parameters) {
        $query = $db->prepare ($parametrized_query);
        $query->execute ($parameters);
        return $query->get_result ();
    }

    # Restituisce tutte le informazioni di un utente data la sua email
    function select_user_email ($db, $email) {
        return prepared_query ($db,
            "SELECT * FROM esercizi_saw.utenti WHERE email=?",
            [$email])->fetch_assoc ();
    }

    function existing_email ($db, $email) {
        $result_assoc = select_user_email ($db, $email);
        return isset ($result_assoc ["email"]);
    }

    # Restituisce true se la password inserita e quella salvata coincidono
    function verify_login ($db, $email, $pass) {
        $result_assoc = select_user_email ($db, $email);
        return password_verify ($pass, $result_assoc ["pass"]);
    }

    function is_admin ($db, $email) {
        return (select_user_email ($db, $email)["role"] == "admin");
    }

    function check_login_code ($db, $hash_login_code) {
        $login_hash = hash ("sha256", $hash_login_code);
        $res = prepared_query ($db,
            "SELECT * FROM esercizi_saw.utenti WHERE login_code=?",
            [$login_hash]);
        if ($res->num_rows > 0) {throw new Exception ("Login code already used");}
    }
?>