<?php
	
	/**
	* @class DatabaseMember
	*
	* @brief Récupération des utilisateurs dans la base de données
	*
	* @file member.php
	*/
	class DatabaseMember {

		/**
		* @param $id Identifiant de l'utilisateur
		* @return array Une classe user sous la forme d'un tableau associatif filtré
		*
		* @brief Renvoie les données de l'utilisateur correspondant à l'identifiant
		* @exception Exception L'identifiant ne correspond à aucun utilisateur
		* @exception PDOException La requête échoue
		*/
		static function byId($id){
			global $pdoDatabase;

			try {
				$request = $pdoDatabase->prepare("SELECT id, email, pseudo, image, banner, public FROM user WHERE id = ?");
				$request->execute(array($id));
				$menberData = $request->fetchAll();

				if(count($menberData) == 0) throw new Exception("User not exist", 404);

                return User::toFilter($menberData[0]);
			} catch(PDOException $e){
				throw new Exception("Error to get user by id: ".$id." ".$e->getMessage(), 400);
			}
		}

		/**
		* @param $pseudo Chaine de caractère contenue dans le pseudo
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array Une liste de classe user sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de toutes les utilisateur qui ont un pseudo contenant le pseudo recherché
		* @exception PDOException La requête échoue
		*/
		static function byPseudo($pseudo, $page, $perPage){
			global $pdoDatabase;

			try {
				$offset = $page * $perPage;

				$request = $pdoDatabase->prepare("SELECT id, email, pseudo, grade, image, banner, public FROM user WHERE pseudo LIKE ? LIMIT $perPage OFFSET $offset");
				$request->execute(array("%".$pseudo."%"));
				$menbersData = $request->fetchAll();

				$menbers = array();

				foreach($menbersData as &$row) {
					array_push($menbers, User::toFilter($row));
				}

				unset($row);

				return $menbers;
			} catch(PDOException $e){
				throw new Exception("Error to get artist by pseudo: ".$pseudo." ".$e->getMessage(), 400);
			}		
		}

	}