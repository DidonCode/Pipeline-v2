<?php

	require_once("../database/connect/database.php");

	include_once("../class/http.php");

	include_once("../class/user.php");
	include_once("../class/subscription.php");

	include_once("../settings.php");
	
	include_once("../database/user/account.php");
	include_once("../database/user/subscription.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

	if(count(array_keys($_POST)) == 2 AND isset($_POST['email'], $_POST['password'])){

		try{
			if(empty($_POST['email']) OR empty($_POST['password']) OR 
			!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) throw new Exception("Argument not valid", 400);
		
			$token = DatabaseUserAccount::connect($_POST['email'], $_POST['password']);

			Http::sendResponse(201, $token);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 3 AND isset($_POST['pseudo'], $_POST['email'], $_POST['password']) OR 
	   count(array_keys($_POST)) == 4 AND isset($_POST['pseudo'], $_POST['email'], $_POST['password'], $_POST['artist'])){

		try{
			if(empty($_POST['pseudo'] OR empty($_POST['email']) OR empty($_POST['password']) OR filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) OR 
			(isset($_POST['artist']) AND !filter_var($_POST['artist'], FILTER_VALIDATE_INT)))) throw new Exception("Argument not valid", 400);

			$artist = 0;
			if(isset($_POST['artist'])) $artist = 1;

			$token = DatabaseUserAccount::create($_POST['pseudo'], $_POST['email'], $_POST['password'], $artist);

			Http::sendResponse(201, $token);
		} catch(Exception $e){
			if($e->getCode() == 1 OR $e->getCode() == 2){
				Http::sendCustomError($e, 400);
				return;
			}
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 1 AND count(array_keys($_FILES)) == 1 AND isset($_FILES['image'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_FILES['image'])) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = DatabaseUserAccount::edit($user, null, null, null, $_FILES['image'], null, null);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['pseudo'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['pseudo'])) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = DatabaseUserAccount::edit($user, $_POST['pseudo'], null, null, null, null, null);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			if($e->getCode() == 2){
				Http::sendCustomError($e, 400);
				return;
			}
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['email'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['email']) OR !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = DatabaseUserAccount::edit($user, null, $_POST['email'], null, null, null, null);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			if($e->getCode() == 1){
				Http::sendCustomError($e, 400);
				return;
			}
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['password'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['password'])) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = DatabaseUserAccount::edit($user, null, null, $_POST['password'], null, null, null);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 2 AND isset($_POST['public'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR ($_POST['public'] != 0 AND $_POST['public'] != 1)) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = DatabaseUserAccount::edit($user, null, null, null, null, $_POST['public'], null);

			Http::sendResponse(200, $result);
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	if(count(array_keys($_POST)) == 1 AND count(array_keys($_FILES)) == 1 AND isset($_FILES['banner'], $_POST['token'])){

		try{
			if(empty($_POST['token']) OR empty($_FILES['banner'])) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			$result = DatabaseUserAccount::edit($user, null, null, null, null, null, $_FILES['banner']);

			Http::sendResponse(200, $result);
		} catch(Exception $e){			
			Http::sendError($e);
		}

		return;
	}
	
	if(count(array_keys($_POST)) == 2 AND isset($_POST['token'], $_POST['type'])){

		try{
			if(empty($_POST['token']) OR empty($_POST['type'])) throw new Exception("Argument not valid", 400);

			$user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);

			if($_POST['type'] == "new"){
				$token = DatabaseUserAccount::generateToken();
				DatabaseUserAccount::updateToken($user, $token);

				$user = array(
					"token" => $token, 
					"user" => User::toClass($user, DatabaseUserSubscription::get($user))->toString()
				);

				Http::sendResponse(201, $user);
				return;
			}

			if($_POST['type'] == "disconnect"){
				DatabaseUserAccount::disconnect($user);
				Http::sendResponse(200, "disconnect");
				return;
			}
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

	Http::sendError(new Exception("Invalid request", 400));
