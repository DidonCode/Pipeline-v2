<?php
	
	require_once("database/connect/database.php");
	require_once("youtube/connect/youtube.php");

	include_once("class/http.php");

	include_once("class/sound.php");
	include_once("class/playlist.php");

	include_once("database/sound.php");
	include_once("database/playlist.php");
	include_once("youtube/sound.php");

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
				$sound = DatabaseSound::byId($_GET['id']);
			}else{
				$sound = YoutubeSound::byId($_GET['id']);
			}

			Http::sendResponse(200, $sound);
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

			$database = DatabaseSound::byTitle($_GET['title'], $page - 1, Settings::$PER_PAGE);
			$youtube = YoutubeSound::byTitle($_GET['title'], $page - 1, Settings::$PER_PAGE);

			$sounds = array(
				"database" => $database,
				"youtube" => $youtube,
				"page" => array(
					"page" => $page,
					"per_page" => Settings::$PER_PAGE
				)
			);

			Http::sendResponse(200, $sounds);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if((count(array_keys($_GET)) == 1 AND isset($_GET['artist'])) ||
       (count(array_keys($_GET)) == 2 AND isset($_GET['artist'], $_GET['page']))){

		try{
			if(empty($_GET['artist']) OR (isset($_GET['page']) AND empty($_GET['page']) AND !filter_var($_GET['page'], FILTER_VALIDATE_INT))) throw new Exception("Argument not valid", 400);

			$page = 1;
			if(isset($_GET['page'])) $page = $_GET['page'];

			if(is_numeric($_GET['artist'])){
				$sounds = DatabaseSound::byArtist($_GET['artist'], $page - 1, Settings::$PER_PAGE);
			}else{
				$sounds = YoutubeSound::byArtist($_GET['artist'], $page - 1, Settings::$PER_PAGE);
			}

			$sounds = array(
				"sounds" => $sounds,
				"page" => array(
					"page" => $page,
					"per_page" => Settings::$PER_PAGE
				)
			);

			Http::sendResponse(200, $sounds);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_GET)) == 1 AND isset($_GET['playlist'])){

		try{
			if(empty($_GET['playlist'])) throw new Exception("Argument not valid", 400);

			if(is_numeric($_GET['playlist'])){
				$sounds = array();

				$soundsData = DatabaseSound::byPlaylist($_GET['playlist']);

				foreach($soundsData as &$row) {
					if(is_numeric($row['id'])){
						array_push($sounds, DatabaseSound::byId($row['id']));
					}else{
						array_push($sounds, YoutubeSound::byId($row['id']));
					}
				}

				unset($row);
			}else{
				$sounds = YoutubeSound::byPlaylist($_GET['playlist']);
			}
			
			Http::sendResponse(200, $sounds);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	Http::sendError(new Exception("Invalid request", 400));