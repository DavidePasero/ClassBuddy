<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start ();

	require 'db.php';

	$db = connect_to_db ();

	$valid_data = TRUE;

	// unset di tutte le variabili di sessione
	$_SESSION = array ();

	// Se anche una sola variabile non è settata, allora non è valida
	if (!isset ($_POST ["firstname"]) or !isset ($_POST ["lastname"]) or !isset ($_POST ["email"]) or !isset ($_POST ["pass"]) or !isset ($_POST ["confirm"]) or !isset ($_POST ["role"]) or !isset ($_POST ["citta"])) {
		$valid_data = FALSE;
	}
	else {
		// Trimma tutti i campi
		$_POST["firstname"] = trim($_POST["firstname"]);
		$_POST["lastname"] = trim($_POST["lastname"]);
		$_POST["email"] = trim($_POST["email"]);
		$_POST["pass"] = trim($_POST["pass"]);
		$_POST["confirm"] = trim($_POST["confirm"]);
		$_POST["role"] = trim($_POST["role"]);
		$citta = trim($_POST["citta"]);

		if (strlen ($_POST["firstname"]) <= 0) {
			$_SESSION["firstname"] = true; // true = errore
			$valid_data = FALSE;	
		}
		if (strlen ($_POST["lastname"]) <= 0) {
			$_SESSION["lastname"] = true;
			$valid_data = FALSE;
		}
		// Valida in modo piuttosto buono le mail automaticamente
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

		// Checks if $_POST["citta"] is a city in the file ../res/citta.txt
		$valid_citta = FALSE;
		if ($valid_data) {
			$file = fopen("../res/citta.txt", "r");
			while (!feof($file)) {
				$line = fgets($file);
				if (trim(strtolower($line)) == strtolower($citta)) {
					$valid_citta = TRUE;
					break;
				}
			}
		}
		// If city isn't valid, form isn't valid
		if (!$valid_citta) {
			$_SESSION["citta"] = true;
			$valid_data = FALSE;
		}
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