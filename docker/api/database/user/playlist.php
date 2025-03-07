<?php

	/**
	* @class DatabaseUserPlaylist
	*
	* @brief Gestion des playlists de l'utilisateur dans la base de données
	*
	* @file playlist.php
	*/
	class DatabaseUserPlaylist {

		/**
		* @param $user Information de l'utilisateur
		* @param $title Titre de la playlist
		* @param $description Description de la playlist
		* @param $public Visibilité de la playlist
		* @return array Une classe playlist sous la forme d'un tableau associatif
		*
		* @brief Crée une nouvelle playlist et renvoie les données de la playlist
		* @exception Exception La playlist n'est pas retrouvée après sa création
		* @exception PDOException La requête échoue
		*/
		static function create($user, $title, $description, $public){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("INSERT INTO playlist (owner, title, description, public) VALUES (?, ?, ?, ?)");
				$request->execute(array($user['id'], $title, $description, $public));
				$id = $pdoDatabase->lastInsertId();

				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ?");
				$request->execute(array($id));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0) throw new Exception("Error to get created playlist. ", 404);

				$playlist = new Playlist(
					$playlistData[0]['id'], 
					$playlistData[0]['owner'],
					$playlistData[0]['title'],
					$playlistData[0]['description'],
					$playlistData[0]['image'],
					$playlistData[0]['public']
				);

				return $playlist->toString();
			} catch(PDOException $e){
				throw new Exception("Error to create new playlist. ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de toutes les playlists de l'utilisateur avec celles où l'utilisateur est collaborateur
		* @exception PDOException La requête échoue
		*/
		static function list($user){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE owner = ?");
				$request->execute(array($user['id']));
				$playlistsData = $request->fetchAll();

				$playlists = array();

				foreach($playlistsData as &$row){
					$playlist = new Playlist(
						$row['id'], 
						$row['owner'],
						$row['title'],
						$row['description'],
						$row['image'],
						$row['public']
					);

					array_push($playlists, $playlist->toString());
				}

				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id IN (SELECT playlist FROM playlist_collaborator WHERE collaborator = ?)");
				$request->execute(array($user['id']));
				$playlistsData = $request->fetchAll();

				foreach($playlistsData as &$row){
					$playlist = new Playlist(
						$row['id'], 
						$row['owner'],
						$row['title'],
						$row['description'],
						$row['image'],
						$row['public']
					);

					array_push($playlists, $playlist->toString());
				}

				unset($row);

				return $playlists;
			} catch(PDOException $e){
				throw new Exception("Error to list playlist of user: ".$user['id'].". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @return array Une classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la playlist correspondant à l'identifiant avec les permissions de l'utilisateur dessus
		* @exception Exception L'identifiant ne correspond à aucune playlist
		* @exception PDOException La requête échoue
		*/
		static function get($user, $id){
			global $pdoDatabase;

			try{
				$permission = array();

				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ?");
				$request->execute(array($id));
				$playlistMainData = $request->fetchAll();

				if(count($playlistMainData) == 0) throw new Exception("Playlist not exit", 404);
				
				$isPublic = $playlistMainData[0]['public'] == 1;

				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0){
					$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id IN (SELECT playlist FROM playlist_collaborator WHERE playlist = ? AND collaborator = ?)");
					$request->execute(array($id, $user['id']));
					$playlistData = $request->fetchAll();

					if(count($playlistData) == 0){
						if(!$isPublic) return null;

						$playlistData = $playlistMainData;
					}else{
						$request = $pdoDatabase->prepare("SELECT modify FROM playlist_collaborator WHERE playlist = ? AND collaborator = ?");
						$request->execute(array($id, $user['id']));
						$permissionData = $request->fetchAll();

						$permission["modify"] = $permissionData[0]['modify'];
					}
				}else{
					$permission["owner"] = 1;
				}

				$playlist = new Playlist(
					$playlistData[0]['id'], 
					$playlistData[0]['owner'],
					$playlistData[0]['title'],
					$playlistData[0]['description'],
					$playlistData[0]['image'],
					$playlistData[0]['public']
				);

				$playlist = $playlist->toString();
				$playlist["permission"] = $permission;

				return $playlist;
			} catch(PDOException $e){
				throw new Exception("Error to get by id playlist of user: ".$user['id'].". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @param $sound Identifiant de la musique
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Ajoute une musique dans la playlist si elle appartient à l'utilisateur ou s'il a les permissions pour modifier
		* @exception PDOException La requête échoue
		*/
		static function addSound($user, $id, $sound){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0){
					$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id IN (SELECT playlist FROM playlist_collaborator WHERE playlist = ? AND collaborator = ? AND modify = 1)");
					$request->execute(array($id, $user['id']));
					$playlistData = $request->fetchAll();

					if(count($playlistData) == 0) return false;
				}
				
				
				$request = $pdoDatabase->prepare("SELECT id FROM playlist_sound WHERE playlist = ? AND sound = ?");
				$request->execute(array($id, $sound));
				$alreadyExist = $request->fetchAll();

				if(count($alreadyExist) > 0) return false;

				$request = $pdoDatabase->prepare("INSERT INTO playlist_sound (playlist, sound) VALUES (?, ?)");
				$request->execute(array($id, $sound));

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to add sound in playlist: ".$id.". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @param $sound Identifiant de la musique
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Supprime une musique dans la playlist si elle appartient à l'utilisateur ou s'il a les permissions pour modifier
		* @exception Exception L'identifiant de la playlist n'est pas valide
		* @exception Exception L'utilisateur n'est pas propriétaire
		* @exception PDOException La requête échoue
		*/
		static function removeSound($user, $id, $sound){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0){
					$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id IN (SELECT playlist FROM playlist_collaborator WHERE playlist = ? AND collaborator = ? AND modify = 1)");
					$request->execute(array($id, $user['id']));
					$playlistData = $request->fetchAll();

					if(count($playlistData) == 0) return false;
				}
				
				$request = $pdoDatabase->prepare("DELETE FROM playlist_sound WHERE playlist = ? AND sound = ?");
				$request->execute(array($id, $sound));

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to remove sound in playlist: ".$id.". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @param $title Nouveau titre de la playlist
		* @param $description Nouvelle description de la playlist
		* @param $public Nouvelle visivilité de la playlist
		* @param $image Nouvelle image de la playlist
		* @return array Une classe playlist sous la forme d'un tableau associatif
		*
		* @brief Modifie les données de la playlist et renvoie les nouvelles données si l'utilisateur est propriétaire
		* @exception Exception L'identifiant de la playlist n'est pas valide
		* @exception Exception Si l'extension de l'image de la playlist n'est pas acceptée
		* @exception Exception Si la poids de l'image de la playlist dépasse la limite autorisée
		* @exception PDOException La requête échoue
		*/
		static function edit($user, $id, $title, $description, $public, $image){
			global $pdoDatabase;

			try{
				$permission = array(
					"owner" => 1
				);

				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0) throw new Exception("Playlist not exit", 404);
				
				if(isset($image)){
					if($playlistData[0]['image'] != "/storage/playlist/default.png") unlink("../..".$playlistData[0]['image']);

					$imageFileName = basename($image['name']);
	        		$imageFileExtension = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));
					$imageFileTmpPath = $image['tmp_name'];

					if(!in_array($imageFileExtension, Settings::$AUTHORIZED_IMAGE_EXT)) throw new Exception("Error file extension not accepted: ".$imageFileExtension, 1);
					if($image['size'] > Settings::$MAX_UPLOAD_SIZE) throw new Exception("Error file too big size.", 2);

					$imageFilePath = "../../storage/playlist/".$id.".".$imageFileExtension;
					$imageDatabasePath = "/storage/playlist/".$id.".".$imageFileExtension;

					if(move_uploaded_file($imageFileTmpPath, $imageFilePath)){
						$request = $pdoDatabase->prepare("UPDATE playlist SET image = ? WHERE id = ?");
						$request->execute(array($imageDatabasePath, $id));

						$playlist = new Playlist(
							$playlistData[0]['id'], 
							$playlistData[0]['owner'],
							$playlistData[0]['title'],
							$playlistData[0]['description'],
							$imageDatabasePath,
							$playlistData[0]['public']
						);
		
						$playlist = $playlist->toString();
						$playlist["permission"] = $permission;
	
						return $playlist;
					}else{
						return false;
					}
				}else{
					$request = $pdoDatabase->prepare("UPDATE playlist SET title = ?, description = ?, public = ? WHERE id = ?");
					$request->execute(array($title, $description, $public, $id));

					$playlist = new Playlist(
						$playlistData[0]['id'], 
						$playlistData[0]['owner'],
						$title,
						$description,
						$playlistData[0]['image'],
						$public
					);
	
					$playlist = $playlist->toString();
					$playlist["permission"] = $permission;

					return $playlist;
				}
			} catch(PDOException $e){
				throw new Exception("Error to edit playlist: ".$id.". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $from Information de la playlist a copier
		* @param $to Information de la playlist a coller
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Clonage des musiques d'une playlist vers une autre playlist
		* @exception PDOException La requête échoue
		*/
		static function clone($user, $from, $to){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT sound FROM playlist_sound WHERE playlist = ?");
				$request->execute(array($from['id']));
				$soundsData = $request->fetchAll();

				foreach($soundsData as &$row) {
					DatabaseUserPlaylist::addSound($user, $to['id'], $row);
				}

				unset($row);

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to clone sounds of playlist: ".$from.". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Supprime les likes, collaborateur de la playlist et la playlist de l'utilisateur
		* @exception Exception L'identifiant de la playlist n'est pas valide
		* @exception Exception L'utilisateur n'est pas propriétaire
		* @exception PDOException La requête échoue
		*/
		static function delete($user, $id){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0) throw new Exception("Playlist not exit", 404);

				if($playlistData[0]['image'] != "/storage/playlist/default.png") unlink("../..".$playlistData[0]['image']);

				$request = $pdoDatabase->prepare("DELETE FROM playlist_sound WHERE playlist = ?");
				$request->execute(array($id));

				$request = $pdoDatabase->prepare("DELETE FROM playlist_collaborator WHERE playlist = ?");
				$request->execute(array($id));

				$request = $pdoDatabase->prepare("DELETE FROM playlist WHERE id = ?");
				$request->execute(array($id));

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to delete playlist: ".$id.". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @param $collaborator Information du collaborateur
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Ajoute un collaborateur à la playlist de l'utilisateur
		* @exception L'identifiant de la playlist n'est pas valide
		* @exception L'utilisateur n'est pas propriétaire
		* @exception La requête échoue
		*/
		static function addCollaborator($user, $id, $collaborator){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0) throw new Exception("Playlist not exit", 404);

				$request = $pdoDatabase->prepare("INSERT INTO playlist_collaborator VALUES ('0', ?, ?, 0)");
				$request->execute(array($id, $collaborator['id']));

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to delete playlist: ".$id.". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @param $collaborator Information du collaborateur
		* @param $modify Autorisation de modifier
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Modifie la permission de modifier d'un collaborateur sur la playlist de l'utilisateur
		* @exception Exception L'identifiant de la playlist n'est pas valide
		* @exception Exception L'utilisateur n'est pas propriétaire
		* @exception PDOException La requête échoue
		*/
		static function editCollaborator($user, $id, $collaborator, $modify){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0) throw new Exception("Playlist not exit", 404);

				$request = $pdoDatabase->prepare("UPDATE playlist_collaborator SET modify = ? WHERE playlist = ? AND collaborator = ?");
				$request->execute(array($modify, $id, $collaborator['id']));

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to delete playlist: ".$id.". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @return array Une liste de classe user sous la forme d'un tableau associatif filtré
		*
		* @brief Renvoie les données des utilisateurs qui sont collaborateur avec leur permission sur la playlist de l'utilisateur
		* @exception Exception L'identifiant de la playlist n'est pas valide
		* @exception Exception L'utilisateur n'est pas propriétaire
		* @exception PDOException La requête échoue
		*/
		static function getCollaborator($user, $id){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0) throw new Exception("Playlist not exit", 404);

				$request = $pdoDatabase->prepare("SELECT collaborator, modify FROM playlist_collaborator WHERE playlist = ?");
				$request->execute(array($id));
				$collaboratorsData = $request->fetchAll();

				$collaborators = array();

				foreach ($collaboratorsData as &$collaborator) {
					$userData = DatabaseUserAccount::byId($collaborator['collaborator']);

					array_push($collaborators, array("user" => User::toFilter($userData), "modify" => $collaborator['modify']));
				}

				unset($collaborator);

				return $collaborators;
			} catch(PDOException $e){
				throw new Exception("Error to list collaborators of playlist: ".$id.". ".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $id Identifiant de la playlist
		* @param $collaborator Information du collaborateur
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Supprime le collaborateur de la playlist de l'utilisateur
		* @exception Exception L'identifiant de la playlist n'est pas valide
		* @exception Exception L'utilisateur n'est pas propriétaire
		* @exception PDOException La requête échoue
		*/
		static function removeCollaborator($user, $id, $collaborator){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND owner = ?");
				$request->execute(array($id, $user['id']));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0) throw new Exception("Playlist not exit", 404);

				$request = $pdoDatabase->prepare("DELETE FROM playlist_collaborator WHERE playlist = ? AND collaborator = ?");
				$request->execute(array($id, $collaborator['id']));

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to delete playlist: ".$id.". ".$e->getMessage(), 500);
			}
		}
	}