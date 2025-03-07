<?php
	
	/**
	* @class DatabaseSound
	*
	* @brief Récupération des musiques dans la base de donnée
	*
	* @file sound.php
	*/
	class DatabaseSound {

		/**
		* @param $id Identifiant de la musique
		* @return array Une classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la musique correspondant à l'identifiant
		* @exception Exception L'identifiant ne correspond à aucune musique
		* @exception PDOException La requête échoue
		*/
		static function byId($id){
			global $pdoDatabase;

			try{
				$request = $pdoDatabase->prepare("SELECT * FROM sound WHERE id = ?");
				$request->execute(array($id));
				$soundData = $request->fetchAll();

				if(count($soundData) == 0) throw new Exception("Sound not exist", 404);

				$sound = new Sound(
					$soundData[0]['id'], 
					$soundData[0]['title'],
					$soundData[0]['artist'],
					$soundData[0]['type'],
					$soundData[0]['image'],
					$soundData[0]['link']
				);

				return $sound->toString();
			} catch(PDOException $e){
				throw new Exception("Error to get sound by id: ".$id." ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $title Chaine de caractère contenue dans le titre
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de toutes les musiques qui ont un titre contenant le titre recherché
		* @exception PDOException La requête échoue
		*/
		static function byTitle($title, $page, $perPage){
			global $pdoDatabase;

			try{
				$offset = intval($page * $perPage);

				$request = $pdoDatabase->prepare("SELECT * FROM sound WHERE title LIKE ? LIMIT $perPage OFFSET $offset");
				$request->execute(array("%".$title."%"));
				$soundsData = $request->fetchAll();

				$sounds = array();

				foreach($soundsData as &$row) {
					$sound = new Sound(
						$row['id'], 
						$row['title'],
						$row['artist'],
						$row['type'],
						$row['image'],
						$row['link']
					);

					array_push($sounds, $sound->toString());
				}

				unset($row);

				return $sounds;
			} catch(PDOException $e){
				throw new Exception("Error to get sound by title: ".$title." ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $artist Identifiant de l'artiste qui est propriétaire
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array Une liste de classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de toutes les musiques qui ont comme propriétaire l'artiste
		* @exception PDOException La requête échoue
		*/
		static function byArtist($artist, $page, $perPage){
			global $pdoDatabase;

			try{
				$offset = intval($page * $perPage);
				
				$request = $pdoDatabase->prepare("SELECT * FROM sound WHERE artist = ? LIMIT $perPage OFFSET $offset");
				$request->execute(array($artist));
				$soundsData = $request->fetchAll();

				$sounds = array();

				foreach($soundsData as &$row) {
					$sound = new Sound(
						$row['id'], 
						$row['title'],
						$row['artist'],
						$row['type'],
						$row['image'],
						$row['link']
					);

					array_push($sounds, $sound->toString());
				}

				unset($row);

				return $sounds;
			} catch(PDOException $e){
				throw new Exception("Error to get sound by artist: ".$artist." ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $playlist Identifiant de la playlist qui contient 
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array Liste des identifiants des musiques dans la playlist
		*
		* @brief Renvoie l'identifiant de toutes les musiques qui sont contenues dans la playlist si elle est publique
		* @exception Exception La playlist n'est pas publique
		* @exception PDOException La requête échoue
		*/
		static function byPlaylist($playlist){
			global $pdoDatabase;

			try{
				$playlist = DatabasePlaylist::byId($playlist);

				if($playlist['public'] == 0) throw new Exception("This playlist is private", 400);

				$request = $pdoDatabase->prepare("SELECT sound as id FROM playlist_sound WHERE playlist = ?");
				$request->execute(array($playlist['id']));
				$soundsData = $request->fetchAll();

				return $soundsData;
			} catch(PDOException $e){
				throw new Exception("Error to get sounds in playlist: ".$playlist." ".$e->getMessage(), 400);
			}
		}
		
	}
