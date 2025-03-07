<?php

	/**
	* @class YoutubePlaylist
	*
	* @brief Récupération des playlists via l'API YouTube
	*
	* @file playlist.php
	*/
	class YoutubePlaylist {

		/**
		* @param $id ID de la playlist YouTube
		* @return array renvoi une classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la playlist YouTube correspondant
		* @exception Exception La playlist n'éxiste pas
		* @exception Exception La playlist ne correspond à aucune playlist
		* @exception PDOException La requête échoue
		*/
		static function byId($id){

			try{
				$url			=	'https://www.googleapis.com/youtube/v3/playlists?&part=snippet&id='.urlencode($id);
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);

				if (count($search->items) == 0) {
					throw new Exception("Error this playlist not exist : ".$id, 404);
				}

				$playlistData		=	$search->items[0];
				$playlistThumb		=	$playlistData->snippet->thumbnails;

				if(!isset($playlistData->snippet) && !isset($playlistThumb->default)) throw new Exception("Error to get playlist by ID: ".$id, 400);

				$quality = YoutubeFunction::imageQuality($playlistThumb);

				$playlist = new Playlist(
					id:$playlistData->id,
					owner: html_entity_decode($playlistData->snippet->channelId, ENT_QUOTES, 'UTF-8'),
					title: html_entity_decode($playlistData->snippet->title, ENT_QUOTES, 'UTF-8'),
					description: html_entity_decode($playlistData->snippet->description, ENT_QUOTES, 'UTF-8'),
					image: $quality->url,
					public: 1
				);

				return $playlist->toString();

			} catch(Exception $e){
				throw $e;
			}
		}


		/**
		* @param $title Titre de la playlist YouTube
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array renvoi une classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la playlist YouTube correspondant en appelant la fonction byId
		* @exception Exception La playlist n'éxiste pas
		* @exception PDOException La requête échoue
		*/
		static function byTitle($title, $page, $perPage){

			try{
				$url			=	'https://www.googleapis.com/youtube/v3/search?type=playlist&maxResults='.$perPage.'&q='.urlencode($title);
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);

				for($i = 0; $i < $page; $i++){
					$url			=	'https://www.googleapis.com/youtube/v3/search?type=playlist&maxResults='.$perPage.'&q='.urlencode($title);
					$options		=	[	CURLOPT_URL				=>	$url,
											CURLOPT_RETURNTRANSFER	=>	TRUE
										];
					$search			=	callYoutubeApi($options);
				}

				$playlists		=	array();

				foreach($search->items as &$playlistData){
					try{
						array_push($playlists, YoutubePlaylist::byId($playlistData->id->playlistId));
					} catch(Exception $e){
						continue;
					}
				}

				unset($playlistData);

				return $playlists;

			} catch(Exception $e){
				throw $e;
			}
		}


		/**
		* @param $owner Responsable de la playlist YouTube
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array renvoi une classe playlist sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la playlist YouTube correspondant en appelant la fonction byId
		* @exception Exception La playlist n'éxiste pas
		* @exception PDOException La requête échoue
		*/
		static function byOwner($owner, $page, $perPage){

			try{$url			=	'https://www.googleapis.com/youtube/v3/search?type=playlist&maxResults='.$perPage.'&channelId='.$owner;
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);

				for($i = 0; $i < $page; $i++){
					$url			=	'https://www.googleapis.com/youtube/v3/search?type=playlist&maxResults='.$perPage.'&channelId='.$owner;
					$options		=	[	CURLOPT_URL				=>	$url,
											CURLOPT_RETURNTRANSFER	=>	TRUE
										];
					$search			=	callYoutubeApi($options);
				}

				$playlists			=	array();
				
				foreach($search->items as &$playlistData){
					try{
						array_push($playlists, YoutubePlaylist::byId($playlistData->id->playlistId));
					} catch(Exception $e){
						continue;
					}
				}

				unset($playlistData);

				return $playlists;

			} catch(Exception $e){
				throw $e;
			}
		}
	}