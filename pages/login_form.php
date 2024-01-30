<!DOCTYPE html>
<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start();
	require __DIR__ . '/../backend/page.php';
	require __DIR__ . '/../backend/db.php';
    
    $db = connect_to_db ();
    cookie_check ($db);
	$db->close ();

	if (isset ($_SESSION ["authenticated"])) 
		header ("Location: index.php");
?>

<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Login</title>
	<link rel="stylesheet" type="text/css" href="../style/form.css">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
	<script src="../scripts/login_form.js" defer></script>
</head>

<body>
	<?php echo print_header();?>
	<main>
		<div id="page_title">Sign-in form</div>
		<div>
			<?php
				if (isset($_SESSION["error"])) {
					echo "<div class=\"error-message\">I dati inseriti non sono corretti</div> <br>";
				}
			?>
			<form action="../backend/login.php" method="POST" name="registration" class="form">
				<div id="email_div">
					<input type="email" id="email" name="email" autocomplete="email" placeholder="Inserire email valida" class="form-element input-neutral">
				</div>

				<input type="password" id="pass" name="pass" autocomplete="current-password" placeholder="Almeno 8 caratteri" class="form-element input-neutral"> <br>
				
				<label class="checkbox-container" id="rememberme-container">
					<input type="checkbox" id="rememberme" name="rememberme">
					<span class="checkmark checkbox"></span>
					Remember me
				</label>

				<input class="btn submit form-element" type="submit" name="Submit" value="Invia">
			</form>
		</div>
	</main>
</body>
</html>
<!-- Aggiornando la pagina non voglio più vedere le segnalazioni di errore.
Il controllo serve per evitare che un utente autenticato che prova ad accedere
alla pagina di login/registration perda la sessione-->
<?php if(!isset ($_SESSION["authenticated"])) $_SESSION = array();?>