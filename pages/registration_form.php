<!DOCTYPE html>
<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	session_start();
	require 'page.php';
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
    <title>Sign-up</title>
	<link rel="stylesheet" type="text/css" href="../style/form.css">
	<link rel="stylesheet" type="text/css" href="../style/home.css">
	<script defer src="../scripts/registration_form.js"></script>
</head>

<body>
	<header>
        <h1 id="main_title">VR space</h1>
        <?php echo navbar();?>
    </header>
	<main>
		<div class="container_title">Sign-up form</div>
		<div class = "container">
			<?php
				if (isset($_SESSION["firstname"]) or isset($_SESSION["lastname"]) or isset($_SESSION["email"]) or
				isset($_SESSION["pass"]) or isset($_SESSION["confirm"])) {
				echo "<div class=\"error-message\">I campi contrassegnati in rosso sono invalidi</div>";
			}
			?>
			<form id="form" action="../backend/registration.php" method="POST" name="registration" class="registration-form">
				<div id="firstname_div">
					<input type="text" id="firstname" name="firstname" autocomplete="given-name" placeholder="Nome" class=<?php if (isset($_SESSION["firstname"])) {echo "\"input-error form-element\"";} else {echo "\"input-neutral form-element\"";}?> required>
				</div>

				<div id="lastname_div">
					<input type="text" id="lastname" name="lastname" autocomplete="family-name" placeholder="Cognome" class=<?php if (isset($_SESSION["lastname"])) {echo "\"input-error form-element\"";} else {echo "\"input-neutral form-element\"";}?> required>
				</div>

				<div id="email_div">
					<input type="email" id="email" name="email" autocomplete="email" placeholder="Inserire email valida" class=<?php if (isset($_SESSION["email"])) {echo "\"input-error form-element\"";} else {echo "\"input-neutral form-element\"";}?> required>
				</div>

				<div id="pass_div">
					<input type="password" id="pass" name="pass" autocomplete="new-password" placeholder="Almeno 8 caratteri" class=<?php if (isset($_SESSION["pass"])) {echo "\"input-error form-element\"";} else {echo "\"input-neutral form-element\"";}?> required>
				</div>

				<div id="confirm_div">
					<input type="password"id="confirm" name="confirm" autocomplete="new-password" placeholder="Re-inserisci la password" class=<?php if (isset($_SESSION["confirm"])) {echo "\"input-error form-element\"";} else {echo "\"input-neutral form-element\"";}?> required>
				</div>

				<div id="submit_div">
					<input type="submit" id="submit" name="Submit" value="Invia">
				</div>

			</form>
		</div>
	</main>
</body>
</html>
