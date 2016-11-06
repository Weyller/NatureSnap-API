<?php
header('Content-type: application/json');
require '../../../db.php';
$dbConn = getConnection();	
if(!empty($_POST['name']) && preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['name']) 
   && !empty($_POST['last_name']) && preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['last_name']) 
   && !empty($_POST['username']) && preg_match('/([a-zA-Z0-9]+)/s', $_POST['username']) 
   && !empty($_POST['password']) 
   && !empty($_POST['email']) && preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i', $_POST['email'])) {
    $firstName = $_POST['name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = sha1($_POST['password']);
    $checkUser = userExist($username, $email);
    if($checkUser == false){
        $ip = "";
        if(!empty($_SERVER['REMOTE_ADDR'])){
            $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        }
        $sql = "INSERT INTO users (name, last_name, email, ip_address, username, password) VALUES(:firstName, :lastName, :email, :ip_address, :username, :password)";
        $namedParameters = array();
        $namedParameters[":firstName"]= $firstName;
        $namedParameters[":lastName"] = $lastName;
        $namedParameters[":email"] = $email;
        $namedParameters[":ip_address"] = $ip;
        $namedParameters[":username"] = $username;
        $namedParameters[":password"] = $password;
        $stmt = $dbConn->prepare($sql);
        $stmt->execute($namedParameters);   
        echo json_encode("success");
    } else {
        echo json_encode("exists");
    }
} else {
    echo json_encode("nothing");
}

//Check if username or email already exist
function userExist ($username,$email){
    global $dbConn;
    $sql = "SELECT * FROM users WHERE username=:username OR email=:email";
    $namedParameters = array();
    $namedParameters[":username"] = $username;
    $namedParameters[":email"] = $email;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($username == $result['username'] || $email == $result['email']){
        return true;
    } else {
        return false;
    }	
}