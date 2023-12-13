<?php
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	# Prevede che una sessione sia già stata startata
	function login_with_rememberme ($db) {
		if (isset ($_COOKIE ["rememberme"])) {
			$hash_login_code = hash ("sha256", $_COOKIE ["rememberme"]);
			$res = prepared_query ($db,
                "SELECT * FROM esercizi_saw.utenti WHERE login_code=? AND cookie_expiration > NOW()",
				[$hash_login_code]);

			if ($res->num_rows == 1) {
				$res_assoc = $res->fetch_assoc ();
				
				// Se il login code esiste e non è scaduto effettuo il login
				$_SESSION ["authenticated"] = true;
				$_SESSION ["email"] = $res_assoc ["email"];
				$_SESSION ["admin"] = is_admin ($db, $res_assoc ["email"]);
				
				$prova = $res_assoc ["email"];
			}
		}
	}
?>