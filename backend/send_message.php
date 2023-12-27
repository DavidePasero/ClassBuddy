<?php
    error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start ();
	require 'db.php';
	$db = connect_to_db ();

	if ($_SERVER ["REQUEST_METHOD"] === "POST" && isset ($_SESSION ["authenticated"])) {
		// Check if the chat is valid (Tutor to Student or Student to Tutor)
		$recipient_role = select_user_email ($db, $_POST ["recipient"]) ["role"];

		// Check if the recipient has a different role as the sender
		if ($recipient_role !== $_SESSION ["role"]) {
			// Send message receive the following data: sender, recipient, message.
			// The sender is the user that is logged in, the recipient is the user that the sender is chatting with.
			$insert_msg = prepared_query ($db, 
			"INSERT INTO S5204959.messaggio (mittente, destinatario, timestamp, testo) VALUES (?, ?, ?, ?)",
			[$_SESSION ["email"], $_POST ["recipient"], date("Y-m-d H:i:s"), $_POST ["message"]]);
			header ("Location: ../pages/chat.php?recipient=" . $_POST ["recipient"]);
		}
	}
?>