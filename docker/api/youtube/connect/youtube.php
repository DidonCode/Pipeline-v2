<?php

	function callYoutubeApi($options) {
		global $pdoDatabase;
		
		try{
			$date = date("Y")."-".date("m")."-".date("d");

			$request = $pdoDatabase->prepare("UPDATE youtube SET exedeed = 0, date = ? WHERE date < ?");
			$request->execute(array($date, $date));
			
			while(true){
				$request = $pdoDatabase->prepare("SELECT value FROM youtube WHERE exedeed = 0");
				$request->execute();
				$keys = $request->fetchAll();

				if(count($keys) == 0) throw new Exception("Error Youtube API quota exceeded.", 400);

				$youtubeKey = $keys[0]['value'];

				$options[CURLOPT_URL] .= "&key=".$youtubeKey;

				$curl  = curl_init();
				curl_setopt_array($curl, $options);

				$json  = curl_exec($curl);
				$error = curl_error($curl);

				curl_close($curl);

				if($error) throw new Exception("Error request for Youtube API: ".$error, 400);

				$data  = json_decode($json);
				if(is_null($data)) throw new Exception("Error response from Youtube API: ".json_last_error_msg(), 400);

				if(isset($data->error)){ 
					if($data->error->code == 400) throw new Exception("Error Youtube API key invalid.", 400);
					if($data->error->code == 403) {
						$request = $pdoDatabase->prepare("UPDATE youtube SET exedeed = 1 WHERE value = ?");
						$request->execute(array($youtubeKey));
					}
				}else{
					return $data;
				}
			}
		} catch(Exception $e){
			throw $e;
		}

	}
?>