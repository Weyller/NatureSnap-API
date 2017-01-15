<?php
require '../../../db.php';
session_start();
$dbConn = getConnection();	
if(isset($_POST['likeForm']) && !empty($_POST['photo_id']) && !empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $photo_id = $_POST['photo_id'];
    likePhoto($user_id, $photo_id); 
}

function likePhoto($user_id, $photo_id){
    global $dbConn;
    $sql = "INSERT INTO likes (user_id, photo_id) VALUES(:user_id, :photo_id)";
    $namedParameters = array();
    $namedParameters[':user_id'] = $user_id;
    $namedParameters[':photo_id'] = $photo_id;
    $stmt = $dbConn->prepare($sql);
    $stmt->execute($namedParameters);  
    echo json_encode("success");

}    
?>