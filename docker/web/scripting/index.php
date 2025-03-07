<?php
	$address = "database";
	$username = "root";
	$password = "butify";

	$database = "butify";
	$hostName = "";

	try {
		$pdoDatabase = new PDO("mysql:host=".$address.";dbname=".$database, $username, $password);
	}catch(Exception $e) {
		throw new Exception("Database connection failed.");
	}

	function newUser(){
		global $pdoDatabase;

		$user = array(
			randomEmail(20),
			password_hash("butify", PASSWORD_DEFAULT),
			randomString(20),
			"/storage/user/profile/example/".randomImage(),
			"/storage/user/banner/example/".randomBanner(),
			randomInt(0, 1),
			randomInt(0, 1)
		);

		$request = $pdoDatabase->prepare("INSERT INTO user (email, password, pseudo, image, banner, artist, public) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$request->execute($user);
	}

	function newPlaylist(){
		global $pdoDatabase;

		$playlist = array(
			randomUser(),
			randomString(20),
			randomString(100),
			"/storage/playlist/example/".randomImage(),
			randomInt(0, 1)
		);

		$request = $pdoDatabase->prepare("INSERT INTO playlist (owner, title, description, image, public) VALUES (?, ?, ?, ?, ?)");
		$request->execute($playlist);
	}

	function newSound(){
		global $pdoDatabase;

		$sound = array(
			randomString(20),
			randomUser(),
			randomInt(0, 1),
			"/storage/sound/image/example/".randomImage(),
			"/storage/sound/file/example/".randomSoundFile()
		);

		$request = $pdoDatabase->prepare("INSERT INTO sound (title, artist, type, image, link) VALUES (?, ?, ?, ?, ?)");
		$request->execute($sound);
	}

	//---------------------------------\\

	function newLikeArtist(){
		global $pdoDatabase;

		$like = array(
			randomUser(),
			randomArtist()
		);

		$request = $pdoDatabase->prepare("INSERT INTO like_artist (user, artist) VALUES (?, ?)");
		$request->execute($like);
	}

	function newLikePlaylist(){
		global $pdoDatabase;

		$like = array(
			randomUser(),
			randomPublicPlaylist()
		);

		$request = $pdoDatabase->prepare("INSERT INTO like_playlist (user, playlist) VALUES (?, ?)");
		$request->execute($like);
	}

	function newLikeSound(){
		global $pdoDatabase;

		$like = array(
			randomUser(),
			randomSound()
		);

		$request = $pdoDatabase->prepare("INSERT INTO like_sound (user, sound) VALUES (?, ?)");
		$request->execute($like);
	}

	//---------------------------------\\

	function newCollaborator(){
		global $pdoDatabase;

		$collaborator = array(
			randomPlaylist(),
			randomUser(),
			randomInt(0, 1)
		);

		$request = $pdoDatabase->prepare("INSERT INTO playlist_collaborator (playlist, collaborator, modify) VALUES (?, ?, ?)");
		$request->execute($collaborator);
	}

	function newSoundPlaylist(){
		global $pdoDatabase;

		$playlistSound = array(
			randomPlaylist(),
			randomSound()
		);

		$request = $pdoDatabase->prepare("INSERT INTO playlist_sound (playlist, sound) VALUES (?, ?)");
		$request->execute($playlistSound);
	}

	//---------------------------------\\

	function newActivity(){
		global $pdoDatabase;

		for($i = 0; $i < 60; $i++){
			$date = new DateTime(date("Y")."-".date("m")."-".date("d"));
			$date->modify('-2 month');
			$date->modify('+'.$i.' day');

			for($j = 0; $j < 30; $j++){
				$activity = array(
					randomUser(),
					randomSound(),
					$date->format('Y-m-d')
				);
	
				$request = $pdoDatabase->prepare("INSERT INTO activity (user, sound, date) VALUES (?, ?, ?)");
				$request->execute($activity);
			}
		}
	}

	//---------------------------------\\

	function randomEmail($length) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';

	    for ($i = 0; $i < $length; $i++) {
	    	if(intval($length / 2) == $i){
	    		$randomString .= "@";
	    	}
	        else{
	        	$randomString .= $characters[random_int(0, $charactersLength - 1)];
	        }
	    }

		$randomString .= ".com";

	    return $randomString;
	}

	function randomString($length) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';

	    for ($i = 0; $i < $length; $i++) {
	    	$randomString .= $characters[random_int(0, $charactersLength - 1)];
	    }

	    return $randomString;
	}

	function randomInt($min, $max){
		return random_int($min, $max);
	}

	function randomArray($array){
		return $array[random_int(0, count($array) - 1)];
	}

	//---------------------------------\\

	function randomUser(){
		global $pdoDatabase;
		
		$request = $pdoDatabase->prepare("SELECT id FROM user");
		$request->execute();
		$users = $request->fetchAll();

		return randomArray($users)['id'];
	}

	function randomArtist(){
		global $pdoDatabase;
		
		$request = $pdoDatabase->prepare("SELECT id FROM user WHERE artist = 1");
		$request->execute();
		$artists = $request->fetchAll();

		return randomArray($artists)['id'];
	}

	function randomPlaylist(){
		global $pdoDatabase;
		
		$request = $pdoDatabase->prepare("SELECT id FROM playlist");
		$request->execute();
		$playlists = $request->fetchAll();

		return randomArray($playlists)['id'];
	}

	function randomPublicPlaylist(){
		global $pdoDatabase;
		
		$request = $pdoDatabase->prepare("SELECT id FROM playlist WHERE public = 1");
		$request->execute();
		$playlists = $request->fetchAll();

		return randomArray($playlists)['id'];
	}

	function randomSound(){
		global $pdoDatabase;
		
		$request = $pdoDatabase->prepare("SELECT id FROM sound");
		$request->execute();
		$sounds = $request->fetchAll();

		return randomArray($sounds)['id'];
	}

	//---------------------------------\\

	function randomSoundFile(){
		if(random_int(0, 1) == 0) {
			return random_int(1, 5).".mp3";
		}
		return random_int(1, 5).".mp4";
	}

	function randomImage(){
		return random_int(1, 10).".png";
	}

	function randomBanner(){
		return random_int(1, 10).".png";
	}

	//---------------------------------\\

	if(isset($_GET['user'])){
		for($i = 0; $i < $_GET['user']; $i++) newUser();
		header("Location: index.php");
	}

	if(isset($_GET['playlist'])){
		for($i = 0; $i < $_GET['playlist']; $i++) newPlaylist();
		header("Location: index.php");
	}

	if(isset($_GET['sound'])){
		for($i = 0; $i < $_GET['sound']; $i++) newSound();
		header("Location: index.php");
	}

	if(isset($_GET['like'])){
		$iteration = 10;

		$request = $pdoDatabase->prepare("SELECT id FROM user WHERE artist = 1");
		$request->execute();
		$artists = $request->fetchAll();

		if(count($artists) > 0) for($i = 0; $i < $iteration; $i++) newLikeArtist();

		$request = $pdoDatabase->prepare("SELECT id FROM playlist");
		$request->execute();
		$playlists = $request->fetchAll();

		if(count($playlists) > 0) for($i = 0; $i < $iteration; $i++) newLikePlaylist();

		$request = $pdoDatabase->prepare("SELECT id FROM sound");
		$request->execute();
		$sounds = $request->fetchAll();

		if(count($sounds) > 0) for($i = 0; $i < $iteration; $i++) newLikeSound();

		header("Location: index.php");
	}

	if(isset($_GET['playlist_relation'])){
		$iteration = 10;

		$request = $pdoDatabase->prepare("SELECT id FROM user");
		$request->execute();
		$users = $request->fetchAll();

		if(count($users) > 0) for($i = 0; $i < $iteration; $i++) newCollaborator();

		$request = $pdoDatabase->prepare("SELECT id FROM playlist");
		$request->execute();
		$playlists = $request->fetchAll();

		if(count($playlists) > 0) for($i = 0; $i < $iteration; $i++) newSoundPlaylist();

		header("Location: index.php");
	}

	if(isset($_GET['activity'])){
		newActivity();

		header("Location: index.php");
	}

	if(isset($_GET['admin'])){
		global $pdoDatabase;

		$request = $pdoDatabase->prepare("SELECT id FROM user WHERE email = 'admin@admin.com' AND grade = 1");
		$request->execute();
		$admin = $request->fetchAll();

		if(count($admin) == 1){
			echo "Le compte est déjà existant";
			return;
		}

		$user = array(
			"admin@admin.com",
			password_hash("butify", PASSWORD_DEFAULT),
			"admin",
			1,
			"/storage/user/profile/example/".randomImage(),
			"/storage/user/banner/example/".randomBanner(),
			randomInt(0, 1),
			randomInt(0, 1)
		);

		$request = $pdoDatabase->prepare("INSERT INTO user (email, password, pseudo, grade, image, banner, artist, public) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		$request->execute($user);

		header("Location: index.php");
	}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération d'éléments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        #container {
            margin-top: 20px;
        }
        .box {
            width: 100px;
            height: 100px;
            background-color: lightblue;
            margin: 5px;
            display: inline-block;
            line-height: 100px;
            font-weight: bold;
        }
		form{
			padding: 10px 0px;
		}
    </style>
</head>
<body>
    <h2>Créer des éléments dynamiquement</h2>

	<form>
    	<input type="number" id="numInput" min="1" name="user" placeholder="Nombre d'utilisateur">
    	<input type="submit" value="Générer">
    </form>

	<form>
    	<input type="number" id="numInput" min="1" name="playlist" placeholder="Nombre de playlist">
    	<input type="submit" value="Générer">
    </form>

	<form>
    	<input type="number" id="numInput" min="1" name="sound" placeholder="Nombre de musique">
    	<input type="submit" value="Générer">
    </form>

	<form>
    	<input type="submit" name="like" value="Crée des likes">
    </form>

	<form>
    	<input type="submit" name="playlist_relation" value="Crée des relations sur les playlists">
    </form>

	<form>
    	<input type="submit" name="activity" value="Crée de l'activité">
    </form>

	<form>
    	<input type="submit" name="admin" value="Crée le compte admin">
    </form>
</body>	
</html>
