<?php
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	// Prevede che una sessione sia già stata startata
	function login_with_rememberme ($db) {
		if (isset ($_COOKIE ["rememberme"])) {
			$hash_cookie_id = hash ("sha256", $_COOKIE ["rememberme"]);
			$res = prepared_query ($db,
                "SELECT * FROM S5204959.utente WHERE cookie_id=? AND cookie_expire > NOW()",
				[$hash_cookie_id]);

			if ($res->num_rows == 1) {
				$res_assoc = $res->fetch_assoc ();
				
				// Se il login code esiste e non è scaduto effettuo il login
				$_SESSION ["authenticated"] = true;
				$_SESSION ["email"] = $res_assoc ["email"];
				$_SESSION ["role"] = select_user_email ($db, $res_assoc ["email"]) ["role"];
			}
		}
	}
?>