<?php

	require_once("../database/connect/database.php");
	require_once("../youtube/connect/youtube.php");

	include_once("../class/http.php");

	include_once("../class/sound.php");
	include_once("../class/playlist.php");
	include_once("../class/artist.php");

	include_once("../database/user/account.php");
	include_once("../database/user/like.php");
	include_once("../database/user/playlist.php");

	include_once("../database/artist.php");
	include_once("../database/sound.php");
	include_once("../database/playlist.php");

	include_once("../youtube/artist.php");
	include_once("../youtube/sound.php");
	include_once("../youtube/playlist.php");

	include_once("../settings.php");
	include_once("../youtube/functions.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_POST)) == 3 AND isset($_POST['action'], $_POST['artist'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['artist']) OR empty($_POST['action']) OR 
			!filter_var($_POST['action'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			if(is_numeric($_POST['artist'])){
				$artist = DatabaseArtist::byId($_POST['artist']);
			}else{
				$artist = YoutubeArtist::byId($_POST['artist']);
			}

			if(!isset($artist)) throw new Exception("Artist not exit", 404);

			if($_POST['action'] == 3){
				$result = DatabaseUserLike::addArtist($user, $artist);
				Http::sendResponse(201, $result);
			}

			if($_POST['action'] == 2){
				$result = DatabaseUserLike::getArtist($user, $artist);
				Http::sendResponse(200, $result);
			}

			if($_POST['action'] == 1){
				$result = DatabaseUserLike::removeArtist($user, $artist);
				Http::sendResponse(200, $result);
			}
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 3 AND isset($_POST['action'], $_POST['sound'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['sound']) OR empty($_POST['action']) OR 
			!filter_var($_POST['action'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			if(is_numeric($_POST['sound'])){
				$sound = DatabaseSound::byId($_POST['sound']);
			}else{
				$sound = YoutubeSound::byId($_POST['sound']);
			}

			if(!isset($sound)) throw new Exception("Sound not exit", 404);

			if($_POST['action'] == 3){
				$result = DatabaseUserLike::addSound($user, $sound);
				Http::sendResponse(201, $result);
			}

			if($_POST['action'] == 2){
				$result = DatabaseUserLike::getSound($user, $sound);
				Http::sendResponse(200, $result);
			}

			if($_POST['action'] == 1){
				$result = DatabaseUserLike::removeSound($user, $sound);
				Http::sendResponse(200, $result);
			}
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 3 AND isset($_POST['action'], $_POST['playlist'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['playlist']) OR empty($_POST['action']) OR 
			!filter_var($_POST['action'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
			
			if(is_numeric($_POST['playlist'])){
				$playlist = DatabaseUserPlaylist::get($user, $_POST['playlist']);
			}else{
				$playlist = YoutubePlaylist::byId($_POST['playlist']);
			}

			if(!isset($playlist)) throw new Exception("Playlist not exit", 404);

			if($_POST['action'] == 3){
				if(!isset($playlist)) throw new Exception("Playlist not exit", 404);

				$result = DatabaseUserLike::addPlaylist($user, $playlist);
				Http::sendResponse(201, $result);
			}

			if($_POST['action'] == 2){
				$result = DatabaseUserLike::getPlaylist($user, $playlist);
				Http::sendResponse(200, $result);
			}

			if($_POST['action'] == 1){
				$result = DatabaseUserLike::removePlaylist($user, $playlist);
				Http::sendResponse(200, $result);
			}
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['type'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['type'])) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
			
			$likes = array();

			if($_POST['type'] == "playlist"){
				$likes = DatabaseUserLike::listPlaylist($user);
			}

			if($_POST['type'] == "sound"){
				$likes = DatabaseUserLike::listSound($user);
				// $likes = array();

				// for($i = 0; $i < 20; $i++){
				// 	array_push($likes, DatabaseSound::byId(1));
				// }
			}

			if($_POST['type'] == "artist"){
				$likes = DatabaseUserLike::listArtist($user);
			}

			Http::sendResponse(200, $likes);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	Http::sendError(new Exception("Invalid request", 400));
