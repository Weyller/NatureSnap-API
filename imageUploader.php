<?php
require '../db.php';
session_start();
$dbConn = getConnection();	

if(isset($_POST['uploadForm']) && !empty($_SESSION['username']) || preg_match('/([a-zA-Z0-9_-]+)/s', !empty($_POST['groupName'])) ) {
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    $description = $_POST['description'];
        
    if($_FILES['filename']['tmp_name']){
        $imageType = exif_imagetype($_FILES['filename']['tmp_name'] ); //Returns 1 if gif, 2 if jpg, 3 if png
        if($imageType !=1 && $imageType !=2 && $imageType !=3) {
            unlink($_FILES['filename']['tmp_name']);//Delete files
        }	
        else {
            //Uploads Directory
            $target_dir = "uploads";

            //Create directory for each user
            if (!file_exists($target_dir."/".$_SESSION['username'])) { 
                mkdir($target_dir."/".$_SESSION['username'], 0777, true); 
             }
            
            //Set 777 permisions to each user folder to allow deleting and updating folder and files
            $old = umask(0);
            chmod($target_dir."/".$_SESSION['username'], 0777);
            umask($old);

            //Handle empty spaces
            $photo = str_replace(' ', '', $_FILES['filename']['name']);
            
            //Check if file exist in user folder or in user group folder
            if(!empty($_POST['groupName']) && file_exists($target_dir."/".$_SESSION['username']."/".$_POST['groupName']."/".basename($photo ))){
                echo "exists";
            } elseif(empty($_POST['groupName']) && file_exists($target_dir."/".$_SESSION['username']."/".basename($photo ))){
                echo "exists";
            }   
            //If file does not exists, then move file into the user folder or group folder
            else {                
                if(!empty($_POST['groupName'])){
                    $group = $_POST['groupName'];
                    $filename = $target_dir."/".$_SESSION['username']."/".$_POST['groupName']."/".basename($photo);  
                    //Check if group exists, also check if photo in group exists
                    $checkPhotoGroup = photoGroupExist($username, $photo, $group);
                    $checkGroup = groupExist($username, $group);
                    if($checkPhotoGroup != true && $checkGroup != false){
                        addGroupPhoto($user_id, $filename, $description, $checkGroup );
                    } else {
                        echo "error";
                    }
                }
                //If no group is included, then check for regular photo only
                else {
                    $filename = $target_dir."/". $_SESSION['username'] . "/" .basename($photo);
                    $checkPhoto = photoExist($username, $photo);
                    if($checkPhoto == false){
                        addPhoto($user_id, $filename, $description);
                    } else {
                        echo "error";
                    }
                }
            }
        }
    } else {
        echo "nothing";
    }
} else {
    echo "invalid";
}
//Check if entry for photo  already exists
function photoExist ($username, $photo){
    global $dbConn;
    $sql = "SELECT * FROM users INNER JOIN photos ON users.user_id = photos.user_id WHERE username=:username AND image_title=:image_title";
    $namedParameters = array();
    $namedParameters[":username"] = $username;
    $namedParameters[":image_title"] = $photo;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($username == $result['username'] && $photo == $result['image_title']){
        return true;
    } else {
        return false;
    }		
}
//Check if entry for group already exists
function groupExist ($username, $group){
    global $dbConn;
    $sql = "SELECT * FROM users INNER JOIN groups ON users.user_id = groups.user_id WHERE username=:username AND group_name=:groupName";
    $namedParameters = array();
    $namedParameters[":username"] = $username;
    $namedParameters[":groupName"] = $group;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($username == $result['username'] && $group == $result['group_name']){
        return (int)$result['group_id'];
    } else {
        return false;
    }	
}
//Check if entry for photo in group already exists
function photoGroupExist ($username, $photo, $group){
    global $dbConn;
    $sql = "SELECT * FROM users INNER JOIN photos ON users.user_id = photos.user_id INNER JOIN groups ON users.user_id = groups.user_id WHERE username=:username AND image_title=:image_title AND group_name=:groupName";
    $namedParameters = array();
    $namedParameters[":username"] = $username;
    $namedParameters[":image_title"] = $photo;
    $namedParameters[":groupName"] = $group;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($username == $result['username'] && $photo == $result['image_title'] && $group == $result['group_name']){
        return true;
    } else {
        return false;
    }	
}
//Add photo
function addPhoto($user_id, $filename, $description){
    global $dbConn;
    move_uploaded_file($_FILES['filename']['tmp_name'], $filename );
    if (file_exists($filename)) {  
        $sql = "INSERT INTO photos (image_title, user_id, description) VALUES(:image_title, :user_id,:description)";
        $namedParameters = array();
        $namedParameters[':image_title'] = $filename;
        $namedParameters[':user_id'] = $user_id;
        $namedParameters[':description'] = $description;
        $stmt = $dbConn->prepare($sql);
        $stmt->execute($namedParameters);  
        echo "success:".$dbConn->lastInsertId();  
    } else {
        echo "error";
    }
}
//Add photo to group
function addGroupPhoto($user_id, $filename, $description, $groupId){
    global $dbConn;
    move_uploaded_file($_FILES['filename']['tmp_name'], $filename );
    //Verify that the file was uploaded
    if (file_exists($filename)) {  
        $sql = "INSERT INTO photos (image_title, user_id, group_id, description) VALUES(:image_title, :user_id,:group_id,:description)";
        $namedParameters = array();
        $namedParameters[':image_title'] = $filename;
        $namedParameters[':user_id'] = $user_id;
        $namedParameters[':description'] = $_POST['description'];
        $namedParameters[':group_id'] =  $groupId;  
        $stmt = $dbConn->prepare($sql);
        $stmt->execute($namedParameters);  
        echo "success:".$dbConn->lastInsertId();  
    } else {
        echo "error";
    }
}
