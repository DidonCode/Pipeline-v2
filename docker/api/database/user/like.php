<?php

	/**
	* @class DatabaseUserLike
	*
	* @brief Gestion des likes de l'utilisateur dans la base de données
	*
	* @file like.php
	*/
	class DatabaseUserLike {

		/**
		* @param $user Information de l'utilisateur
		* @param $artist Infomation de l'artiste
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Ajoute l'artiste au like de l'utilisateur si celui-ci n'est pas déjà liké
		* @exception PDOException La requête échoue
		*/
		static function addArtist($user, $artist){
			global $pdoDatabase;

			try{
				if(DatabaseUserLike::getArtist($user, $artist)) return true;

				if($artist == $user['id']) return false;

				$request = $pdoDatabase->prepare("INSERT INTO like_artist VALUES ('0', ?, ?)");
				$request->execute(array($user['id'], $artist['id']));

			} catch(PDOException $e){
				throw new Exception("Error to add like artist: ".$artist." .".$e->getMessage(), 500);
			}

			return true;
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $artist Infomation de l'artiste
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Renvoie si l'utilisateur a liké l'artiste
		* @exception PDOException La requête échoue
		*/
		static function getArtist($user, $artist){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT id FROM like_artist WHERE user = ? AND artist = ?");
				$request->execute(array($user['id'], $artist['id']));
				$alreadyLiked = $request->fetchAll();

				return count($alreadyLiked) == 1;
			} catch(PDOException $e){
				throw new Exception("Error to add like artist: ".$artist." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe artist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des artistes que l'utilisateur a likés
		* @exception Exception L'identifiant d'un artist n'est pas valide
		* @exception PDOException La requête échoue
		*/
		static function listArtist($user){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT artist FROM like_artist WHERE user = ?");
				$request->execute(array($user['id']));
				$artistsData = $request->fetchAll();

				$artists = array();

				foreach($artistsData as &$row) {
					if(!is_numeric($row['artist'])){
						$artist = YoutubeArtist::byId($row['artist']);
					}
					else{
						$artist = DatabaseArtist::byId($row['artist']);
					}

					array_push($artists, $artist);
				}

				unset($row);

				return $artists;
			} catch(PDOException $e){
				throw new Exception("Error to list artist likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $artist Infomation de l'artiste
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Supprime l'artiste des likes de l'utilisateur
		* @exception PDOException La requête échoue
		*/
		static function removeArtist($user, $artist){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("DELETE FROM like_artist WHERE user = ? AND artist = ?");
				$request->execute(array($user['id'], $artist['id']));

			} catch(PDOException $e){
				throw new Exception("Error to remove like artist: ".$artist." .".$e->getMessage(), 500);
			}

			return true;
		}	

		/**
		* @param $user Information de l'utilisateur
		* @param $sound Infomation de la musique
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Ajoute de la musique au like de l'utilisateur si celui-ci ne la pas déjà liké
		* @exception PDOException La requête échoue
		*/
		static function addSound($user, $sound){
			global $pdoDatabase;

			try{
				if(DatabaseUserLike::getSound($user, $sound)) return true;

				$request = $pdoDatabase->prepare("INSERT INTO like_sound VALUES ('0', ?, ?)");
				$request->execute(array($user['id'], $sound['id']));

			} catch(PDOException $e){
				throw new Exception("Error to add like sound: ".$sound." .".$e->getMessage(), 500);
			}

			return true;
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $sound Infomation de la musique
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Renvoie si l'utilisateur a liké la musique
		* @exception PDOException La requête échoue
		*/
		static function getSound($user, $sound){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT id FROM like_sound WHERE user = ? AND sound = ?");
				$request->execute(array($user['id'], $sound['id']));
				$alreadyLiked = $request->fetchAll();

				return count($alreadyLiked) == 1;
			} catch(PDOException $e){
				throw new Exception("Error to add like artist: ".$sound." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des musiques que l'utilisateur a likés
		* @exception Exception L'identifiant d'une musique n'est pas valide
		* @exception PDOException La requête échoue
		*/
		static function listSound($user){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT sound FROM like_sound WHERE user = ?");
				$request->execute(array($user['id']));
				$soundsData = $request->fetchAll();

				$sounds = array();

				foreach($soundsData as &$row) {
					if(!is_numeric($row['sound'])){
						$sound = YoutubeSound::byId($row['sound']);
					}
					else{
						$sound = DatabaseSound::byId($row['sound']);
					}

					array_push($sounds, $sound);
				}

				unset($row);

				return $sounds;
			} catch(PDOException $e){
				throw new Exception("Error to list sound likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $sound Infomation de la musique
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Supprime la musique des likes de l'utilisateur
		* @exception PDOException La requête échoue
		*/
		static function removeSound($user, $sound){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("DELETE FROM like_sound WHERE user = ? AND sound = ?");
				$request->execute(array($user['id'], $sound['id']));
				
			} catch(PDOException $e){
				throw new Exception("Error to remove like sound: ".$sound." .".$e->getMessage(), 500);
			}

			return true;
		}	

		/**
		* @param $user Information de l'utilisateur
		* @param $playlist Infomation de la playlist
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Ajoute la playlist au like de l'utilisateur si celui-ci ne la pas déjà liké
		* @exception PDOException La requête échoue
		*/
		static function addPlaylist($user, $playlist){
			global $pdoDatabase;

			try{
				if(DatabaseUserLike::getPlaylist($user, $playlist)) return true;

				if($playlist['owner'] == $user['id']) return false;

				$request = $pdoDatabase->prepare("INSERT INTO like_playlist VALUES ('0', ?, ?)");
				$request->execute(array($user['id'], $playlist['id']));

				return true;
			} catch(PDOException $e){
				throw new Exception("Error to add like playlist: ".$playlist." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $playlist Infomation de la playlist
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Renvoie si l'utilisateur a liké la playlist
		* @exception PDOException La requête échoue
		*/
		static function getPlaylist($user, $playlist){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT id FROM like_playlist WHERE user = ? AND playlist = ?");
				$request->execute(array($user['id'], $playlist['id']));
				$alreadyLiked = $request->fetchAll();

				return count($alreadyLiked) == 1;
			} catch(PDOException $e){
				throw new Exception("Error to add like artist: ".$playlist." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des playlists que l'utilisateur a likés
		* @exception Exception L'identifiant d'une playlists n'est pas valide
		* @exception PDOException La requête échoue
		*/
		static function listPlaylist($user){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT playlist FROM like_playlist WHERE user = ?");
				$request->execute(array($user['id']));
				$playlistsData = $request->fetchAll();

				$playlists = array();

				foreach($playlistsData as &$row) {
					if(!is_numeric($row['playlist'])){
						$playlist = YoutubePlaylist::byId($row['playlist']);
					}
					else{
						$playlist = DatabaseUserPlaylist::get($user, $row['playlist']);
					}
					
					if(isset($playlist)) array_push($playlists, $playlist);
				}

				unset($row);

				return $playlists;
			} catch(PDOException $e){
				throw new Exception("Error to list playlist likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @param $playlist Infomation de la playlist
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Supprime la playlist des likes de l'utilisateur
		* @exception PDOException La requête échoue
		*/
		static function removePlaylist($user, $playlist){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("DELETE FROM like_playlist WHERE user = ? AND playlist = ?");
				$request->execute(array($user['id'], $playlist['id']));

			} catch(PDOException $e){
				throw new Exception("Error to remove like playlist: ".$playlist." .".$e->getMessage(), 500);
			}

			return true;
		}

	}