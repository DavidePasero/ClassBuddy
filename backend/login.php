<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start ();

	require 'db.php';
	$db = connect_to_db ();

	$_SESSION = array ();

	if (isset ($_POST["email"]) and isset ($_POST["pass"]) and verify_login ($db, $_POST["email"], $_POST["pass"])) {
		$_SESSION ["authenticated"] = true;
		$_SESSION ["email"] = $_POST ["email"];
		$_SESSION ["admin"] = is_admin ($db, $_POST ["email"]);

		if (isset ($_POST ["rememberme"])) {
			insert_rememberme ($db);
		}

		header ("Location: ../pages/index.php");
	}
	else {
		$_SESSION ["error"] = true;
		header ("Location: ../pages/login_form.php");
	}

	function insert_rememberme ($db) {
		// Inserisce rememberme nel database
		$res = select_user_email ($db, $_SESSION["email"]);
		$login_code = hash ("sha256", $res ["pass"] . time ());
		$hash_login_code = hash ("sha256", $login_code);
		check_login_code ($db, $hash_login_code);

		// Scade in 15 giorni
		$expiration = time()+60*60*24*15;

		prepared_query ($db, "UPDATE esercizi_saw.utenti SET login_code=?, cookie_expiration=? WHERE email=?", [$hash_login_code, date ('Y-m-d H:i:s', $expiration), $_SESSION ["email"]]);
		// Set cookie rememberme
		setcookie ("rememberme", $login_code, $expires=$expiration);
	}
?>