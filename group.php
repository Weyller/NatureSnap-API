<?php
require '../db.php';
session_start();
$dbConn = getConnection();

//Add group to database
if(isset($_POST['addGroup']) && !empty($_SESSION['user_id']) && preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['groupName']) ){
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    $group = $_POST['groupName'];

    $checkGroup = groupExist($user_id, $group);
    if($checkGroup == false){
        $target_dir = "uploads";
        //Check for user folder
        if (!file_exists($target_dir."/".$_SESSION['username'])) {    
          mkdir($target_dir."/".$_SESSION['username'], 0777, true); 
        }
        //777 permisions
        $old_user = umask(0);
        chmod($target_dir."/".$_SESSION['username'], 0777);
        umask($old_user);

        //Create group folder inside user folder
        if (!file_exists($target_dir."/".$_SESSION['username'].'/'.$group)) {    
          mkdir($target_dir."/".$_SESSION['username'].'/'.$group, 0777, true); 
        }
        //777 permisions
        $old_group = umask(0);
        chmod($target_dir."/".$_SESSION['username'].'/'.$group, 0777);
        umask($old_group);

        //Check group folder exists
        if (file_exists($target_dir."/".$_SESSION['username'].'/'.$group)) {  
            $sql = "INSERT INTO groups (group_name, user_id) VALUES(:group_name, :user_id)";
            $namedParameters = array();
            $namedParameters[":group_name"] = $group;
            $namedParameters[":user_id"] = $user_id;
            $stmt = $dbConn -> prepare($sql);
            $stmt -> execute($namedParameters);
            echo "success:".$dbConn->lastInsertId();
        } else {
            echo "error";
        }
    } else {
        echo "exists";
    }
} 
//Delete group from database
elseif(isset($_POST['deleteGroup']) && !empty($_SESSION['user_id']) && preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['groupName']) && !empty($_SESSION['username']) ){
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    $group = $_POST['groupName'];

    //Check if group entry exist in the database
    $checkGroup = groupExist($user_id, $group);

    if($checkGroup == true){
        $target_dir = "uploads";

        //Check if user folder exists
        if (!file_exists($target_dir."/".$username)) {    
          echo "invalid"; 
        } else {    
            if(!file_exists($target_dir."/".$username.'/'.$group)){
                echo "invalid";
            } else {
                deleteFolder($target_dir."/".$username.'/'.$group);
                $sql = "DELETE FROM groups WHERE group_name=:group_name AND user_id=:user_id";
                $namedParameters = array();
                $namedParameters[":group_name"] = $group;
                $namedParameters[":user_id"] = $user_id;
                $stmt = $dbConn -> prepare($sql);
                $stmt -> execute($namedParameters);
                echo "success";
            }
        }
    } else {
        echo "error";
    }
} else {
    echo "error";
}

//Check if group already exist
function groupExist ($user_id, $group_name){
    global $dbConn;
    $sql = "SELECT * FROM groups WHERE user_id =:user_id AND group_name=:group_name";
    $namedParameters = array();
    $namedParameters[":user_id"] = $user_id;
    $namedParameters[":group_name"] = $group_name;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($user_id == $result['user_id'] && $group_name == $result['group_name']){
        return true;
    } else {
        return false;
    }		
}
//Delete group folder and all files
function deleteFolder($dir){
    $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file) { 
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
    } 
    return rmdir($dir); 
} 
