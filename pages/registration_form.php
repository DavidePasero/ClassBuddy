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
    <title>Sign-up</title>
	<link rel="stylesheet" type="text/css" href="../style/form.css">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
	<script type="module" src="../scripts/registration_form.js"></script>
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
			<form id="form" action="../backend/registration.php" method="POST" name="registration" class="form">
				<div id="firstname_div">
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

				<div id="role_div" class="radiobutton">
					<label class="checkbox-container">
						<input type="radio" id="student" name="role" value="studente" checked>
						<span class="checkmark radio"></span>
						Studente
					</label>

					<label class="checkbox-container">
						<input type="radio" id="tutor" name="role" value="tutor">
						<span class="checkmark radio"></span>
						Tutor
					</label>
				</div>
				
				<div id="online_presenza_div" class="radiobutton">
					<label class="checkbox-container">
						<input type="checkbox" id="online" name="online" value="online" checked>
						<span class="checkmark checkbox"></span>
						Online
					</label>

					<label class="checkbox-container">
						<input type="checkbox" id="presenza" name="presenza" value="presenza">
						<span class="checkmark checkbox"></span>
						Presenza
					</label>
				</div>

				<div id="location_div">
					<input list="cittaDropdown" class="form-element input-list" id="cittaInput" name="citta" placeholder="Digita o seleziona una città">

					<datalist id="cittaDropdown" class="data-list">
					</datalist>

					<button type="button" class="btn icon-button form-element" id="getCurrentLocation" name="getCurrentLocation">
						La tua posizione
						<img src="../res/icons/location.svg" alt="Location icon">
					</button>
				</div>

				<div id="submit_div">
					<input class="btn submit form-element" type="submit" id="submit" name="Submit" value="Invia">
				</div>

			</form>
		</div>
	</main>
</body>
</html>
