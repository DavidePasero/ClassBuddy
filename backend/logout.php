<?php
    error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start ();
    require 'db.php';

    if (isset ($_SESSION ["authenticated"])) {
        // Cancello il cookie dal client settando il suo valore a una stringa vuota e la sua expiry date nel passato
        // Dopodichè cancello il cookie dal database
        if (isset ($_COOKIE ["rememberme"])) {
            setcookie("rememberme", "", time()-1);
            $db = connect_to_utenti ();
            prepared_query ($db, "UPDATE esercizi_saw.utenti SET login_code=NULL, cookie_expiration=NULL WHERE email=?;", [$_SESSION ["email"]]);
            unset ($_COOKIE ["rememberme"]);
        }

        session_unset ();
        session_destroy ();
    }

    header ("Location: ../pages/index.php");
?>