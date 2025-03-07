<?php

	/**
	* @class Playlist
	*
	* @brief Gère les données d'une playlist
	*
	* @file playlist.php
	*/
	class Playlist {

		private $id;
		private $owner;
		private $title;
		private $description;
		private $image;
		private $public;

		function __construct($id, $owner, $title, $description, $image, $public){
			$this->id = $id;
			$this->owner = $owner;
			$this->title = $title;
			$this->description = $description;
			$this->image = $image;
			$this->public = $public;
		}

		/**
		* @return int
		*
		* @brief Renvoie l'identifiant de la playlist
		*/
		function getId(){
			return $this->id;
		}

		/**
		* @return int
		*
		* @brief Renvoie l'identifiant de l'utilisateur qui a créé la playlist
		*/
		function getOwner(){
			return $this->owner;
		}

		/**
		* @return string
		*
		* @brief Renvoie le titre de la playlist
		*/
		function getTitle(){
			return $this->title;
		}

		/**
		* @return string
		*
		* @brief Renvoie la description de la playlist
		*/
		function getDescription(){
			return $this->description;
		}

		/**
		* @return string
		*
		* @brief Renvoie l'image de la playlist
		*/
		function getImage(){
			return $this->image;
		}

		/**
		* @return int
		*
		* @brief Renvoie la visibilité de la playlist
		*/
		function isPublic(){
			return $this->public;
		}

		/**
		* @return array
		*
		* @brief Renvoie les données de la playlist
		*/
		function toString(){
			return array(
				"id" => $this->id,
				"owner" => $this->owner,
				"title" => $this->title,
				"description" => $this->description,
				"image" => str_starts_with($this->image, "/storage") ? Settings::$STORAGE_HOST_NAME.$this->image : $this->image,
				"public" => $this->public
			);
		}

		/**
		* @return array
		*
		* @brief Renvoie les données de la playlist
		*/
		function toArray(){
			return array(
				$this->id,
				$this->owner,
				$this->title,
				$this->description,
				$this->image,
				$this->public
			);
		}

		/**
		* @param $playlist Données d'une playlist
		* 
		* @return Playlist
		*
		* @brief Renvoie une nouvelle playlist à partir des données
		*/
		static function toClass($playlist){
			return new Playlist(
				$playlist['id'],
				$playlist['owner'],
				$playlist['title'],
				$playlist['description'],
				$playlist['image'],
				$playlist['public']
			);
		}
	}