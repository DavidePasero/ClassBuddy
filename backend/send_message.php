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
	// Check if the chat is valid (Tutor to Student or Student to Tutor)
	$recipient = select_user_email ($db, $_POST ["recipient"]);

	if ($recipient === NULL) {
		echo "Destinatario non trovato";
		exit();
	}
	if ($recipient["email"] === $_SESSION ["email"]) {
		echo "Non puoi mandare messaggi a te stesso";
		exit();
	}
	
	// Check if the recipient has a different role as the sender
	if ($recipient["role"] !== $_SESSION ["role"]) {
		// Send message receive the following data: sender, recipient, message.
		// The sender is the user that is logged in, the recipient is the user that the sender is chatting with.
		$insert_msg = prepared_query ($db, 
		"INSERT INTO S5204959.messaggio (mittente, destinatario, timestamp, testo) VALUES (?, ?, ?, ?)",
		[$_SESSION ["email"], $_POST ["recipient"], date("Y-m-d H:i:s"), $_POST ["message"]]);
		// header ("Location: ../pages/chat.php?recipient=" . $_POST ["recipient"]);
		if (!$insert_msg) {
			echo "Invio fallito, aggiona la pagina e riprova";
			exit();
		}
	}
	else {
		echo "I messaggi possono essere inviati solo tra tutor e studenti";
		exit();
	}
?>