<?php
require_once("../php/database.php");
require_once("../php/setting.php");
try
{
	$request_user = $pdoDatabase->prepare('SELECT COUNT(*) FROM user');
	$request_user->execute();
	$nb_user = $request_user->fetchAll();

	$request_artist = $pdoDatabase->prepare('SELECT COUNT(*) FROM user WHERE artist = 1');
	$request_artist->execute();
	$nb_artiste = $request_artist->fetchAll();

	$request_music = $pdoDatabase->prepare('SELECT COUNT(*) FROM sound');
	$request_music->execute();
	$nb_music = $request_music->fetchAll();

	$request_playlist = $pdoDatabase->prepare('SELECT COUNT(*) FROM playlist');
	$request_playlist->execute();
	$nb_playlist = $request_playlist->fetchAll();
}
catch (Exception $e)
{
    throw new Exception("Error.");
}
?>

<h1 class="titre_admin">Accueil</h1>
<div class="ensemble_card">
	<a href="/web/admin/utilisateur" onclick="route(event)" class="card_admin contrast-card primary-secondary clRounded1">
		<div>
			<i class="fa-solid fa-user fa-5x contrast-text"></i>
			<p class ="card_number contrast-text"><?php echo($nb_user[0][0])?></p>
			<p class ="card_text contrast-text">Utilisateurs</p>
		</div>
	</a>
	<a href="/web/admin/utilisateur" onclick="route(event)" class="card_admin contrast-card primary-secondary clRounded1">
		<div>
			<i class="fa-solid fa-user-music fa-5x contrast-text"></i>
			<p class ="card_number contrast-text"><?php echo($nb_artiste[0][0])?></p>
			<p class ="card_text contrast-text">Artistes</p>
		</div>
	</a>
	<a href="/web/admin/titre" onclick="route(event)" class="card_admin contrast-card primary-secondary clRounded1">
		<div>
			<i class="fa-solid fa-music fa-5x contrast-text"></i>
			<p class ="card_number contrast-text"><?php echo($nb_music[0][0])?></p>
			<p class ="card_text contrast-text">Titres</p>
		</div>
	</a>
	<a href="/web/admin/playlist" onclick="route(event)" class="card_admin contrast-card primary-secondary clRounded1">
		<div>
			<i class="fa-solid fa-list-music fa-5x contrast-text"></i>
			<p class ="card_number contrast-text"><?php echo($nb_playlist[0][0])?></p>
			<p class ="card_text contrast-text">Playlists</p>
		</div>
	</a>
</div>