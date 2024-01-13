<?php
    error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start ();
    require 'db.php';

    if (isset ($_SESSION ["authenticated"])) {
        // Cancello il cookie del client settando il suo valore a una stringa vuota e la sua expiry date nel passato
        // Dopodichè cancello il cookie dal database
        if (isset ($_COOKIE ["rememberme"])) {
            setcookie("rememberme", "", time()-1);
            $db = connect_to_db ();
            if (!prepared_query ($db, "UPDATE S5204959.utente SET cookie_id=NULL, cookie_expire=NULL WHERE email=?;", [$_SESSION ["email"]])){

                header ("Location: ../pages/error.php?error_type=invalid_request");

            }
            unset ($_COOKIE ["rememberme"]);
        }

        session_unset ();
        session_destroy ();
    }

    header ("Location: ../pages/index.php");
?>