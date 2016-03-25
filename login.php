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
        if($result['username'] == $_POST['username'] && $result['password'] == SHA1($_POST['password']) ){
            updateDate($result['username'], SHA1($_POST['password']));
            $_SESSION['username'] = $result['username'];
            $_SESSION['user_id'] = $result['user_id'];
            echo "success";
        } else {
            echo "error";
        }
    } else {
    echo "invalid";	
}

//Coordinated Universal Time (UTC)
function updateDate($username, $password){
    global $dbConn;
    date_default_timezone_set('UTC');
    $ip = "";
    if(!empty($_SERVER['REMOTE_ADDR'])){
        $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    }
    $sql = "UPDATE users SET latest_login=:latest_login, ip_address=:ip_address WHERE username=:username AND password=:password";   
    $namedParameters = array();
    $namedParameters[":latest_login"] = date('Y-m-d H:i:s', time());
    $namedParameters[":ip_address"] = $ip;
    $namedParameters[":username"] = $username;
    $namedParameters[":password"] = $password;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);    
}
