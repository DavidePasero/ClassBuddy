<?php
	if (PHP_SESSION_NONE == session_status())
		session_start ();

	require_once 'db.php';

	$db = connect_to_db ();

	$valid_data = TRUE;

	$_SESSION = array ();

	if (!isset ($_POST ["firstname"]) or strlen (trim ($_POST["firstname"])) <= 0) {
		$_SESSION["firstname"] = true; // true = errore
		$valid_data = false;	
	}
	if (!isset ($_POST ["lastname"]) or strlen (trim ($_POST["lastname"])) <= 0) {
		$_SESSION["lastname"] = true;
		$valid_data = false;
	}
	// Valida in modo piuttosto buono le mail automaticamente
	if (!isset ($_POST ["email"]) or !(filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) or existing_email ($db, trim($_POST["email"]))) {
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
	$role = isset ($_POST ["role"]) ? $_POST ["role"] : "studente";
	if (strtolower($role) != "studente" and strtolower($role) != "tutor") {
		$_SESSION["role"] = true;
		$valid_data = false;
	}

	// Controlla città e online/presenza solo se il ruolo è tutor
	if ($role == "tutor") {
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

		// Controlla se almeno uno dei due checkbox per lezioni online o in presenza è stato selezionato
		if (!isset ($_POST ["online"]) and !isset ($_POST ["presenza"])) {
			$_SESSION["online_presenza"] = true;
			$valid_data = false;
		}
	}

	if ($valid_data) {
		foreach ($_POST as $key => $value) {
			$_POST [$key] = trim ($value);
		}

		$db->begin_transaction ();

		try {
			$res = prepared_query ($db,
				"INSERT INTO S5204959.utente (firstname, lastname, email, pass, role) VALUES (?, ?, ?, ?, ?)",
				[$_POST ["firstname"], $_POST ["lastname"], $_POST ["email"], password_hash ($_POST ["pass"], PASSWORD_DEFAULT), $role]);
			if (!$res)
				throw new Exception ("Registrazione non andata a buon fine");

			// Se il ruolo è tutor, inserisce i dati nella tabella tutor
			if ($role == "tutor") {
				// Se la checkbox è selezionata, il valore è settato a true, altrimenti a false
				$_POST ["online"] = isset ($_POST ["online"]) ? 1 : 0;
				$_POST ["presenza"] = isset ($_POST ["presenza"]) ? 1 : 0;
				$res = prepared_query ($db,
						"INSERT INTO S5204959.tutor (email, citta, online, presenza) VALUES (?, ?, ?, ?)",
						[$_POST ["email"], $_POST ["citta"], $_POST ["online"], $_POST ["presenza"]]);
				if (!$res)
					throw new Exception ("Registrazione non andata a buon fine");
			}
		}
		catch (Exception $e) {
			$db->rollback ();
			header ("Location: ../pages/registration_form.php");
			exit ();
		}
		$db->commit ();
		header ("Location: ../pages/login_form.php");
	}
	else
		header ("Location: ../pages/registration_form.php");
?>