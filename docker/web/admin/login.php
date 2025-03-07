<!DOCTYPE html>
<html>
	<head>
		<title>Connexion</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="../images/logos/logoButifyIcon.png">
		
		<link rel="stylesheet" type="text/css" href="css/login.css">
	</head>

	<body>
		<div class="logo">
            <img src="../images/logos/logoButify.png" alt="Logo Butify">
            <span>Butify</span>
        </div>
		<div class="container  sign-in-container" id="container">
				<form id="sign-in" method="POST">
					<h1>Connexion</h1>
					<br>
					<input type="email" name="sign-in-email" placeholder="E-mail" />
					<input type="password" name="sign-in-password" placeholder="Mot de passe" />
					<a href="#">Mot de passe oublié ?</a>
					<button>Connexion</button>
				</form>
		</div>
	</body>
</html>

<script src="js/login.js"></script>