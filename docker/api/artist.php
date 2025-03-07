<?php
	
	require_once("database/connect/database.php");
	require_once("youtube/connect/youtube.php");

	include_once("class/http.php");

	include_once("class/artist.php");

	include_once("database/artist.php");
	include_once("youtube/artist.php");

	include_once("settings.php");
	include_once("youtube/functions.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_GET)) == 1 AND isset($_GET['id'])){

		try{
			if(empty($_GET['id'])) throw new Exception("Argument not valid", 400);

			if(is_numeric($_GET['id'])){
				$artist = DatabaseArtist::byId($_GET['id']);
			}else{
				$artist = YoutubeArtist::byId($_GET['id']);
			}

			Http::sendResponse(200, $artist);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if((count(array_keys($_GET)) == 1 AND isset($_GET['pseudo'])) ||
	   (count(array_keys($_GET)) == 2 AND isset($_GET['pseudo'], $_GET['page']))){
	
		try{
			if(empty($_GET['pseudo']) OR (isset($_GET['page']) AND empty($_GET['page']) AND !filter_var($_GET['page'], FILTER_VALIDATE_INT))) throw new Exception("Argument not valid", 400);

			$page = 1;
			if(isset($_GET['page'])) $page = $_GET['page'];

			$database = DatabaseArtist::byPseudo($_GET['pseudo'], $page - 1, Settings::$PER_PAGE);
			$youtube = YoutubeArtist::byPseudo($_GET['pseudo'], $page - 1, Settings::$PER_PAGE);

			$artists = array(
				"database" => $database,
				"youtube" => $youtube,
				"page" => array(
					"page" => $page,
					"per_page" => Settings::$PER_PAGE
				)
			);

			Http::sendResponse(200, $artists);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	Http::sendError(new Exception("Invalid request", 400));
