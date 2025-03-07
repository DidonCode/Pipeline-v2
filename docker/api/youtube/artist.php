<?php

	/**
	* @class YoutubeArtist
	*
	* @brief Récupération des artistes via l'API YouTube
	*
	* @file artist.php
	*/	
	class YoutubeArtist {

		/**
		* @param $id ID de l'artiste YouTube
		* @return array renvoi une classe artiste sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de l'artiste YouTube correspondant
		* @exception Exception L'artiste n'éxiste pas
		* @exception Exception L'artiste ne correspond à aucun artiste
		* @exception PDOException La requête échoue
		*/
		static function byId($id){

			try{
				$url			=	'https://www.googleapis.com/youtube/v3/channels?videoCategoryId=10&part=snippet&id='.$id;
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);

				if (count($search->items) == 0) {
					throw new Exception("Error this artist not exist : ".$id, 404);
				}

				$artistData		=	$search->items[0];

				if(!isset($artistData->snippet) || !isset($artistData->snippet->thumbnails->default)) throw new Exception("Error to get artist by ID: ".$id, 400);
				
				$lien = $artistData->snippet->thumbnails;
				$quality = YoutubeFunction::imageQuality($lien);

				$url			=	'https://www.googleapis.com/youtube/v3/channels?part=brandingSettings&id='.$id;
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$bannerData			=	callYoutubeApi($options);
				
				if (!isset($bannerData->items) || (isset($bannerData->items[0]->brandingSettings->image) && isset($bannerData->items[0]->brandingSettings->image->bannerExternalUrl))) $banner = $bannerData->items[0]->brandingSettings->image->bannerExternalUrl;

				else $banner = "/storage/user/banner/default.png";

				$pseudo_decode = html_entity_decode($artistData->snippet->title, ENT_QUOTES, 'UTF-8');

				$artist = new Artist(
					id: $artistData->id,
					pseudo: $pseudo_decode,
					image: $quality->url,
					banner: $banner,
					public: 1
				);

				return $artist->toString();

			}  catch(Exception $e){
				throw $e;
			}
		}

		/**
		* @param $pseudo Nom de l'artiste YouTube
		* @param $page Numéro de la page
		* @param $perPage Nombre de résultats par page
		* @return array renvoi une classe artiste sous la forme d'un tableau associatif
		*
		* @brief Renvoie les données de la l'artiste YouTube correspondant
		* @exception Exception L'artiste n'éxiste pas
		* @exception Exception Le pseudo ne correspond à aucun Artiste
		* @exception PDOException La requête échoue
		*/
		static function byPseudo($pseudo, $page, $perPage){

			try{
				$url			=	'https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults='.$perPage.'&type=channel&q='.urlencode($pseudo);
				$options		=	[	CURLOPT_URL				=>	$url,
										CURLOPT_RETURNTRANSFER	=>	TRUE
									];
				$search			=	callYoutubeApi($options);

				for($i = 0; $i < $page; $i++){
					$url			=	'https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults='.$perPage.'&pageToken='.$search->nextPageToken.'&type=channel&q='.urlencode($pseudo);
					$options		=	[	CURLOPT_URL				=>	$url,
											CURLOPT_RETURNTRANSFER	=>	TRUE
										];
					$search			=	callYoutubeApi($options);
				}

				$artists		=	array();

				foreach($search->items as &$artistData){

					if(!isset($artistData->snippet) || !isset($artistData->snippet->thumbnails->default)) continue;

					$quality = YoutubeFunction::imageQuality($artistData->snippet->thumbnails);

					$url_			=	'https://www.googleapis.com/youtube/v3/channels?part=brandingSettings&id='.$artistData->id->channelId;
					$options_		=	[	CURLOPT_URL				=>	$url_,
											CURLOPT_RETURNTRANSFER	=>	TRUE
										];
					$bannerData		=	callYoutubeApi($options_);

					if(!isset($bannerData->items) OR !isset($bannerData->items->brandingSettings->image)) $banner = "/storage/user/banner/default.png";
					else $banner = $bannerData->items->brandingSettings->image->bannerExternalUrl;

					$pseudo_decode = html_entity_decode($artistData->snippet->title, ENT_QUOTES, 'UTF-8');

					$artist = new Artist(
						id:$artistData->id->channelId,
						pseudo:$pseudo_decode,
						image:$quality->url,
						banner: $banner,
						public: 1
					);

					array_push($artists, $artist->toString());
				}

				unset($artistData);

				return $artists;

			} catch(Exception $e){
				throw $e;
			}
		}
		
	}