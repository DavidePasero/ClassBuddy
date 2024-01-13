<?php
    error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start ();
	require 'db.php';
	$db = connect_to_db ();

	if (!$_SERVER ["REQUEST_METHOD"] === "POST" or !isset ($_SESSION ["authenticated"])) {
		echo "Devi essere autenticato per mandare un messaggio";
		exit();
	}
	if (!isset ($_POST ["recipient"]) or !isset ($_POST ["message"]) or empty ($_POST ["recipient"]) or empty ($_POST ["message"])) {
		echo "Devi inserire un destinatario e un messaggio";
		exit();
	}
	// Controlla se la chat è valida (Tutor a Studente o Studente a Tutor)
	$recipient = select_user_email ($db, $_POST ["recipient"]);

	if ($recipient === NULL) {
		echo "Destinatario non trovato";
		exit();
	}
	if ($recipient["email"] === $_SESSION ["email"]) {
		echo "Non puoi mandare messaggi a te stesso";
		exit();
	}
	
	if ($recipient["role"] !== $_SESSION ["role"]) {
		$insert_msg = prepared_query ($db, 
		"INSERT INTO S5204959.messaggio (mittente, destinatario, timestamp, testo) VALUES (?, ?, ?, ?)",
		[$_SESSION ["email"], $_POST ["recipient"], date("Y-m-d H:i:s"), $_POST ["message"]]);
		// header ("Location: ../pages/chat.php?recipient=" . $_POST ["recipient"]);
		if (!$insert_msg) {
			echo "Invio fallito, aggiona la pagina e riprova";
			exit();
		}
		echo "OK";
	}
	else {
		echo "I messaggi possono essere inviati solo tra tutor e studenti";
		exit();
	}
?>