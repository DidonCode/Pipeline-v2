<?php

	/**
	* @class YoutubeFunction
	*
	* @brief Récupération de la meilleure qualité d'image possible d'une musique
	*
	* @file functions.php
	*/	
	class YoutubeFunction {

		/**
		* @param $thumbnails Lien permettant d'avoir l'image de la musique 
		* @return var qualite de l'image
		*
		* @brief Renvoie la meilleure qualité d'image possible
		*/
		static function imageQuality($thumbnails){
			$image = "/storage/sound/image/default.png";
		
			foreach($thumbnails as &$qualite){ $image = $qualite;}

			unset($qualite);

			return $image;
		}

		/**
		* @param $id Id de la musique 
		* @return bool true or false
		*
		* @brief Renvoie si la musique est restricted (au dessus de 18 ans)
		*/
		static function ageRestricted($id){

			$url			=	'https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id='.$id;

			$options		=	[	CURLOPT_URL				=>	$url,
									CURLOPT_RETURNTRANSFER	=>	TRUE
								];
			$search			=	callYoutubeApi($options);

			return isset($search->items[0]->contentDetails->contentRating->ytRating);
		}
	}

?>