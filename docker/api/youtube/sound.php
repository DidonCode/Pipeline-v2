<?php

	/**
	* @class YoutubeSound
	*
	* @brief Récupération des musiques via l'API YouTube
	*
	* @file sound.php
	*/	
	class YoutubeSound {

		/**
		* @param $id Identifiant YouTube de la musique
		* @return array renvoi une classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la musique YouTube correspondant à l'identifiant
		* @exception Exception La réponse de l'API YouTube ($search) est vide
		* @exception Exception L'identifiant ne correspond à aucune musique
		* @exception PDOException La requête échoue
		*/
		static function byId($id){
			
			try{
				if(YoutubeFunction::ageRestricted($id)) throw new Exception("Error this sound is restricted : ".$id.".", 400);

				$url			=	'https://www.googleapis.com/youtube/v3/videos?videoCategoryId=10&part=snippet&id='.$id;
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);

				if (count($search->items) == 0) {
					throw new Exception("Error this sound not exist : ".$id.".", 404);
				}

				$soundData		=	$search->items[0];

				if(!isset($soundData->snippet) || !isset($soundData->snippet->thumbnails->default)) throw new Exception("Error to get sound by id: ".$id, 400);

				$quality = YoutubeFunction::imageQuality($soundData->snippet->thumbnails);

				$sound = new Sound(
					id:$soundData->id,
					title: html_entity_decode($soundData->snippet->title, ENT_QUOTES, 'UTF-8'),
					artist: html_entity_decode($soundData->snippet->channelId, ENT_QUOTES, 'UTF-8'),
					type: 1,
					image:$quality->url,
					link:"https://www.youtube.com/watch?v=".$soundData->id
				);

				return $sound->toString();

			} catch(Exception $e){
				throw $e;
			}
		}

		/**
		* @param $title Titre de la musique YouTube
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array renvoi une classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la musique YouTube correspondant au titre
		* @exception Exception Le titre ne correspond à aucune musique
		* @exception PDOException La requête échoue
		*/
		static function byTitle($title, $page, $perPage){

			try{

				$url			=	'https://www.googleapis.com/youtube/v3/search?part=snippet&videoCategoryId=10&maxResults='.$perPage.'&regionCode=FR&type=video&q='.urlencode($title);
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);

				for($i = 0; $i < $page; $i++){
					$url			=	'https://www.googleapis.com/youtube/v3/search?part=snippet&videoCategoryId=10&maxResults='.$perPage.'&pageToken='.$search->nextPageToken.'&regionCode=FR&type=video&q='.urlencode($title);
					$options		=	[	CURLOPT_URL				=>	$url,
											CURLOPT_RETURNTRANSFER	=>	TRUE
										];
					$search			=	callYoutubeApi($options);
				}
				
				if(count($search->items) == 0) return array();

				$sounds			=	array();

				$soundData		=	$search->items[0];

				if(!isset($soundData->snippet)) throw new Exception("Error to get sound by title: ".$title, 400);

				foreach($search->items as &$soundData){
					if(YoutubeFunction::ageRestricted($soundData->id->videoId)) continue;

					if(!isset($soundData->snippet->thumbnails->default)) continue;

					$quality = YoutubeFunction::imageQuality($soundData->snippet->thumbnails);
					$titre_decode = html_entity_decode($soundData->snippet->title, ENT_QUOTES, 'UTF-8');
					$artist_decode = html_entity_decode($soundData->snippet->channelId, ENT_QUOTES, 'UTF-8');

					$sound = new Sound(
						id:$soundData->id->videoId,
						title:$titre_decode,
						artist:$artist_decode,
						type: 1,
						image:$quality->url,
						link:"https://www.youtube.com/watch?v=".$soundData->id->videoId
					);

					array_push($sounds, $sound->toString());
				}

				unset($soundData);

				return $sounds;

			} catch(Exception $e){
				throw $e;
			}
		}

		/**
		* @param $artist Artiste de la musique YouTube
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array renvoi une classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la musique YouTube correspondant à l'artiste
		* @exception Exception L'artiste ne correspond à aucun compte
		* @exception PDOException La requête échoue
		*/
		static function byArtist($artist, $page, $perPage){

			try{
				$url			=	'https://www.googleapis.com/youtube/v3/search?part=snippet&videoCategoryId=10&maxResults='.$perPage.'&type=video&videoDuration=medium&channelId='.$artist;
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);

				for($i = 0; $i < $page; $i++){
					$url			=	'https://www.googleapis.com/youtube/v3/search?part=snippet&videoCategoryId=10&maxResults='.$perPage.'&pageToken='.$search->nextPageToken.'&type=video&videoDuration=medium&channelId='.$artist;
					$options		=	[	CURLOPT_URL				=>	$url,
											CURLOPT_RETURNTRANSFER	=>	TRUE
										];
					$search			=	callYoutubeApi($options);
				}

				$sounds			=	array();

				if($search->pageInfo->totalResults == 0) return $sounds;;

				$soundData		=	$search->items[0];

				if(!isset($soundData->snippet)) throw new Exception("Error to get sound by artist: ".$artist, 400);
	
				foreach($search->items as &$soundData){

					if(YoutubeFunction::ageRestricted($soundData->id->videoId)) continue;

					if(!isset($soundData->snippet->thumbnails->default)) continue;

					$quality = YoutubeFunction::imageQuality($soundData->snippet->thumbnails);					
					$titre_decode = html_entity_decode($soundData->snippet->title, ENT_QUOTES, 'UTF-8');
					$artist_decode = html_entity_decode($soundData->snippet->channelId, ENT_QUOTES, 'UTF-8');
	
					$sound = new Sound(
						id:$soundData->id->videoId,
						title:$titre_decode,
						artist:$artist_decode,
						type: 1,
						image:$quality->url,
						link:"https://www.youtube.com/watch?v=".$soundData->id->videoId
					);

					array_push($sounds, $sound->toString());
				}

				unset($soundData);

				return $sounds;

			} catch(Exception $e){
				throw $e;
			}
		}

		/**
		* @param $playlist Playlist YouTube
		* @return array renvoi une classe sound sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la playlist YouTube correspondant
		* @exception Exception La playlist ne correspond à aucune playlist
		* @exception PDOException La requête échoue
		*/
		static function byPlaylist($playlist){

			try{
				$url			=	'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&type=video&videoDuration=medium&playlistId='.$playlist;
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);
	
				$sounds			=	array();

				$soundData		=	$search->items[0];

				if(!isset($search->items[0]) && count($soundData->snippet)) throw new Exception("Error to get sounds in playlist: ".$playlist, 400);
	
				foreach($search->items as &$soundData){

					if(YoutubeFunction::ageRestricted($soundData->snippet->resourceId->videoId)) continue;

					if(!isset($soundData->snippet->thumbnails->default)) continue;

					$quality = YoutubeFunction::imageQuality($soundData->snippet->thumbnails);
					$titre_decode = html_entity_decode($soundData->snippet->title, ENT_QUOTES, 'UTF-8');
					$artist_decode = html_entity_decode($soundData->snippet->channelId, ENT_QUOTES, 'UTF-8');
					
					$sound = new Sound(
						id: $soundData->snippet->resourceId->videoId,
						title:$titre_decode,
						artist:$artist_decode,
						type: 1,
						image:$quality->url,
						link:"https://www.youtube.com/watch?v=".$soundData->snippet->playlistId
					);
		
					array_push($sounds, $sound->toString());
				}
		
				unset($soundData);
	
				return $sounds;

			} catch(Exception $e){
				throw $e;
			}
		}
	}