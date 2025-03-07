<?php
	
	require_once("database/connect/database.php");

	include_once("class/http.php");

	include_once("class/user.php");

	include_once("settings.php");

    include_once("database/member.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_GET)) == 1 AND isset($_GET['id'])){

		try{
            if(empty($_GET['id']) OR !filter_var($_GET['id'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

			$member = DatabaseMember::byId($_GET['id']);

			Http::sendResponse(200, $member);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if((count(array_keys($_GET)) == 1 AND isset($_GET['pseudo'])) ||
	   (count(array_keys($_GET)) == 2 AND isset($_GET['pseudo'], $_GET['page']))){

		try{
			if(empty($_GET['pseudo']) OR (isset($_GET['page']) AND empty($_GET['page']) AND !filter_var($_GET['page'], FILTER_VALIDATE_INT))) throw new Exception("Argument not valid", 400);

			$page = 0;
			if(isset($_GET['page'])) $page = $_GET['page'];

			$members = DatabaseMember::byPseudo($_GET['pseudo'], $page, Settings::$PER_PAGE);

			Http::sendResponse(200, $members);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	Http::sendError(new Exception("Invalid request", 400));