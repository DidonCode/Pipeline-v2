<?php

	/**
	* @class Sound
	*
	* @brief Gère les données d'une musique
	*
	* @file sound.php
	*/
	class Sound {

		private $id;
		private $title;
		private $artist;
		private $type;
		private $image;
		private $link;

		function __construct($id, $title, $artist, $type, $image, $link){
			$this->id = $id;
			$this->title = $title;
			$this->artist = $artist;
			$this->type = $type;
			$this->image = $image;
			$this->link = $link;
		}

		/**
		* @return int
		*
		* @brief Renvoie l'identifiant de la musique
		*/
		function getId(){
			return $this->id;
		}

		/**
		* @return string
		*
		* @brief Renvoie le titre de la musique
		*/
		function getTitle(){
			return $this->title;
		}

		/**
		* @return int
		*
		* @brief Renvoie l'identifiant de l'artiste qui a créé la musique
		*/
		function getArtist(){
			return $this->artist;
		}

		/**
		* @return int
		*
		* @brief Renvoie le type de la musique
		*/
		function getType(){
			return $this->type;
		}

		/**
		* @return string
		*
		* @brief Renvoie l'image de la musique
		*/
		function getImage(){
			return $this->image;
		}

		/**
		* @return string
		*
		* @brief Renvoie le chemin du fichier de la musique
		*/
		function getLink(){
			return $this->link;
		}

		/**
		* @return array
		*
		* @brief Renvoie les données de la musiques
		*/
		function toString(){
			return array(
				"id" => $this->id,
				"title" => $this->title,
				"artist" => $this->artist,
				"type" => $this->type,
				"image" => str_starts_with($this->image, "/storage") ? Settings::$STORAGE_HOST_NAME.$this->image : $this->image,
				"link" => str_starts_with($this->link, "/storage") ? Settings::$STORAGE_HOST_NAME.$this->link : $this->link
			);
		}
		
		/**
		* @return array
		*
		* @brief Renvoie les données de la musique
		*/
		function toArray(){
			return array(
				$this->id,
				$this->title,
				$this->artist,
				$this->type,
				$this->image,
				$this->link
			);
		}

		/**
		* @param $sound Données d'une musique
		* 
		* @return Sound
		*
		* @brief Renvoie une nouvelle musique à partir des données
		*/
        static function toClass($sound){
            return new Sound(
                $sound['id'],
                $sound['title'],
                $sound['artist'],
                $sound['type'],
                $sound['image'],
                $sound['link']
            );
        }
	}