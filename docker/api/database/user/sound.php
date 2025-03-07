<?php

	/**
	* @class DatabaseUserSound
	*
	* @brief Gestion des musiques de l'utilisateur dans la base de données
	*
	* @file sound.php
	*/
	class DatabaseUserSound {

		/**
		* @param $user Information de l'utilisateur
		* @param $image Image de la musique
		* @param $titre Titre de la musique
		* @param $audio Fichier audio de la musique
		* @return array Une classe sound sous la forme d'un tableau associatif
		*
		* @brief Crée une nouvelle musique et renvoie les données de la musique
		* @exception Exception Si l'extension de l'image de la musique n'est pas acceptée
		* @exception Exception Si l'extension du fichier audio de la musique n'est pas acceptée
		* @exception Exception Si la poids de l'image de la musique dépasse la limite autorisée
		* @exception Exception Si la poids du fichier audio de la musique dépasse la limite autorisée
		* @exception Exception Si le déplacement de l'image de la musique échoue
		* @exception Exception Si le déplacement du fichier audio de la musique échoue
		* @exception PDOException La requête échoue
		*/
		static function createAudio($user, $image, $title, $audio){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT MAX(id) FROM sound");
				$request->execute();
				$id = $request->fetchAll()[0][0] + 1;

				$imageFileName = basename($image['name']);
        		$imageFileExtension = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));
				$imageFileTmpPath = $image['tmp_name'];

				if(!in_array($imageFileExtension, Settings::$AUTHORIZED_IMAGE_EXT)) throw new Exception("Error image extension not accepted: ".$imageFileExtension, 4);
				if($image['size'] > Settings::$MAX_UPLOAD_SIZE) throw new Exception("Error image file too big size.", 5);

				$imageFilePath = "../../storage/sound/image/".$id.".".$imageFileExtension;
				$imageDatabasePath = "/storage/sound/image/".$id.".".$imageFileExtension;

				//---------------------\\

				$audioFileName = basename($audio['name']);
        		$audioFileExtension = strtolower(pathinfo($audioFileName, PATHINFO_EXTENSION));
				$audioFileTmpPath = $audio['tmp_name'];

				if(!in_array($audioFileExtension, Settings::$AUTHORIZED_AUDIO_EXT)) throw new Exception("Error audio file extension not accepted: ".$audioFileExtension, 6);
				if($audio['size'] > Settings::$MAX_UPLOAD_SIZE) throw new Exception("Error audio file too big size.", 7);

				$audioFilePath = "../../storage/sound/file/".$id.".".$audioFileExtension;
				$audioDatabasePath = "/storage/sound/file/".$id.".".$audioFileExtension;

				if(move_uploaded_file($audioFileTmpPath, $audioFilePath) AND move_uploaded_file($imageFileTmpPath, $imageFilePath)){
					try{
						$request = $pdoDatabase->prepare("INSERT INTO sound VALUES (?, ?, ?, ?, ?, ?)");
						$request->execute(array($id, $title, $user['id'], 0, $imageDatabasePath, $audioDatabasePath));
						
						$sound = new Sound(
							strval($id),
							$title,
							$user['id'],
							"0",
							$imageDatabasePath,
							$audioDatabasePath
						);
						
						return $sound->toString();
					}catch(PDOException $e){
						throw new Exception("Error to add upload sound. ".$e->getMessage(), 400);
					}
				}else{
					throw new Exception("Error to move uploaded file to folder.", 400);
				}
			} catch(PDOException $e){
				throw new Exception("Error to get number of sound. ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $image Image de la musique
		* @param $titre Titre de la musique
		* @param $video Fichier vidéo de la musique
		* @return array Une classe sound sous la forme d'un tableau associatif
		*
		* @brief Crée une nouvelle musique et renvoie les données de la musique
		* @exception Exception Si l'extension de l'image de la musique n'est pas acceptée
		* @exception Exception Si l'extension du fichier vidéo de la musique n'est pas acceptée
		* @exception Exception Si la poids de l'image de la musique dépasse la limite autorisée
		* @exception Exception Si la poids du fichier vidéo de la musique dépasse la limite autorisée
		* @exception Exception Si le déplacement de l'image de la musique échoue
		* @exception Exception Si le déplacement du fichier vidéo de la musique échoue
		* @exception PDOException La requête échoue
		*/
		static function createVideo($user, $image, $title, $video){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT MAX(id) FROM sound");
				$request->execute();
				$id = $request->fetchAll()[0][0] + 1;

				$imageFileName = basename($image['name']);
        		$imageFileExtension = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));
				$imageFileTmpPath = $image['tmp_name'];

				if(!in_array($imageFileExtension, Settings::$AUTHORIZED_IMAGE_EXT)) throw new Exception("Error image file extension not accepted: ".$imageFileExtension, 4);
				if($image['size'] > Settings::$MAX_UPLOAD_SIZE) throw new Exception("Error image file too big size.", 5);

				$imageFilePath = "../../storage/sound/image/".$id.".".$imageFileExtension;
				$imageDatabasePath = "/storage/sound/image/".$id.".".$imageFileExtension;

				//---------------------\\

				$fileName = basename($video['name']);
        		$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				$fileTmpPath = $video['tmp_name'];

				if(!in_array($fileExtension, Settings::$AUTHORIZED_VIDEO_EXT)) throw new Exception("Error video file extension not accepted: ".$fileExtension, 6);
				if($video['size'] > Settings::$MAX_UPLOAD_SIZE) throw new Exception("Error video file too big size.", 7);

				$filePath = "../../storage/sound/file/".$id.".".$fileExtension;
				$fileDatabasePath = "/storage/sound/file/".$id.".".$fileExtension;

				if(move_uploaded_file($fileTmpPath, $filePath) AND move_uploaded_file($imageFileTmpPath, $imageFilePath)){
					try{
						$request = $pdoDatabase->prepare("INSERT INTO sound VALUES (?, ?, ?, ?, ?, ?)");
						$request->execute(array($id, $title, $user['id'], 1, $imageDatabasePath, $fileDatabasePath));

						$sound = new Sound(
							strval($id),
							$title,
							$user['id'],
							"0",
							"",
							$fileDatabasePath
						);
						
						return $sound->toString();
					}catch(PDOException $e){
						throw new Exception("Error to add upload sound. ".$e->getMessage(), 400);
					}
				}else{
					throw new Exception("Error to move uploaded file to folder." , 400);
				}
			} catch(PDOException $e){
				throw new Exception("Error to get number of sound. ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $playlist Information de la playlist
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de toutes les musiques de la playlist
		* @exception Exception L'identifiant d'une musique n'est pas valide
		* @exception PDOException La requête échoue
		*/
		static function get($playlist){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT sound FROM playlist_sound WHERE playlist = ?");
				$request->execute(array($playlist['id']));
				$soundsData = $request->fetchAll();

				$sounds = array();

				foreach($soundsData as &$row) {
					if(is_numeric($row['sound'])){
						$sound = DatabaseSound::byId($row['sound']);
					}
					else{
						$sound = YoutubeSound::byId($row['sound']);
					}

					array_push($sounds, $sound);
				}

				unset($row);

				return $sounds;
			} catch(PDOException $e){
				throw new Exception("Error to get sounds in playlist: ".$playlist." ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $sound Information de la musique
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Supprime la musique correspondante
		* @exception PDOException La requête échoue
		*/
		static function delete($sound){
			global $pdoDatabase;

			try{
				try{
					if(!unlink("../..".parse_url($sound['image'], PHP_URL_PATH)) && !unlink("../..".parse_url($sound['link'], PHP_URL_PATH))) return false;

					$request = $pdoDatabase->prepare("DELETE FROM like_sound WHERE sound = ?");
					$request->execute(array($sound['id']));
					
					$request = $pdoDatabase->prepare("DELETE FROM playlist_sound WHERE sound = ?");
					$request->execute(array($sound['id']));

					$request = $pdoDatabase->prepare("DELETE FROM activity WHERE sound = ?");
					$request->execute(array($sound['id']));

					$request = $pdoDatabase->prepare("DELETE FROM sound WHERE id = ?");
					$request->execute(array($sound['id']));

					return true;
				} catch(Exception $e) {
					return false;
				}
			} catch(PDOException $e){
				throw new Exception("Error to delete sound: ".$sound['id'].". ".$e->getMessage(), 400);
			}
		}
	}
?>