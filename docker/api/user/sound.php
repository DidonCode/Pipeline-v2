<?php

	require_once("../database/connect/database.php");
	require_once("../youtube/connect/youtube.php");

	include_once("../class/http.php");

	include_once("../class/sound.php");
	include_once("../class/playlist.php");

	include_once("../database/sound.php");

	include_once("../youtube/sound.php");

	include_once("../database/user/account.php");
	include_once("../database/user/sound.php");
	include_once("../database/user/playlist.php");

	include_once("../settings.php");
	include_once("../youtube/functions.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_POST)) == 2 AND count(array_keys($_FILES)) == 2 AND isset($_FILES['image'], $_POST['title'], $_FILES['audio'], $_POST['token'])){

		try{
			if(empty($_POST['token'])) throw new Exception("Argument not valid", 400);
			
			if(empty($_POST['title'])) throw new Exception("Error title not defined", 1);
			if(empty($_FILES['image'])) throw new Exception("Error image not defined", 2);
			if(empty($_FILES['audio'])) throw new Exception("Error audio not defined", 3);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
			if($user['artist'] == 0) throw new Exception("You are not artist", 400);

			$sound = DatabaseUserSound::createAudio($user, $_FILES['image'], $_POST['title'], $_FILES['audio']);

			Http::sendResponse(201, $sound);
		} catch(Exception $e){
			if($e->getCode() >= 1 AND $e->getCode() <= 7){
				Http::sendCustomError($e, 400);
				return;
			}

			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND count(array_keys($_FILES)) == 2 AND isset($_FILES['image'], $_FILES['video'], $_POST['title'], $_POST['token'])){

		try{
			if(empty($_POST['token'])) throw new Exception("Argument not valid", 400);

			if(empty($_POST['title'])) throw new Exception("Error title not defined", 1);
			if(empty($_FILES['image'])) throw new Exception("Error image not defined", 2);
			if(empty($_FILES['video'])) throw new Exception("Error video not defined", 3);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
			if($user['artist'] == 0) throw new Exception("You are not artist", 400);

			$sound = DatabaseUserSound::createVideo($user, $_FILES['image'], $_POST['title'], $_FILES['video']);

			Http::sendResponse(201, $sound);
		} catch(Exception $e){
			if($e->getCode() >= 1 AND $e->getCode() <= 7){
				Http::sendCustomError($e, 400);
				return;
			}

			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['playlist'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['playlist']) OR !filter_var($_POST['playlist'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$playlist = DatabaseUserPlaylist::get($user, $_POST['playlist']);

			if($playlist == null) throw new Exception("Playlist not exit", 404);
			
			$sounds = DatabaseUserSound::get($playlist);

			Http::sendResponse(200, $sounds);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['sound'], $_POST['token'])){

		try{
			if(empty($_POST['sound']) OR empty($_POST['token']) OR !filter_var($_POST['sound'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$sound = DatabaseSound::byId($_POST['sound']);

			if($sound['artist'] != $user['id']) throw new Exception("Your are not the owner");

			$result = DatabaseUserSound::delete($sound);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}


	Http::sendError(new Exception("Invalid request", 400));
