<?php

	require_once("../database/connect/database.php");
	require_once("../youtube/connect/youtube.php");

	include_once("../class/http.php");

	include_once("../settings.php");

	include_once("../class/sound.php");
	include_once("../class/playlist.php");
	include_once("../class/artist.php");
	include_once("../class/user.php");

	include_once("../database/sound.php");
	include_once("../youtube/sound.php");

	include_once("../database/user/account.php");
	include_once("../database/user/playlist.php");
	include_once("../database/user/sound.php");

	include_once("../settings.php");
	include_once("../youtube/functions.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_POST)) == 4 AND isset($_POST['title'], $_POST['description'], $_POST['public'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['title']) OR
			($_POST['public'] != 0 AND $_POST['public'] != 1)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$playlist = DatabaseUserPlaylist::create($user, $_POST['title'], $_POST['description'], $_POST['public']);

			Http::sendResponse(201, $playlist);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 1 AND isset($_POST['token'])){

		try{
			if(empty($_POST['token'])) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$playlists = DatabaseUserPlaylist::list($user);

			Http::sendResponse(200, $playlists);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 3 AND count(array_keys($_FILES)) == 0 AND isset($_POST['id'], $_POST['action'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['action']) OR empty($_POST['id']) OR 
			!filter_var($_POST['action'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$playlist = false;

			if($_POST['action'] == 2){
				if($_POST['id'] === "liked"){
					$playlist = new Playlist(
						$_POST['id'],
						$user['id'],
						'Musiques "J\'aime"',
						'Vos titre "j\'aime"',
						Settings::$STORAGE_HOST_NAME."/storage/playlist/liked.png",
						0
					);
					$playlist = $playlist->toString();
					$playlist["permission"] = array(
						"owner" => 1
					);
				}
				else{
					$playlist = DatabaseUserPlaylist::get($user, $_POST['id']);
				}
			}

			if($_POST['action'] == 1){
				$playlist = DatabaseUserPlaylist::delete($user, $_POST['id']);
			}

			Http::sendResponse(200, $playlist);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 5 AND isset($_POST['id'], $_POST['title'], $_POST['description'], $_POST['public'], $_POST['token'])){
		
		try{
			if(empty($_POST['token']) OR empty($_POST['title']) OR empty($_POST['id']) OR ($_POST['public'] != 0 AND
			$_POST['public'] != 1) OR !filter_var($_POST['id'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = DatabaseUserPlaylist::edit($user, $_POST['id'], $_POST['title'], $_POST['description'], $_POST['public'], null);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND count(array_keys($_FILES)) == 1 AND isset($_POST['id'], $_FILES['image'], $_POST['token'])){
		
		try{
			if(empty($_POST['token']) OR empty($_FILES['image']) OR empty($_POST['id']) OR 
			!filter_var($_POST['id'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = DatabaseUserPlaylist::edit($user, $_POST['id'], null, null, null, $_FILES['image']);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			if($e->getCode() == 1 OR $e->getCode() == 2){
				Http::sendCustomError($e, 400);
				return;
			}
			
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 4 AND isset($_POST['id'], $_POST['sound'], $_POST['action'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['sound']) OR empty($_POST['action']) OR empty($_POST['id']) OR 
			!filter_var($_POST['action'], FILTER_VALIDATE_INT) OR !filter_var($_POST['id'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = true;

			if($_POST['action'] == 2){
				try{
					$sound = null;

					if(is_numeric($_POST['sound'])){
						$sound = DatabaseSound::byId($_POST['sound']);
					}else{
						$sound = YoutubeSound::byId($_POST['sound']);
					}

					$result = DatabaseUserPlaylist::addSound($user, $_POST['id'], $_POST['sound']);
				} catch(Exception $e){
					return false;
				}
			}

			if($_POST['action'] == 1){
				$result = DatabaseUserPlaylist::removeSound($user, $_POST['id'], $_POST['sound']);
			}

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 4 AND isset($_POST['id'], $_POST['collaborator'], $_POST['action'], $_POST['token'])){
		
		try{
			if(empty($_POST['id']) OR empty($_POST['collaborator']) OR empty($_POST['action']) OR empty($_POST['token']) OR 
			!filter_var($_POST['id'], FILTER_VALIDATE_INT) OR !filter_var($_POST['collaborator'], FILTER_VALIDATE_INT) OR 
			!filter_var($_POST['action'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);
			$collaborator = DatabaseUserAccount::byId($_POST['collaborator']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
			if(!isset($collaborator)) throw new Exception("Collaborator not exit", 404);
			if($user['id'] == $collaborator['id']) throw new Exception("Your are the owner", 400);

			$result = true;

			if($_POST['action'] == 2) {
				$result = DatabaseUserPlaylist::addCollaborator($user, $_POST['id'], $collaborator);
			}

			if($_POST['action'] == 1) {
				$result = DatabaseUserPlaylist::removeCollaborator($user, $_POST['id'], $collaborator);
			}
			
			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 3 AND isset($_POST['id'], $_POST['owner'], $_POST['token'])){

		try{
			if(empty($_POST['id']) OR empty($_POST['owner']) OR empty($_POST['token']) OR !filter_var($_POST['id'], FILTER_VALIDATE_INT) OR 
			!filter_var($_POST['owner'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);
			$owner = DatabaseUserAccount::byId($_POST['owner']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
			if(!isset($owner)) throw new Exception("Collaborator not exit", 404);
			if($user['id'] == $owner['id']) throw new Exception("Your are the owner", 400);

			$result = DatabaseUserPlaylist::removeCollaborator($owner, $_POST['id'], $user);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}
		
	if(count(array_keys($_POST)) == 4 AND isset($_POST['id'], $_POST['collaborator'], $_POST['modify'], $_POST['token'])){
		
		try{
			if(empty($_POST['id']) OR empty($_POST['collaborator']) OR ($_POST['modify'] != 0 AND $_POST['modify'] != 1) OR empty($_POST['token']) OR 
			!filter_var($_POST['id'], FILTER_VALIDATE_INT) OR !filter_var($_POST['collaborator'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);
			$collaborator = DatabaseUserAccount::byId($_POST['collaborator']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
			if(!isset($collaborator)) throw new Exception("Collaborator not exit", 404);

			$result = DatabaseUserPlaylist::editCollaborator($user, $_POST['id'], $collaborator, $_POST['modify']);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['id'], $_POST['token'])){
		try{
			if(empty($_POST['id']) OR empty($_POST['token']) OR !filter_var($_POST['id'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$collaborators = DatabaseUserPlaylist::getCollaborator($user, $_POST['id']);

			Http::sendResponse(200, $collaborators);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 5 AND isset($_POST['playlist'], $_POST['title'], $_POST['description'], $_POST['public'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['title']) OR 
			($_POST['public'] != 0 AND $_POST['public'] != 1) OR empty($_POST['playlist'])) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$newPlaylist = DatabaseUserPlaylist::create($user, $_POST['title'], $_POST['description'], $_POST['public']);
			$playlist = DatabaseUserPlaylist::get($user, $_POST['playlist']);

			DatabaseUserPlaylist::clone($user, $playlist, $newPlaylist);

			Http::sendResponse(201, $newPlaylist);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	Http::sendError(new Exception("Invalid request", 400));
