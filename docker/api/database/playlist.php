<?php
	
	/**
	* @class DatabasePlaylist
	*
	* @brief Récupération des playlists dans la base de donnée
	*
	* @file playlist.php
	*/
	class DatabasePlaylist {

		/**
		* @param $id Identifiant de la playlist
		* @return array Une classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la playlist correspondant à l'identifiant
		* @exception Exception L'identifiant ne correspond à aucune playlist
		* @exception PDOException La requête échoue
		*/
		static function byId($id){
			global $pdoDatabase;

			try {
				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE id = ? AND public = 1");
				$request->execute(array($id));
				$playlistData = $request->fetchAll();

				if(count($playlistData) == 0) throw new Exception("Playlist not exit", 404);

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
				throw new Exception("Error to get playlist by id: ".$id." ".$e->getMessage(), 400);
			}
		}
		
		/**
		* @param $title Chaine de caractère contenue dans le titre
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array Une liste de classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de toutes les playlists qui sont publiques et ont un titre contenant le titre recherché
		* @exception PDOException La requête échoue
		*/
		static function byTitle($title, $page, $perPage){
			global $pdoDatabase;

			try {
				$offset = $page * $perPage;

				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE public = 1 AND title LIKE ? LIMIT $perPage OFFSET $offset");
				$request->execute(array("%".$title."%"));
				$playlistsData = $request->fetchAll();

				$playlists = array();

				foreach($playlistsData as &$row) {
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
				throw new Exception("Error to get playlist by title: ".$title." ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $owner Identifiant de l'utilisateur qui est propriétaire
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array Une liste de classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de toutes les playlists qui sont publiques et ont comme propriétaire l'utilisateur
		* @exception PDOException La requête échoue
		*/
		static function byOwner($owner, $page, $perPage){
			global $pdoDatabase;

			try {
				$offset = $page * $perPage;

				$request = $pdoDatabase->prepare("SELECT * FROM playlist WHERE public = 1 AND owner = ? LIMIT $perPage OFFSET $offset");
				$request->execute(array($owner));
				$playlistsData = $request->fetchAll();

				$playlists = array();

				foreach($playlistsData as &$row) {
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
				throw new Exception("Error to get playlist by owner: ".$owner." ".$e->getMessage(), 400);
			}
		}
	}