<?php

    /**
	* @class Subscription
	*
	* @brief Gère les données d'un abonnement
	*
	* @file subscription.php
	*/
    class Subscription {

		private $user;
		private $type;
		private $createdAt;
		private $price;
		private $updateAt;
		private $session;

		function __construct($user, $type, $createdAt, $price, $updateAt, $session){
			$this->user = $user;
            $this->type = $type;
			$this->createdAt = new DateTime($createdAt);
			$this->price = $price;
			$this->updateAt = new DateTime($updateAt);
			$this->session = $session;
		}

		/**
		* @return bool
		*
		* @brief Renvoie si l'abonnement est expiré
		*/
        function isExpired(){
			$now = new DateTime(date("Y-m-d H:m:s"));
            return $this->updateAt->getTimestamp() < $now->getTimestamp();
        }

		/**
		* @return bool
		*
		* @brief Renvoie si l'abonnement peut être encore échangé
		*/
        function isChangeable(){
			$now = new DateTime(date("Y-m-d H:m:s"));
            return $this->createdAt->getTimestamp() < $now->getTimestamp() + 3600;
        }

		/**
		* @return int
		*
		* @brief Renvoie l'identifiant de l'utilisateur
		*/
		function getUser(){
			return $this->user;
		}

		/**
		* @return string
		*
		* @brief Renvoie le type
		*/
		function getType(){
			return $this->type;
		}

		/**
		* @return DateTime
		*
		* @brief Renvoie la date de création
		*/
		function getCreatedAt(){
			return $this->createdAt;
		}

		/**
		* @return int
		*
		* @brief Renvoie le prix d'achat
		*/
		function getPrice(){
			return $this->price;
		}

		/**
		* @return DateTime
		*
		* @brief Renvoie la date de renouvellement
		*/
		function getUpdateAt(){
			return $this->updateAt;
		}

		/**
		* @return string
		*
		* @brief Renvoie l'identifiant de la session d'achat
		*/
		function getSession(){
			return $this->session;
		}

		/**
		* @return array
		*
		* @brief Renvoie les données de l'abonnement
		*/
		function toString(){
			return array(
				"type" => $this->type,
				"createdAt" => $this->createdAt->format("Y-m-d H:i:s"),
				"updateAt" => $this->updateAt->format("Y-m-d H:i:s"),
			);
		}
		
		/**
		* @return array
		*
		* @brief Renvoie les données de l'abonnement
		*/
		function toArray(){
			return array(
				$this->user,
				$this->type,
				$this->createdAt,
				$this->price,
				$this->updateAt,
				$this->session
			);
		}

		/**
		* @param $subscription Données d'un abonnement
		* 
		* @return Subscription
		*
		* @brief Renvoie un nouvel abonnement à partir des données
		*/
        static function toClass($subscription){
            return new Subscription(
                $subscription['user'],
                $subscription['type'],
                $subscription['created_at'],
                $subscription['price'],
                $subscription['update_at'],
                $subscription['session']
            );
        }
	}