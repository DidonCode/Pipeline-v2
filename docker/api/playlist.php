<?php
	
	require_once("database/connect/database.php");
	require_once("youtube/connect/youtube.php");

	include_once("class/http.php");

	include_once("class/playlist.php");

	include_once("database/playlist.php");
	include_once("youtube/playlist.php");

	include_once("settings.php");
	include_once("youtube/functions.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_GET)) == 1 AND isset($_GET['id'])){

		try{
			if(empty($_GET['id'])) throw new Exception("Argument not valid", 400);

			if(is_numeric($_GET['id'])) {
				$playlist = DatabasePlaylist::byId($_GET['id']);
			}else{
				$playlist = YoutubePlaylist::byId($_GET['id']);
			}

			Http::sendResponse(200, $playlist);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if((count(array_keys($_GET)) == 1 AND isset($_GET['title'])) ||
	   (count(array_keys($_GET)) == 2 AND isset($_GET['title'], $_GET['page']))){
	
		try{
			if(empty($_GET['title']) OR (isset($_GET['page']) AND empty($_GET['page']) AND !filter_var($_GET['page'], FILTER_VALIDATE_INT))) throw new Exception("Argument not valid", 400);
			
			$page = 1;
			if(isset($_GET['page'])) $page = $_GET['page'];

			$database = DatabasePlaylist::byTitle($_GET['title'], $page - 1, Settings::$PER_PAGE);
			$youtube = YoutubePlaylist::byTitle($_GET['title'], $page - 1, Settings::$PER_PAGE);

			$playlists = array(
				"database" => $database,
				"youtube" => $youtube,
				"page" => array(
					"page" => $page,
					"per_page" => Settings::$PER_PAGE
				)
			);

			Http::sendResponse(200, $playlists);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if((count(array_keys($_GET)) == 1 AND isset($_GET['owner'])) ||
	   (count(array_keys($_GET)) == 2 AND isset($_GET['owner'], $_GET['page']))){
		
		try{
			if(empty($_GET['owner']) OR (isset($_GET['page']) AND empty($_GET['page']) AND !filter_var($_GET['page'], FILTER_VALIDATE_INT))) throw new Exception("Argument not valid", 400);

			$page = 1;
			if(isset($_GET['page'])) $page = $_GET['page'];

			if(is_numeric($_GET['owner'])){
				$playlists = DatabasePlaylist::byOwner($_GET['owner'], $page - 1, Settings::$PER_PAGE);
			}else{
				$playlists = YoutubePlaylist::byOwner($_GET['owner'], $page - 1, Settings::$PER_PAGE);
			}
			
			$playlists = array(
				"playlists" => $playlists,
				"page" => array(
					"page" => $page,
					"per_page" => Settings::$PER_PAGE
				)
			);

			Http::sendResponse(200, $playlists);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	Http::sendError(new Exception("Invalid request", 400));
