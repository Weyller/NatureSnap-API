<?php
function getConnection() {
	$host  = "localhost";
	$dbname  ="";
	$username = "";
	$password = "";
		
	// Establishing a connection
	$dbConn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

	// Setting Errorhandling to Exception
	$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	return $dbConn;

}
getConnection();
?>