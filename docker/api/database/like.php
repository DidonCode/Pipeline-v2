<?php

	/**
	* @class DatabaseLike
	*
	* @brief Récupération des likes dans la base de données pour les utilisateurs publics
	*
	* @file like.php
	*/
    class DatabaseLike {

		/**
		* @param $user Identifiant de l'utilisateur
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des musiques aimées par l'utilisateur
		* @exception Exception L'utilisateur est en privé
		* @exception Exception L'identifiant d'une musique n'est pas valide
		* @exception PDOException La requête échoue
		*/
        static function likedSound($user){
			global $pdoDatabase;

			try{
				if($user['public'] == 0) throw new Exception("Error user: ".$user['id']." is private.", 400);

				$request = $pdoDatabase->prepare("SELECT sound FROM like_sound WHERE user = ?");
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
				throw new Exception("Error to list sound likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Identifiant de l'utilisateur
		* @return array Une liste de classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des playlists aimées par l'utilisateur
		* @exception Exception L'utilisateur est en privé
		* @exception Exception L'identifiant d'une la playlist n'est pas valide
		* @exception PDOException La requête échoue
		*/
        static function likedPlaylist($user){
			global $pdoDatabase;

			try{
				if($user['public'] == 0) throw new Exception("Error user: ".$user['id']." is private.", 400);

				$request = $pdoDatabase->prepare("SELECT playlist FROM like_playlist WHERE user = ?");
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
				throw new Exception("Error to list playlist likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @param $user Identifiant de l'utilisateur
		* @return array Une liste de classe artist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des artistes aimées par l'utilisateur
		* @exception Exception L'utilisateur est en privé
		* @exception Exception L'identifiant d'un artiste n'est pas valide
		* @exception PDOException La requête échoue
		*/
        static function likedArtist($user){
			global $pdoDatabase;

			try{
				if($user['public'] == 0) throw new Exception("Error user: ".$user['id']." is private.", 400);
				
				$request = $pdoDatabase->prepare("SELECT artist FROM like_artist WHERE user = ?");
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
				throw new Exception("Error to list artist likes from user: ".$user['id']." .".$e->getMessage(), 500);
			}
		}

		/**
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des musiques les plus écoutées
		* @exception PDOException La requête échoue
		*/
        static function mostListened(){
			global $pdoDatabase;
			
			try{
				$request = $pdoDatabase->prepare("SELECT sound, COUNT(*) AS play_count FROM activity WHERE sound REGEXP '^[0-9]+$' GROUP BY sound ORDER BY play_count DESC LIMIT 10");
				$request->execute();
				$soundsData = $request->fetchAll();

				$sounds = array();

				foreach($soundsData as &$row){
					$sound = DatabaseSound::byId($row['sound']);

					array_push($sounds, $sound);
				}

				unset($row);

				return $sounds;
			} catch(PDOException $e){
				throw new Exception("Error get most listened sounds".$e->getMessage(), 500);
			}
		}

		/**
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des musiques les moins écoutées
		* @exception PDOException La requête échoue
		*/
		static function leastListened(){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT sound, COUNT(*) AS play_count FROM activity WHERE sound REGEXP '^[0-9]+$' GROUP BY sound ORDER BY play_count ASC LIMIT 10;");
				$request->execute();
				$soundsData = $request->fetchAll();

				$sounds = array();

				foreach($soundsData as &$row){
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
				throw new Exception("Error get least listened sounds".$e->getMessage(), 500);
			}
		}

		/**
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des musiques les plus aimées
		* @exception PDOException La requête échoue
		*/
		static function mostLikedSound(){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT ls.sound, COUNT(*) AS like_count FROM like_sound ls JOIN sound s ON ls.sound = s.id WHERE s.title REGEXP '[^0-9]' GROUP BY ls.sound ORDER BY like_count DESC LIMIT 10;");
				$request->execute();
				$soundsData = $request->fetchAll();

				$sounds = array();

				foreach($soundsData as &$row){
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
				throw new Exception("Error get most liked sounds".$e->getMessage(), 500);
			}
		}

		/**
		* @return array Une liste de classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des playlists les plus aimées
		* @exception PDOException La requête échoue
		*/
		static function mostLikedPlaylist(){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT lp.playlist, COUNT(*) AS like_count FROM like_playlist lp JOIN playlist p ON lp.playlist = p.id WHERE p.title REGEXP '[^0-9]' GROUP BY lp.playlist ORDER BY like_count ASC LIMIT 10;");
				$request->execute();
				$playlistsData = $request->fetchAll();

				$playlists = array();

				foreach($playlistsData as &$row){
					if(is_numeric($row['playlist'])){
						$playlist = DatabasePlaylist::byId($row['playlist']);
					}else{
						$playlist = YoutubePlaylist::byId($row['playlist']);
					}

					array_push($playlists, $playlist);
				}

				unset($row);

				return $playlists;
			} catch(PDOException $e){
				throw new Exception("Error get most liked playlists ".$e->getMessage(), 500);
			}
		}

		/**
		* @return array Une liste de classe artist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données des artistes émergents
		* @exception PDOException La requête échoue
		*/
		static function leastArtist(){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT s.artist, ANY_VALUE(a.sound) AS sound, COUNT(*) AS play_count  FROM activity a JOIN sound s ON a.sound = s.id WHERE a.sound REGEXP '^[0-9]+$' GROUP BY s.artist ORDER BY play_count ASC LIMIT 10");
				$request->execute();
				$artistsData = $request->fetchAll();

				$artists = array();

				foreach($artistsData as &$row){
					if(is_numeric($row['artist'])){
						$artist = DatabaseArtist::byId($row['artist']);
					}else{
						$artist = YoutubeArtist::byId($row['artist']);
					}

					array_push($artists, $artist);
				}

				unset($row);

				return $artists;
			} catch(PDOException $e){
				throw new Exception("Error get least artist".$e->getMessage(), 500);
			}
		}

    }