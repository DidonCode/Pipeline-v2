<!DOCTYPE html>
<html>

<head>
	<title>Connexion</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/x-icon" href="images/logos/logoButifyIcon.png">

	<link rel="stylesheet" type="text/css" href="css/Butify/login.css">
</head>

<body>
	<div class="logo">
		<img src="images/logos/logoButify.png" alt="Logo Butify">
		<span>Butify</span>
	</div>
	<div class="container" id="container">
		<div class="form-container sign-up-container">
			<form id="sign-up" method="POST">
				<h1>Créer un compte</h1>
				<br>
				<input type="text" name="sign-up-pseudo" placeholder="Pseudo" />
				<input type="email" name="sign-up-email" placeholder="E-mail" />
				<input type="password" name="sign-up-password" placeholder="Mot de passe" />
				<label class="checkWrap">
					<span class="check-label-artist">Créer un compte artiste</span>
					<input id="artist" type="checkbox" hidden>
					<span class="checkmark"></span>
				</label>
				<br>
				<button>Inscription</button>

			</form>
		</div>
		<div class="form-container sign-in-container">
			<form id="sign-in" method="POST">
				<h1>Connexion</h1>
				<br>
				<input type="email" name="sign-in-email" placeholder="E-mail" />
				<input type="password" name="sign-in-password" placeholder="Mot de passe" />
				<a href="#">Mot de passe oublié ?</a>
				<button>Connexion</button>
			</form>
		</div>
		<div class="overlay-container">
			<div class="overlay">
				<div class="overlay-panel overlay-left">
					<h1>Heureux de te revoir !</h1>
					<p>Reste connecté avec nous !</p>
					<button class="ghost" id="signIn">Connexion</button>
				</div>
				<div class="overlay-panel overlay-right">
					<h1>Salut, l'ami !</h1>
					<p>Viens commencer ton voyage auditif avec nous !</p>
					<button class="ghost" id="signUp">Inscription</button>
				</div>
			</div>
		</div>
	</div>
</body>

</html> 

<script src="js/session.js"></script>
<script src="js/login.js"></script>