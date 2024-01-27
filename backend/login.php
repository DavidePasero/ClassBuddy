<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);

	if (session_status() === PHP_SESSION_NONE)
		session_start();

	require_once 'db.php';
	$db = connect_to_db ();

	// Controllo se il client abbia il cookie rememberme
	if (isset ($_COOKIE ["rememberme"]) && $_COOKIE ["rememberme"] !== "") {
		// Non unsetto SESSION perchè il controllo del cookie viene fatto ogni volta che si carica una pagina
		check_rememberme ($db);
	}
	// Controllo le credenziali inserite dall'utente
	if ($_SERVER["REQUEST_METHOD"] === "POST" and !isset($_SESSION["authenticated"])) {
		$_SESSION = array ();
		check_credentials ($db);
	}
	

	function check_credentials ($db) {
		if (isset ($_POST["email"]) and isset ($_POST["pass"]) and verify_login ($db, $_POST["email"], $_POST["pass"])) {
			set_session ($_POST ["email"], select_user_email ($db, $_POST ["email"]) ["role"]);
	
			if (isset ($_POST ["rememberme"]))
				insert_rememberme ($db);
	
			header ("Location: ../pages/index.php");
		}
		else {
			$_SESSION ["error"] = true;
			header ("Location: ../pages/login_form.php");
		}
	}

	function check_rememberme ($db) {
		$hash_cookie_id = hash ("sha256", $_COOKIE ["rememberme"]);
		$res = prepared_query ($db,
			"SELECT * FROM S5204959.utente WHERE cookie_id=? AND cookie_expire > NOW()",
			[$hash_cookie_id]);

		if ($res->num_rows == 1) {
			$res_assoc = $res->fetch_assoc ();
			// Se il login code esiste e non è scaduto effettuo il login
			set_session ($res_assoc ["email"], $res_assoc ["role"]);
		}
	}

	function insert_rememberme ($db) {
		// Inserisce rememberme nel database
		$res = select_user_email ($db, $_SESSION["email"]);
		do {
			$cookie_id = hash ("sha256", $res ["pass"] . time ());
			$hash_cookie_id = hash ("sha256", $cookie_id); // hash del cookie_id per motivi di sicurezza
		} while (check_login_code ($db, $hash_cookie_id));

		// Scade in 15 giorni
		$expiration = time()+60*60*24*15;

		prepared_query ($db, "UPDATE S5204959.utente SET cookie_id=?, cookie_expire=? WHERE email=?", [$hash_cookie_id, date ('Y-m-d H:i:s', $expiration), $_SESSION ["email"]]);
		setcookie ("rememberme", $cookie_id, $expiration);
	}

	function set_session ($email, $role) {
		$_SESSION ["authenticated"] = true;
		$_SESSION ["email"] = $email;
		$_SESSION ["role"] = $role;
	}
?>