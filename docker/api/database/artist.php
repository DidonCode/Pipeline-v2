<?php
	
	/**
	* @class DatabaseArtist
	*
	* @brief Récupération des artistes dans la base de données
	*
	* @file artist.php
	*/
	class DatabaseArtist {

		/**
		* @param $id Identifiant de l'artiste
		* @return array Une classe artist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de l'artiste correspondant à l'identifiant
		* @exception Exception L'identifiant ne correspond à aucun artiste
		* @exception PDOException La requête échoue
		*/
		static function byId($id){
			global $pdoDatabase;

			try {
				$request = $pdoDatabase->prepare("SELECT id, pseudo, image, banner, public FROM user WHERE id = ?");
				$request->execute(array($id));
				$artistData = $request->fetchAll();

				$artist = new Artist(
					$artistData[0]['id'],
					$artistData[0]['pseudo'],
					$artistData[0]['image'],
					$artistData[0]['banner'],
					$artistData[0]['public']
				);

				return $artist->toString();
			} catch(PDOException $e){
				throw new Exception("Error to get artist by id: ".$id." ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $pseudo Chaine de caractère contenue dans le pseudo
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array Une liste de classe artist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données d'un ou plusieurs artistes qui ont un pseudo contenant le pseudo recherché
		* @exception PDOException La requête échoue
		*/
		static function byPseudo($pseudo, $page, $perPage){
			global $pdoDatabase;

			try {
				$offset = $page * $perPage;

				$request = $pdoDatabase->prepare("SELECT id, pseudo, image, banner, public FROM user WHERE artist = 1 AND pseudo LIKE ? LIMIT $perPage OFFSET $offset");
				$request->execute(array("%".$pseudo."%"));
				$artistsData = $request->fetchAll();

				$artists = array();

				foreach($artistsData as &$row) {
					$artist = new Artist(
						$row['id'], 
						$row['pseudo'],
						$row['image'],
						$row['banner'],
						$row['public']
					);

					array_push($artists, $artist->toString());
				}

				unset($row);

				return $artists;
			} catch(PDOException $e){
				throw new Exception("Error to get artist by pseudo: ".$pseudo." ".$e->getMessage(), 400);
			}		
		}

	}