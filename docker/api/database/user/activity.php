<?php

	/**
	* @class DatabaseUserActivity
	*
	* @brief Gestion de l'activité de l'utilisateur dans la base de données
	*
	* @file activity.php
	*/
	class DatabaseUserActivity {

		/**
		* @param $user Information de l'utilisateur
		* @param $sound Identifiant de la musique
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Ajoute la musique à l'activité de l'utilisateur
		* @exception PDOException La requête échoue
		*/
		static function add($user, $sound){
			global $pdoDatabase;

			try{
				$date = date("Y")."-".date("m")."-".date("d");

				$request = $pdoDatabase->prepare("INSERT INTO activity VALUES (0, ?, ?, ?)");
				$request->execute(array($user['id'], $sound, $date));

				return true;
			} catch(PDOException $e){
				throw new Exception("Error add sound in history for user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des musiques écoutées par l'utilisateur le mois dernier
		* @exception Exception L'identifiant d'une musique n'est pas valide
		* @exception PDOException La requête échoue
		*/
		static function getRecently($user){
			global $pdoDatabase;

			try{
				$date = new DateTime(date("Y")."-".date("m")."-".date("d"));
				$date->modify('-1 month');

				$request = $pdoDatabase->prepare("SELECT sound, COUNT(*) AS play_count FROM activity WHERE user = ? AND date >= ? GROUP BY sound ORDER BY play_count DESC LIMIT 10;");
				$request->execute(array($user['id'], $date->format('Y-m-d')));
				$activitiesData = $request->fetchAll();

				$sounds = array();

				foreach($activitiesData as &$row){
					if(is_numeric($row['sound'])){
						$sound = DatabaseSound::byId($row['sound']);
					}else{
						$sound = YoutubeSound::byId($row['sound']);
					}

					array_push($sounds, $sound);
				}

				unset($row);

				return $sounds;
			} catch(PDOException $e){
				throw new Exception("Error get recent listening for user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des musiques écoutées par l'utilisateur le deux mois dernier
		* @exception Exception L'identifiant d'une musique n'est pas valide
		* @exception PDOException La requête échoue
		*/
		static function getLast($user){
			global $pdoDatabase;

			try{
				$date = new DateTime(date("Y")."-".date("m")."-".date("d"));
				$date->modify('-2 month');

				$request = $pdoDatabase->prepare("SELECT sound, COUNT(*) AS play_count FROM activity WHERE user = ? AND date <= ? GROUP BY sound ORDER BY play_count DESC LIMIT 10;");
				$request->execute(array($user['id'], $date->format('Y-m-d')));
				$activitiesData = $request->fetchAll();

				$sounds = array();

				foreach($activitiesData as &$row){
					if(is_numeric($row['sound'])){
						$sound = DatabaseSound::byId($row['sound']);
					}else{
						$sound = YoutubeSound::byId($row['sound']);
					}

					array_push($sounds, $sound);
				}

				unset($row);

				return $sounds;
			} catch(PDOException $e){
				throw new Exception("Error get last listening for user: ".$user['id']." .".$e->getMessage(), 500);
				
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe artist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des derniers artistes aimés par l'utilisateur
		* @exception PDOException La requête échoue
		*/
		static function getLastedArtistLikes($user){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT artist FROM like_artist WHERE user = ? LIMIT 10");
				$request->execute(array($user['id']));
				$artistsData = $request->fetchAll();

				$artists = array();

				foreach($artistsData as &$row) {
					if(!is_numeric($row['artist'])){
						$artist = YoutubeArtist::byId($row['artist']);
					}else{
						$artist = DatabaseArtist::byId($row['artist']);
					}

					array_push($artists, $artist);
				}

				unset($row);

				return $artists;
			} catch(PDOException $e){
				throw new Exception("Error to get lasted artists likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des dernières playlists aimées par l'utilisateur
		* @exception PDOException La requête échoue
		*/
		static function getLastedPlaylistLikes($user){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT playlist FROM like_playlist WHERE user = ? LIMIT 10");
				$request->execute(array($user['id']));
				$playlistsData = $request->fetchAll();

				$playlists = array();

				foreach($playlistsData as &$row) {
					if(!is_numeric($row['playlist'])){
						$playlist = YoutubePlaylist::byId($row['playlist']);
					}else{
						$playlist = DatabasePlaylist::byId($row['playlist']);
					}

					array_push($playlists, $playlist);
				}

				unset($row);

				return $playlists;
			} catch(PDOException $e){
				throw new Exception("Error to get lasted playlists likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Information de l'utilisateur
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des dernières musiques aimées par l'utilisateur
		* @exception PDOException La requête échoue
		*/
		static function getLastedSoundLikes($user){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT sound FROM like_sound WHERE user = ? LIMIT 10");
				$request->execute(array($user['id']));
				$soundsData = $request->fetchAll();

				$sounds = array();

				foreach($soundsData as &$row) {
					if(!is_numeric($row['sound'])){
						$sound = YoutubeSound::byId($row['sound']);
					}else{
						$sound = DatabaseSound::byId($row['sound']);
					}

					array_push($sounds, $sound);
				}

				unset($row);

				return $sounds;
			} catch(PDOException $e){
				throw new Exception("Error to get lasted sounds likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}
	}