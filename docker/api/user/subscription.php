<?php

    require_once("../libs/stripe/init.php");
    
    require_once("../database/connect/database.php");

    include_once("../class/subscription.php");

    include_once("../class/http.php");

    include_once("../database/user/subscription.php");
    include_once("../database/user/account.php");

    include_once("../settings.php");

    header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    \Stripe\Stripe::setApiKey(Settings::$STRIPE_SECRET);

    if(count(array_keys($_POST)) == 2 AND isset($_POST['subscription'], $_POST['token'])){

        try{
            if(empty($_POST['subscription']) || empty($_POST['token'])) throw new Exception("Argument not valid", 400);

            $user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
            
            $product = Settings::$STRIPE_PRODUCTS[$_POST['subscription']];

            if(!$product) throw new Exception("Product not valid", 400);

            $subscription = DatabaseUserSubscription::get($user);

            if($subscription) {
                if(!isset(Settings::$STRIPE_PRODUCTS[$subscription->getType()]['upgrading']) && !$subscription->isChangeable()) throw new Exception("You have already subscription", 200);
            }

            $session = DatabaseUserSubscription::createSession($user, $_POST['subscription'], $product['priceId']);

            $subscription = array(
                "key" => Settings::$STRIPE_CLIENT,
                "session" => $session->id
            );

            Http::sendResponse(201, $subscription);
        }catch(Exception $e){
            Http::sendError($e);
        }

        return;
    }
    if(count(array_keys($_POST)) == 2 AND isset($_POST['action'], $_POST['token'])){

        try{
            if(empty($_POST['action']) OR empty($_POST['token']) OR !filter_var($_POST['action'], FILTER_VALIDATE_INT)) throw new Exception("Argument not valid", 400);

            $user = DatabaseUserAccount::get($_POST['token']);

			if(!isset($user)) throw new Exception("Invalid token", 403);
            
            if($_POST['action'] == 2){
                $subscription = DatabaseUserSubscription::get($user);
                Http::sendResponse(200, $subscription);
            }

            if($_POST['action'] == 1){
                $result = DatabaseUserSubscription::cancel($user);
                Http::sendResponse(200, $result);
            }
        }catch(Exception $e){
            Http::sendError($e);
        }

        return;
    }

    if(count(array_keys($_POST)) == 0){
        $payload = @file_get_contents('php://input');

        if(!isset($payload)) return;

        try{
            $event = json_decode($payload, true);

            if(!$event) return;

            $eventType = $event['type'];
            $eventData = $event['data']['object'];

            switch ($eventType) {
                case 'checkout.session.completed':
                    $productName = $eventData['metadata']['name'];
                    $userId = $eventData['metadata']['user'];

                    if(!isset(Settings::$STRIPE_PRODUCTS[$productName])) throw new Exception("Error this product not exist: ".$productName, 400);

                    $createdDate = new DateTime();
                    $createdDate->setTimestamp($eventData['created']);

                    $updatedData = new DateTime();
                    $updatedData->setTimestamp($eventData['created']);
                    $updatedData->modify("+1 month");

                    $paymentId = $eventData['payment_intent'];
                    $subscriptionId = $eventData['subscription'];

                    // $user = DatabaseUserAccount::byId($userId);
                    // $subscription = DatabaseUserSubscription::get($user);

                    // if($subscription->isChangeable()){
                    //     //remboursement
                    //     DatabaseUserSubscription::cancel($user);
                        
                    // }

                    DatabaseUserSubscription::create(
                        $productName,
                        $createdDate->format('Y-m-d H:i'), 
                        $eventData['amount_total'] / 100, 
                        $updatedData->format('Y-m-d H:i'), 
                        $eventData['id'],
                        $paymentId,
                        $subscriptionId
                    );

                    break;
            }
        }catch(Exception $e){
            Http::sendError($e);
        }

        return;
    }

    Http::sendError(new Exception("Invalid request", 400));