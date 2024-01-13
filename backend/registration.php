<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start ();

	require 'db.php';

	$db = connect_to_db ();

	$valid_data = TRUE;

	// unset di tutte le variabili di sessione
	$_SESSION = [];

	if (!isset ($_POST ["firstname"]) or strlen (trim ($_POST["firstname"])) <= 0) {
		$_SESSION["firstname"] = true; // true = errore
		$valid_data = false;	
	}
	if (!isset ($_POST ["lastname"]) or strlen (trim ($_POST["lastname"])) <= 0) {
		$_SESSION["lastname"] = true;
		$valid_data = false;
	}
	// Valida in modo piuttosto buono le mail automaticamente
	if (!isset ($_POST ["email"]) or !(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) or existing_email ($db, $_POST["email"])) {
		$_SESSION["email"] = true;
		$valid_data = false;
	}
	if (!isset ($_POST ["pass"]) or strlen (trim($_POST["pass"])) < 8) {
		$_SESSION["pass"] = true;
		$valid_data = false;
	}
	if (!isset ($_POST ["confirm"]) or trim ($_POST["confirm"]) != trim ($_POST["pass"])) {
		$_SESSION["confirm"] = true;
		$valid_data = false;
	}
	if (!isset ($_POST ["role"]) or (strtolower($_POST ["role"]) != "studente" and strtolower($_POST ["role"]) != "tutor")) {
		$_SESSION["role"] = true;
		$valid_data = false;
	}

	// Controlla citta e online_presenza solo se l'utente è un tutor
	if ($_POST ["role"] == "tutor") {
		// Controlla se $_POST["citta"] è una città nel file ../res/citta.txt
		$valid_citta = false;
		if ($valid_data) {
			$file = fopen("../res/citta.txt", "r");
			while (!feof($file)) {
				$line = fgets($file);
				if (trim(strtolower($line)) == strtolower($_POST ["citta"])) {
					$valid_citta = true;
					break;
				}
			}
		}
	
		if (!$valid_citta) {
			$_SESSION["citta"] = true;
			$valid_data = false;
		}

		if (!isset ($_POST ["online"]) and !isset ($_POST ["presenza"])) {
			$_SESSION["online_presenza"] = true;
			$valid_data = false;
		}
	}

	if ($valid_data) {
		// Faccio la trim di tutti i campi passati in POST
		foreach ($_POST as $key => $value) {
			$_POST [$key] = trim ($value);
		}

		if (!prepared_query ($db,
				"INSERT INTO S5204959.utente (firstname, lastname, email, pass, role) VALUES (?, ?, ?, ?, ?)",
				[$_POST ["firstname"], $_POST ["lastname"], $_POST ["email"], password_hash ($_POST ["pass"], PASSWORD_DEFAULT), $_POST ["role"]])){

				header ("Location: ../pages/error.php?error_type=user_insertion_failed");

			}

		if ($_POST ["role"] == "tutor") {
			$_POST ["online"] = isset ($_POST ["online"]) ? 1 : 0;
			$_POST ["presenza"] = isset ($_POST ["presenza"]) ? 1 : 0;
			if (!prepared_query ($db,
					"INSERT INTO S5204959.tutor (email, citta, online, presenza) VALUES (?, ?, ?, ?)",
					[$_POST ["email"], $_POST ["citta"], $_POST ["online"], $_POST ["presenza"]])){

					header ("Location: ../pages/error.php?error_type=tutor_insertion_failed");

				}
		}
		header ("Location: ../pages/login_form.php");
	}
	else {
		header ("Location: ../pages/registration_form.php");
	}
?>