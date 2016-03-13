<?php
		session_start();
		require 'db.php';
		$dbConn = getConnection();
		$username = $_POST['username'];
		$password = sha1($_POST['password']);
		$sql = "SELECT * FROM users WHERE username=:username AND password=:password";
		$namedParameters = array();
		$namedParameters[":username"] = $username;
		$namedParameters[":password"] = $password;
		$stmt = $dbConn -> prepare($sql);
		$stmt -> execute($namedParameters);
		$result = $stmt->fetch();
		
	if(empty($result)) {
		echo "error";	
	}
	else {
		//user_id and username sessions for logged in user,
		//SESSION will be replace with SharedPreferences from Android
		$_SESSION['username'] = $result['username'];
		$_SESSION['user_id'] = $result['user_id'];
		echo "success";
	}	
	

?>
