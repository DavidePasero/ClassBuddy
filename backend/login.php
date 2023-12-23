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
		$cookie_id = hash ("sha256", $res ["pass"] . time ());
		$hash_cookie_id = hash ("sha256", $cookie_id);
		check_login_code ($db, $hash_cookie_id);

		// Scade in 15 giorni
		$expiration = time()+60*60*24*15;

		prepared_query ($db, "UPDATE S5204959.utente SET cookie_id=?, cookie_expire=? WHERE email=?", [$hash_cookie_id, date ('Y-m-d H:i:s', $expiration), $_SESSION ["email"]]);
		// Set cookie rememberme
		setcookie ("rememberme", $cookie_id, $expiration);
	}
?>