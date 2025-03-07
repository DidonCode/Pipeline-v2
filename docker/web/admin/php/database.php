<?php
	$address = "database";
	$username = "root";
	$password = "butify";

	$database = "butify";

	try {
		$pdoDatabase = new PDO("mysql:host=".$address.";dbname=".$database, $username, $password);

		return $pdoDatabase;
	}catch(Exception $e) {
		throw new Exception("Database connection failed.");
	}
?>
