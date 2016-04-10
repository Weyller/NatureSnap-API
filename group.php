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
        if ($stmt->rowCount()){
            echo "success";  
        } else {
            echo "error";
        }
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
elseif(isset($_POST['deleteGroup']) && !empty($_SESSION['user_id']) && preg_match('/([a-zA-Z0-9_-]+)/s', !empty($_POST['groupName']))){
    $user_id = $_SESSION['user_id'];
    $groupName = $_POST['groupName'];

    //Get group id, if not group id is found, then return null
    $group_id = getGroupId($user_id, $groupName);

    if($group_id != null){
        $target_dir = "uploads";
        if (!file_exists($target_dir."/".$user_id)) {    
          echo "invalid"; 
        } else {    
            if(!file_exists($target_dir."/".$user_id.'/'.$group_id)){
                echo "invalid";
            } else {
                deleteGroupPhotos($target_dir, $group_id, $groupName, $user_id);
                //deleteFolder($target_dir."/".$user_id.'/'.$group_id);
        
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
function deleteGroupPhotos($target_dir, $group_id, $groupName, $user_id){
    global $dbConn;   
    
    //We start our transaction.
    $dbConn->beginTransaction();
    
    try {
        $sql = "DELETE FROM groups WHERE group_name=:group_name AND user_id=:user_id";
        $namedParameters = array();
        $namedParameters[":group_name"] = $groupName;
        $namedParameters[":user_id"] = $user_id;
        $stmt = $dbConn -> prepare($sql);
        $stmt -> execute($namedParameters);

        //Retrieve all photos from group_photos, 
        //then use them to delete photos from photos table
        $sql = "SELECT * FROM group_photos INNER JOIN photos ON photos.photo_id = group_photos.photo_id WHERE user_id =:user_id AND group_id=:group_id";
        $namedParameters = array();
        $namedParameters[":user_id"] = $user_id;
        $namedParameters[":group_id"] = $group_id;
        $stmt = $dbConn -> prepare($sql);
        $stmt -> execute($namedParameters);
        $result = $stmt->fetchAll();

        //Delete all photos that belong to a group in the photos table
        foreach($result as $photos) {
            $sql = "DELETE FROM photos WHERE photo_id=:photo_id";
            $namedParameters = array();
            $namedParameters[":photo_id"] = $photos['photo_id'];
            $stmt = $dbConn -> prepare($sql);
            $stmt -> execute($namedParameters);  
        }
        //Delete entries from group photos
        $sql = "DELETE FROM group_photos WHERE group_id=:group_id";
        $namedParameters = array();
        $namedParameters[":group_id"] = $group_id;
        $stmt = $dbConn -> prepare($sql);
        $stmt -> execute($namedParameters);  
        
        //Commit changes
        $dbConn->commit();
        
        //Finally, delete group folder and files
        deleteFolder($target_dir."/".$user_id.'/'.$group_id);
        
        echo "success";
    } catch(Exception $e){
        echo "error";
        $dbConn->rollBack();
    }
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
//Group ID
function getGroupId($user_id, $groupName){
    global $dbConn;
    $sql = "SELECT * FROM groups WHERE user_id =:user_id AND group_name=:group_name";
    $namedParameters = array();
    $namedParameters[":user_id"] = $user_id;
    $namedParameters[":group_name"] = $groupName;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();  
    if($user_id == $result['user_id'] && $groupName = $result['group_name']){
        return (int)$result['group_id'];
    } else {
        return null;
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