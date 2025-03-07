<?php

	require_once("../database/connect/database.php");
	require_once("../youtube/connect/youtube.php");

	include_once("../class/http.php");

	include_once("../class/sound.php");
	include_once("../class/playlist.php");
	include_once("../class/artist.php");

	include_once("../database/sound.php");
	include_once("../database/playlist.php");
	include_once("../database/artist.php");

	include_once("../youtube/sound.php");
	include_once("../youtube/playlist.php");
	include_once("../youtube/artist.php");

	include_once("../database/user/account.php");
	include_once("../database/user/activity.php");

	include_once("../settings.php");
	include_once("../youtube/functions.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_POST)) == 2 AND isset($_POST['type'], $_POST['token'])){

		if(empty($_POST['token'])) return;

		try {
			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = array();

			if($_POST['type'] == "recent"){
				$result = DatabaseUserActivity::getRecently($user);
			}

			if($_POST['type'] == "last"){
				$result = DatabaseUserActivity::getLast($user);
			}

			if($_POST['type'] == "artist"){
				$result = DatabaseUserActivity::getLastedArtistLikes($user);
			}

			if($_POST['type'] == "playlist"){
				$result = DatabaseUserActivity::getLastedPlaylistLikes($user);
			}

			if($_POST['type'] == "sound"){
				$result = DatabaseUserActivity::getLastedSoundLikes($user);
			}

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['sound'], $_POST['token'])){

		if(empty($_POST['token'])) return;

		try {
			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			DatabaseUserActivity::add($user, $_POST['sound']);

			Http::sendResponse(201, "");
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}


	Http::sendError(new Exception("Invalid request", 400));