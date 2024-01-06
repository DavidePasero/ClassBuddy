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
    <meta charset="utf-8" name="viewport" content="width=device-width", initial-scale="1.0">
    <title>Sign-up</title>
	<link rel="stylesheet" type="text/css" href="../style/form.css">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
	<script type="module" defer src="../scripts/registration_form.js"></script>
</head>

<body>
	<?php echo print_header();?>
	<main>
		<div id="page_title">Registration form</div>
		<div>
			<?php
				if (isset($_SESSION["firstname"]) or isset($_SESSION["lastname"]) or isset($_SESSION["email"]) or
				isset($_SESSION["pass"]) or isset($_SESSION["confirm"])) {
				echo "<div class=\"error-message\">I campi contrassegnati in rosso sono invalidi</div>";
			}
			?>
			<form id="form" action="../backend/registration.php" method="POST" name="registration" class="registration-form">
				<div id="firstname_div form-element">
					<input type="text" id="firstname" name="firstname" autocomplete="given-name" placeholder="Nome" class=<?php if (isset($_SESSION["firstname"])) {echo "\"input-error\"";} else {echo "\"input-neutral form-element\"";}?> required>
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
					<input type="password" id="confirm" name="confirm" autocomplete="new-password" placeholder="Re-inserisci la password" class=<?php if (isset($_SESSION["confirm"])) {echo "\"input-error form-element\"";} else {echo "\"input-neutral form-element\"";}?> required>
				</div>

				<!-- Create a div for two radio buttons with the options "Studente" and "Tutor"-->
				<div id="role_div" class="radiobutton">
					<input type="radio" id="student" name="role" value="studente" checked>
					<label for="student">Studente</label>
					<input type="radio" id="tutor" name="role" value="tutor">
					<label for="tutor">Tutor</label>
				</div>
				
				<!-- Create a div for two checkboxes with the options "Online" and "In presenza"-->
				<div id="online_presenza_div" class="radiobutton">
					<input type="checkbox" id="online" name="online" value="online" checked>
					<label for="online">Online</label>
					<input type="checkbox" id="presenza" name="presenza" value="presenza">
					<label for="presenza">In presenza</label>
				</div>

				<!-- Create a div with a button named getCurrentLocation-->
				<div id="location_div">
					<!-- Text input for typing -->
					<input list="cittaDropdown" class="form-element" id="cittaInput" name="citta" placeholder="Digita o seleziona una città">

					<!-- Datalist (list of all the cities in italy) -->
					<datalist id="cittaDropdown">

					</datalist>
					<button type="button" class="form-element" id="getCurrentLocation" name="getCurrentLocation">Get current location</button>
				</div>

				<div id="submit_div">
					<input class="btn submit form-element" type="submit" id="submit" name="Submit" value="Invia">
				</div>

			</form>
		</div>
	</main>
</body>
</html>
