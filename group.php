<?php
require '../db.php';
session_start();
$dbConn = getConnection();

//Add group to database
if(isset($_POST['addGroup']) && !empty($_SESSION['user_id']) && preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['groupName']) ){
    $user_id = $_SESSION['user_id'];
    $group_name = $_POST['groupName'];

    //Check if group name exists
    $checkGroup = groupExist($user_id, $group_name);
    if($checkGroup == false){
        //Uploads Directory
        $target_dir = "uploads";
        
        //Check for user folder
        if (!file_exists($target_dir."/".$_SESSION['user_id'])) {    
          mkdir($target_dir."/".$_SESSION['user_id'], 0777, true); 
        }
        //777 permisions for user folder
        $old_user = umask(0);
        chmod($target_dir."/".$_SESSION['user_id'], 0777);
        umask($old_user);
        
        //Insert group into database
        $sql = "INSERT INTO groups (group_name, user_id) VALUES(:group_name, :user_id)";
        $namedParameters = array();
        $namedParameters[":group_name"] = $group_name;
        $namedParameters[":user_id"] = $user_id;
        $stmt = $dbConn -> prepare($sql);
        $stmt -> execute($namedParameters);
        echo "success:".$dbConn->lastInsertId();        
        
        //Use $dbConn->lastInsertId() to get last inserted id,
        //use that id to create the group folder
        $last = $dbConn->lastInsertId();
        
        //Create group folder inside user folder
        if (!file_exists($target_dir."/".$_SESSION['user_id'].'/'.$last)) {    
          mkdir($target_dir."/".$_SESSION['user_id'].'/'.$last, 0777, true); 
        }
        //777 permisions for group folder
        $old_group = umask(0);
        chmod($target_dir."/".$_SESSION['user_id'].'/'.$last, 0777);
        umask($old_group);
    } else {
        echo "error";
    }
} 
//Delete group from database
elseif(isset($_POST['deleteGroup']) && !empty($_SESSION['user_id']) && preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['groupId']) ){
    $user_id = $_SESSION['user_id'];
    $group_id = $_POST['groupId'];

    //Check if group entry exist in the database
    $checkGroup = groupIdExist($user_id, $group_id);

    if($checkGroup == true){
        $target_dir = "uploads";

        //Check if user folder exists
        //If it exists, then delete files and database entries
        if (!file_exists($target_dir."/".$user_id)) {    
          echo "invalid"; 
        } else {    
            if(!file_exists($target_dir."/".$user_id.'/'.$group_id)){
                echo "invalid";
            } else {
                deleteFolder($target_dir."/".$user_id.'/'.$group_id);
                deleteGroupPhotos($group_id, $user_id);
                $sql = "DELETE FROM groups WHERE group_id=:group_id AND user_id=:user_id";
                $namedParameters = array();
                $namedParameters[":group_id"] = $group_id;
                $namedParameters[":user_id"] = $user_id;
                $stmt = $dbConn -> prepare($sql);
                $stmt -> execute($namedParameters);
                echo "success";
            }
        }
    } else {
        echo "error";
    }
} 
//Edit group name
elseif(isset($_POST['editGroup']) && !empty($_SESSION['user_id']) && preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['oldGroup']) && preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['newGroup']) ){
    editGroup($_SESSION['user_id'],$_POST['oldGroup'],$_POST['newGroup']);
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
//Delete all photos from group
function deleteGroupPhotos($group_id, $user_id){
    global $dbConn;    
    $sql = "DELETE FROM photos WHERE group_id=:group_id AND user_id=:user_id";
    $namedParameters = array();
    $namedParameters[":group_id"] = $group_id;
    $namedParameters[":user_id"] = $user_id;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);    
}
//Check if group ID exists
//If exists, then delete group folder, files and entries
function groupIdExist ($user_id, $group_id){
    global $dbConn;
    $sql = "SELECT * FROM groups WHERE user_id =:user_id AND group_id=:group_id";
    $namedParameters = array();
    $namedParameters[":user_id"] = $user_id;
    $namedParameters[":group_id"] = $group_id;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($user_id == $result['user_id'] && $group_id == $result['group_id']){
        return true;
    } else {
        return false;
    }		
}
//Change old group name with new group name
function editGroup ($user_id, $old, $new){
    global $dbConn;
    $sql = "SELECT * FROM groups WHERE user_id =:user_id AND group_name=:group_name";
    $namedParameters = array();
    $namedParameters[":user_id"] = $user_id;
    $namedParameters[":group_name"] = $old;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();  
    
    //Before updating, check if group name exist and belongs to user_id
    if($result['group_name']==$old){
        $sql = "UPDATE groups SET group_name=:new WHERE user_id=:user_id AND group_name=:old";
        $namedParameters = array();
        $namedParameters[":user_id"] = $user_id;
        $namedParameters[":old"] = $old;
        $namedParameters[":new"] = $new;
        $stmt = $dbConn -> prepare($sql);
        $stmt -> execute($namedParameters);
        //Check if rows were affected after execution
        if ($stmt->rowCount()){
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
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