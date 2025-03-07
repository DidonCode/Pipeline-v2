<?php

	/**
	* @class DatabaseUserAccount
	*
	* @brief Gestion du compte de l'utilisateur dans la base de données
	*
	* @file account.php
	*/
	class DatabaseUserAccount {

		/**
		* @param $email Adresse mail du compte
		* @param $password Mot de passe du compte
		* @return array Une classe user sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de l'utilisateur avec son token de connexion ou null si aucun compte n'est trouvé
		* @exception PDOException La requête échoue
		*/
		static function connect($email, $password){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT id, email, password, pseudo, grade, image, banner, public, artist FROM user WHERE email = ?");
				$request->execute(array($email));
				$usersData = $request->fetchAll();

				if(count($usersData) == 0) return null;

				foreach($usersData as &$userData) {
					if(password_verify($password, $userData['password'])){
						$token = DatabaseUserAccount::generateToken();
						$expire = new DateTime(date("Y")."-".date("m")."-".date("d"));
						$expire->modify("+".Settings::$SESSION_EXPIRE." days");

						$user = User::toClass($userData, DatabaseUserSubscription::get(array("id" => $userData["id"])));
						
						$request = $pdoDatabase->prepare("UPDATE user SET token = ?, expire = ? WHERE id = ?");
						$request->execute(array($token, $expire->format('Y-m-d'), $user->getId()));

						unset($userData);

						return array(
							"token" => $token,
							"user" => $user->toString()
						);
					}	
				}
			} catch(PDOException $e){
				throw new Exception("Error to find user for connection. ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $pseudo Pseudonyme du commpte
		* @param $email Adresse mail du compte
		* @param $password Mot de passe du compte
		* @param $artist Type du compte
		* @return array Une classe user sous la forme d'un tableau associatif
		*
		* @brief Crée un nouveau compte et renvoie les données de l'utilisateur avec son token de connexion
		* @exception Exception Si l'utilisateur est un artiste et que le pseudo est déjà utilisé
		* @exception Exception Si l'adresse mail est déjà utilisée
		* @exception Exception Le compte n'est pas retrouvé après sa création
		* @exception PDOException La requête échoue
		*/
		static function create($pseudo, $email, $password, $artist){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
				$request->execute(array($email));

				if($request->fetchAll()[0][0] >= 1) throw new Exception("Error this email is already used.", 1);

				if($artist == 1){
					$request = $pdoDatabase->prepare("SELECT COUNT(*) FROM user WHERE pseudo = ?");
					$request->execute(array($pseudo));

					if($request->fetchAll()[0][0] >= 1) throw new Exception("Error this pseudo is already used for artist.", 2);
				}

				$password = password_hash($password, PASSWORD_DEFAULT);

				$expire = new DateTime(date("Y")."-".date("m")."-".date("d"));
				$expire->modify("+".Settings::$SESSION_EXPIRE." days");

				$request = $pdoDatabase->prepare("INSERT INTO user (id, email, password, pseudo, artist, expire) VALUES (0, ?, ?, ?, ?, ?)");
				$request->execute(array($email, $password, $pseudo, $artist, $expire->format('Y-m-d')));

				$id = $pdoDatabase->lastInsertId();

				$request = $pdoDatabase->prepare("SELECT id, email, pseudo, grade, image, banner, public, artist FROM user WHERE id = ?");
				$request->execute(array($id));
				$userData = $request->fetchAll();
				
				if(count($userData) == 0) throw new Exception("Error to get user account after creation.", 400);

				$user = User::toClass($userData[0], null);

				$token = DatabaseUserAccount::generateToken();
				DatabaseUserAccount::updateToken($user->toString(), $token);

				return array(
					"token" => $token,
					"user" => $user->toString()
				);
			} catch(PDOException $e){
				throw new Exception("Error to create user. ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $token Chaine de caractères associée à la connexion
		* @return array Les données de l'utilisateur sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de l'utilisateur qui correspondent au token ou null
		* @exception PDOException La requête échoue
		*/
		static function get($token){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT id, email, pseudo, grade, image, banner, public, artist, expire FROM user WHERE token = ?");
				$request->execute(array($token));
				$userData = $request->fetchAll();

				if(count($userData) == 0) return null;

				$expire = new DateTime($userData[0]["expire"]);
				$today = new DateTime(date("Y")."-".date("m")."-".date("d"));

				$expired = date_diff($today, $expire);

				if(intval($expired->format('%R%a')) <= -1) throw new Exception('Session expired', 401);

				return $userData[0]; 
			} catch(PDOException $e){
				throw new Exception("Error to find if user exist. ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $id Identifiant de l'utilisateur
		* @return array Les données de l'utilisateur sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de l'utilisateur qui correspondant à l'identifiant ou null
		* @exception PDOException La requête échoue
		*/
		static function byId($id){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT id, email, pseudo, grade, image, banner, public, artist FROM user WHERE id = ?");
				$request->execute(array($id));
				$userData = $request->fetchAll();

				if(count($userData) == 0) return null;

				return $userData[0]; 
			} catch(PDOException $e){
				throw new Exception("Error to find if user exist. ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $token Nouveau token à attribuer
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Change le token de l'utilisateur par celui donné
		* @exception PDOException La requête échoue
		*/
		static function updateToken($user, $token){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("UPDATE user SET token = ? WHERE id = ?");
				$request->execute(array($token, $user['id']));
				$userData = $request->fetchAll();

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to update token for user: ".$user['id'].". ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $pseudo Nouveau pseudo de l'utilisateur
		* @param $email Nouvelle adresse mail de l'utilisateur
		* @param $password Nouveau mot de passe de l'utilisateur
		* @param $image Nouvelle image de profil de l'utilisateur
		* @param $public Nouvelle visibilité de l'utilisateur
		* @param $banner Nouvelle bannière du profil de l'utilisateur
		* @return array Une classe user sous la forme d'un tableau associatif filtré
		*
		* @brief Modifie les données de l'utilisateur et renvoie les nouvelles données de l'utilisateur
		* @exception Exception Si l'utilisateur est un artiste et que le pseudo est déjà utilisé
		* @exception Exception Si l'utilisateur est un artiste, l'email ne peut pas être changé
		* @exception Exception Si l'adresse mail est déjà utilisée
		* @exception Exception Si l'extension de l'image de profil n'est pas acceptée
		* @exception Exception Si l'extension de la bannière du profil n'est pas acceptée
		* @exception Exception Si la poids de l'image de profil dépasse la limite autorisée
		* @exception Exception Si la poids de la bannière du profil dépasse la limite autorisée
		* @exception PDOException La requête échoue
		*/
		static function edit($user, $pseudo, $email, $password, $image, $public, $banner){
			global $pdoDatabase;

			try{
				$sql = "UPDATE user SET";
				$values = array();

				if(isset($pseudo)) {
					if($user['artist'] == 1){
						$request = $pdoDatabase->prepare("SELECT id FROM user WHERE pseudo = ?");
						$request->execute(array($pseudo));
						$alreadyExit = $request->fetchAll();

						if(count($alreadyExit) > 0) throw new Exception("Error this pseudo is already used for artist.", 2);
					}

					$sql .= " pseudo = ?";
					$user['pseudo'] = $pseudo;
					array_push($values, $pseudo);
				}

				if(isset($email)) {
					//if($user['artist'] == 1) throw new Exception("Error artist can't change email.", 3);

					$request = $pdoDatabase->prepare("SELECT id FROM user WHERE email = ?");
					$request->execute(array($email));
					$alreadyExit = $request->fetchAll();

					if(count($alreadyExit) > 0) throw new Exception("Error this email is already used.", 1);

					$sql .= " email = ?";
					$user['email'] = $email;
					array_push($values, $email);
				}

				if(isset($password)) {
					$sql .= " password = ?";
					$password = password_hash($password, PASSWORD_DEFAULT);
					array_push($values, $password);
				}

				if($public != null AND $public == 0 OR $public == 1){
					$sql .= " public = ?";
					$user['public'] = $public;
					array_push($values, $public);
				}

				if(isset($image)) {
					if($user['image'] != "/storage/user/profile/default.png") unlink("../..".$user['image']);

					$imageFileName = basename($image['name']);
	        		$imageFileExtension = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));
					$imageFileTmpPath = $image['tmp_name'];
					
					if(!in_array($imageFileExtension, Settings::$AUTHORIZED_IMAGE_EXT)) throw new Exception("Error file extension not accepted: ".$imageFileExtension, 4);
					if($image['size'] > Settings::$MAX_UPLOAD_SIZE) throw new Exception("Error file too big size.", 5);

					$imageFilePath = "../../storage/user/profile/".$user['id'].".".$imageFileExtension;
					$imageDatabasePath = "/storage/user/profile/".$user['id'].".".$imageFileExtension;

					if(move_uploaded_file($imageFileTmpPath, $imageFilePath)){
						$sql .= " image = ?";
						$user['image'] = $imageDatabasePath;
						array_push($values, $imageDatabasePath);
					}else{
						$sql .= " image = ?";
						$user['image'] = "/storage/user/profile/default.png";
						array_push($values, "/storage/user/profile/default.png");
					}
				}

				if(isset($banner)) {
					if($user['banner'] != "/storage/user/banner/default.png") unlink("../..".$user['banner']);

					$bannerFileName = basename($banner['name']);
	        		$bannerFileExtension = strtolower(pathinfo($bannerFileName, PATHINFO_EXTENSION));
					$bannerFileTmpPath = $banner['tmp_name'];
					
					if(!in_array($bannerFileExtension, Settings::$AUTHORIZED_IMAGE_EXT)) throw new Exception("Error file extension not accepted: ".$bannerFileExtension, 4);
					if($banner['size'] > Settings::$MAX_UPLOAD_SIZE) throw new Exception("Error file too big size.", 5);

					$bannerFilePath = "../../storage/user/banner/".$user['id'].".".$bannerFileExtension;
					$bannerDatabasePath = "/storage/user/banner/".$user['id'].".".$bannerFileExtension;

					if(move_uploaded_file($bannerFileTmpPath, $bannerFilePath)){
						$sql .= " banner = ?";
						$user['banner'] = $bannerDatabasePath;
						array_push($values, $bannerDatabasePath);
					}else{
						$sql .= " banner = ?";
						$user['banner'] = "/storage/user/banner/default.png";
						array_push($values, "/storage/user/banner/default.png");
					}
				}

				if(count($values) == 0) throw new Exception("Error no argument passed to edit user ".$user['id'], 400);

				$sql .= " WHERE id = ?";
				array_push($values, $user['id']);

				$request = $pdoDatabase->prepare($sql);
				$request->execute($values);
				
				return User::toClass($user, DatabaseUserSubscription::get($user))->toString();
			} catch(PDOException $e){
				throw new Exception("Error to edit user ".$user['id'].". ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Modifie le token de l'utilisateur a null
		* @exception PDOException La requête échoue
		*/
		static function disconnect($user){
			global $pdoDatabase;

			try{
				return DatabaseUserAccount::updateToken($user, null);
			} catch(PDOException $e){
				throw new Exception("Error to find user. ".$e->getMessage(), 400);
			}
		}

		/**
		* @return string Une chaîne de caractères
		*
		* @brief Genere un token basé sur une chaîne de caractères aléatoire et hashé avec le timestamp de la demande
		*/
		static function generateToken(){
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';

			$length = random_int(64, 128);

			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[random_int(0, $charactersLength - 1)];
			}

			return hash_hmac('md5', time() * 1000, $randomString);
		}
	}