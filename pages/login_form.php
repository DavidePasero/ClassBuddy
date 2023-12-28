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
    <title>Login</title>
	<link rel="stylesheet" type="text/css" href="../style/form.css">
	<link rel="stylesheet" type="text/css" href="../style/home.css">
	<script defer src="../scripts/login_form.js"></script>
</head>

<body>
	<header>
        <h1 id="main_title">ClassBuddy</h1>
        <?php echo navbar();?>
    </header>
	<main>
		<div class="container_title_2">Sign-in form</div>
		<div>
			<?php
				if (isset($_SESSION["error"])) {
					echo "<div class=\"error-message\">I dati inseriti non sono corretti</div> <br>";
				}
			?>
			<form action="../backend/login.php" method="POST" name="registration" class="registration-form">
				<div id="email_div">
					<input type="email" id="email" name="email" autocomplete="email" placeholder="Inserire email valida" class="form-element input-neutral">
				</div>

				<input type="password" id="pass" name="pass" autocomplete="current-password" placeholder="Almeno 8 caratteri" class="form-element input-neutral"> <br>
				
				<label class="checkbox-container">Remember me
					<input type="checkbox" id="rememberme" name="rememberme">
					<span class="checkmark"></span>
				</label>

				<input type="submit" name="Submit" value="Invia">
			</form>
		</div>
	</main>

</body>
</html>
