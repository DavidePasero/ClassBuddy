<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start ();

	require 'db.php';

	$db = connect_to_db ();

	$valid_data = TRUE;

	$_POST["pass"] = trim($_POST["pass"]);
	// unset di tutte le variabili di sessione
	$_SESSION = array ();

	if (strlen ($_POST["firstname"]) <= 0) {
		$_SESSION["firstname"] = true; // true = variable set = error -> it will be displayed in client side
		$valid_data = FALSE;	
	}
	if (strlen ($_POST["lastname"]) <= 0) {
		$_SESSION["lastname"] = true;
		$valid_data = FALSE;
	}
	# Valida in modo piuttosto buono le mail automaticamente
	if (!(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) or existing_email ($db, $_POST["email"])) {
		$_SESSION["email"] = true;
		$valid_data = FALSE;
	}
	if (strlen ($_POST["pass"]) < 8) {
		$_SESSION["pass"] = true;
		$valid_data = FALSE;
	}
	if ($_POST["confirm"] != $_POST["pass"]) {
		$_SESSION["confirm"] = true;
		$valid_data = FALSE;
	}
	if (strtolower($_POST ["role"]) != "studente" and strtolower($_POST ["role"]) != "tutor") {
		$_SESSION["role"] = true;
		$valid_data = FALSE;
	}

	if ($valid_data) {
		prepared_query ($db,
			"INSERT INTO S5204959.utente (firstname, lastname, email, pass, role) VALUES (?, ?, ?, ?, ?)",
			[$_POST ["firstname"], $_POST ["lastname"], $_POST ["email"], password_hash ($_POST ["pass"], PASSWORD_DEFAULT), $_POST ["role"]]);
		header ("Location: ../pages/login_form.php");
	}
	else {
		header ("Location: ../pages/registration_form.php");
	}
?>