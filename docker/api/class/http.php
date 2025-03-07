<?php

	/**
	* @class Http
	*
	* @brief Gère les renvois API
	*
	* @file http.php
	*/
	class Http {

		/**
		* @param $statutCode Code de réponse http
		* @param $data Donnée à renvoyer
		* 
		* @return void
		*
		* @brief Renvoie une réponse http
		*/
		static function sendResponse($statutCode, $data){
			http_response_code($statutCode);
			echo json_encode($data);
		}

		/**
		* @param $exception Exception à renvoyer
		* 
		* @return void
		*
		* @brief Renvoie une erreur http
		*/
		static function sendError($exception){
			Http::sendResponse($exception->getCode(), array("error" => array("error_message" => $exception->getMessage(), "error_code" => $exception->getCode())));
		}

		/**
		* @param $exception Exception à renvoyer
		* @param $exceptionCode Code spécifique à renvoyer
		* 
		* @return void
		*
		* @brief Renvoie une erreur http
		*/
		static function sendCustomError($exception, $exceptionCode){
			Http::sendResponse($exceptionCode, array("error" => array("error_message" => $exception->getMessage(), "error_code" => $exception->getCode())));
		}
	}