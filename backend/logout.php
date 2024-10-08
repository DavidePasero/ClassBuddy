<?php
	if (PHP_SESSION_NONE == session_status())
        session_start ();
    require_once 'db.php';

    if (isset ($_SESSION ["authenticated"])) {
        // Cancello il cookie dal client settando il suo valore a una stringa vuota e la sua expiry date nel passato
        // Dopodichè cancello il cookie dal database
        if (isset ($_COOKIE ["rememberme"])) {
            setcookie("rememberme", "", time()-1);
            $db = connect_to_db ();
            $res = prepared_query ($db, "UPDATE S5204959.utente SET cookie_id=NULL, cookie_expire=NULL WHERE email=?;", [$_SESSION ["email"]]);
            if (!$res)
                echo_back_json_data (create_error_msg ("Errore nell'eliminazione del cookie."));
            unset ($_COOKIE ["rememberme"]);
        }

        session_unset ();
        session_destroy ();
    }

    header ("Location: ../pages/index.php");
?>