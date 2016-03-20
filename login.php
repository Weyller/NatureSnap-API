<?php
		session_start();
		require '../db.php';
		$dbConn = getConnection();
        if(!empty($_POST['username']) && !empty(SHA1($_POST['password']))){
            $sql = "SELECT * FROM users WHERE username=:username AND password=:password";
            $namedParameters = array();
            $namedParameters[":username"] = $_POST['username'];
            $namedParameters[":password"] = SHA1($_POST['password']);
            $stmt = $dbConn -> prepare($sql);
            $stmt -> execute($namedParameters);
            $result = $stmt->fetch();
        }
		
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
