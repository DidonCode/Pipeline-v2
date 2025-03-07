<?php

	require_once("../database/connect/database.php");
	require_once("../youtube/connect/youtube.php");

	include_once("../class/http.php");

	include_once("../class/sound.php");
	include_once("../class/playlist.php");
	include_once("../class/artist.php");

	include_once("../database/sound.php");
	include_once("../database/artist.php");

	include_once("../youtube/sound.php");
	include_once("../youtube/artist.php");

	include_once("../database/user/account.php");
	include_once("../database/user/activity.php");

	include_once("../settings.php");
	include_once("../youtube/functions.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_POST)) == 2 AND isset($_POST['sound'], $_POST['token'])){

		if(empty($_POST['token'])) return;

		try {
			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$activity = DatabaseUserActivity::getLast($user);

			$lastArtistLiked = DatabaseUserActivity::getLastedArtistLikes($user);

			foreach($lastArtistLiked as &$row) {
				if(is_numeric($row['id'])){
					$soundsLastArtist = DatabaseSound::byArtist($row['id'], 0, 5);
				}else{
					$soundsLastArtist = YoutubeSound::byArtist($row['id'], 0, 5);
				}
			}

			unset($row);

			if(is_numeric($_POST['sound'])){
				$sound = DatabaseSound::byId($_POST['sound']);

				$soundsArtist = DatabaseSound::byArtist($sound['artist'], 0, 5);
			}else{
				$sound = YoutubeSound::byId($_POST['sound']);

				$soundsArtist = YoutubeSound::byArtist($sound['artist'], 0, 5);
			}

			$result = array();
			if(isset($activity)) $result = array_merge($result, $activity);
			if(isset($soundsArtist)) $result = array_merge($result, $soundsArtist);
			if(isset($soundsLastArtist)) $result = array_merge($result, $soundsLastArtist);

			shuffle($result);
			array_unshift($result, $sound);
			$result = array_values(array_unique($result, SORT_REGULAR));

			Http::sendResponse(201, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	Http::sendError(new Exception("Invalid request", 400));