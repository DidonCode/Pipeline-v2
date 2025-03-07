<?php

	/**
	* @class DatabaseUserSubscription
	*
	* @brief Gestion des abonnements de l'utilisateur dans la base de données
	*
	* @file subscription.php
	*/
    class DatabaseUserSubscription {

        /**
		* @param $user Information de l'utilisateur
		* @return \Stripe\Checkout\Session Session stripe pour l'achat
		*
		* @brief Crée une session d'achat Stripe et stocke l'id pour retrouver l'achat dans le webhook
		* @exception PDOException La requête échoue
        * @exception \Stripe\Exception\ApiErrorException Erreur de l'api Stripe
		*/
        static function createSession($user, $name, $priceId){
            global $pdoDatabase;

            try {
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'mode' => 'subscription',
                    'line_items' => [[
                        'price' => $priceId,
                        'quantity' => 1,
                    ]],
                    'success_url' => Settings::$STRIPE_SUCCESS,
                    'cancel_url' => Settings::$STRIPE_CANCEL,
                    'metadata' => [
                        'name' => $name,
                        'user' => $user['id'],
                    ]
                ]);  
                
                $request = $pdoDatabase->prepare("INSERT INTO subscription (user, session) VALUES (?, ?) ON DUPLICATE KEY UPDATE session = VALUES(session)");
                $request->execute(array($user['id'], $session->id));

                return $session;
            } catch(\Stripe\Exception\ApiErrorException $e){
				throw new Exception("Error to create stripe session".$e->getMessage(), 500);
			}
        }

        /**
		* @param $type Type de l'abonnement
        * @param $createdAt Date de création
        * @param $price Prix payé pour l'abonnement
        * @param $updateAt Date d'expiration ou renouvellement
        * @param $sessionId Identifiant de la session d'achat Stripe
        * @param $paymentId Identifiant du payement de l'achat Stripe
        * @param $subscriptionId Identifiant de l'abonnement de l'achat Stripe
		* @return bool Confirme si l'exécution a réussi
		*
		* @brief Mise à jour des informations de l'abonnement après le paiement de l'utilisateur
		* @exception PDOException La requête échoue
		*/
        static function create($type, $createdAt, $price, $updateAt, $sessionId, $paymentId, $subscriptionId){
            global $pdoDatabase;

            try{
                $request = $pdoDatabase->prepare("UPDATE subscription SET type = ?, created_at = ?, price = ?, update_at = ?, payment = ?, subscription = ? WHERE session = ?");
                $request->execute(array($type, $createdAt, $price, $updateAt, $paymentId, $subscriptionId, $sessionId));

                return true;
            } catch(PDOException $e){
                throw new Exception("Error to create subscription for session: ".$sessionId.". ".$e->getMessage(), 500);
            }
        }

        /**
		* @param $user Information de l'utilisateur
		* @return bool Renvoie si l'abonnement a bien été annulé
		*
		* @brief Annule l'abonnement de l'utilisateur
		* @exception PDOException La requête échoue
        * @exception \Stripe\Exception\ApiErrorException Erreur de l'api Stripe
		*/
        static function cancel($user){
            global $pdoDatabase;

            try{
                $request = $pdoDatabase->prepare("SELECT subscription FROM subscription WHERE user = ?");
                $request->execute(array($user['id']));
                $subscriptionId = $request->fetchAll()[0][0];

                $subscription = \Stripe\Subscription::retrieve($subscriptionId);
                $subscription->cancel();

                $request = $pdoDatabase->prepare("DELETE FROM subscription WHERE user = ?");
                $request->execute(array($user['id']));

                return true;
            } catch(\Stripe\Exception\ApiErrorException $e){
                throw new Exception("Error to cancel subscription for user: ".$user['id'].". ".$e->getMessage(), 500);
            } catch(PDOException $e){
                throw new Exception("Error to get or remove subscription for user: ".$user['id'].". ".$e->getMessage(), 500);
            }
        }

        /**
		* @param $user Information de l'utilisateur
		* @return Subscription Une classe subscription sous la forme d'un tableau associatif
		*
		* @brief Renvoie l'abonnement en cours ou expiré de l'utilisateur
		* @exception PDOException La requête échoue
		*/
        static function get($user){
            global $pdoDatabase;

            try{
                $request = $pdoDatabase->prepare("SELECT * FROM subscription WHERE user = ?");
                $request->execute(array($user['id']));
                $subscriptionData = $request->fetchAll();

                if(count($subscriptionData) >= 1) {
                    if(!isset($subscriptionData[0]['type'], $subscriptionData[0]['created_at'], $subscriptionData[0]['price'], $subscriptionData[0]['update_at'])) return null;

                    return Subscription::toClass($subscriptionData[0]);
                }

                return null;
            } catch(PDOException $e){
                throw new Exception("Error to get subscription for user: ".$user['id'].". ".$e->getMessage(), 500);
            }
        } 

    }