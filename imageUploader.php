<?php
require '../db.php';
session_start();
$dbConn = getConnection();	

if(isset($_POST['uploadForm']) && !empty($_SESSION['user_id']) || preg_match('/([a-zA-Z0-9_-]+)/s', !empty($_POST['groupName'])) ) {
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
            if (!file_exists($target_dir."/".$_SESSION['user_id'])) { 
                mkdir($target_dir."/".$_SESSION['user_id'], 0777, true); 
             }
            
            //Set 777 permisions to each user folder to allow deleting and updating folder and files
            $old = umask(0);
            chmod($target_dir."/".$_SESSION['user_id'], 0777);
            umask($old);

            //Handle empty spaces
            $photo = str_replace(' ', '', $_FILES['filename']['name']);
            
            $group_id = false;
            
           if(!empty($_POST['groupName'])){
                $group = $_POST['groupName'];
                $group_id = getGroupId($group, $user_id);
           }
                               
            //Check if file exist in user folder or in user group folder
            if(!empty($_POST['groupName']) && $group_id != false && file_exists($target_dir."/".$_SESSION['user_id']."/".$group_id."/".basename($photo ))){
                echo "exists";
            } elseif(empty($_POST['groupName']) && file_exists($target_dir."/".$_SESSION['user_id']."/".basename($photo ))){
                echo "exists";
            }   
            //If file does not exists, then move file into the user folder or group folder
            else {                
                if($group_id !=false){
                    //Initialize group and file paths
                    $filename = $target_dir."/".$_SESSION['user_id']."/".$group_id."/".$photo;  
                    $group_dir = $target_dir."/".$_SESSION['user_id']."/".$group_id;

                    //Check if group exists, also check if photo in group exists
                    $checkPhotoGroup = photoGroupExist($user_id, $photo, $group);
                    $checkGroup = groupExist($user_id, $group);
                    if($checkPhotoGroup != true && $checkGroup != false && file_exists($group_dir)){
                        move_uploaded_file($_FILES['filename']['tmp_name'], $filename );
                        addGroupPhoto($user_id, $filename, $description, $checkGroup );
                    } else {
                        echo "error";
                    }
                }
                
                //If no group is included, then check for regular photo only
                if($group_id ==false){
                    $filename = $target_dir."/". $_SESSION['user_id'] . "/" .basename($photo);
                    $checkPhoto = photoExist($user_id, $photo);
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
function photoExist ($user_id, $photo){
    global $dbConn;
    $sql = "SELECT * FROM users INNER JOIN photos ON users.user_id = photos.user_id WHERE users.user_id=:user_id AND image_title=:image_title";
    $namedParameters = array();
    $namedParameters[":user_id"] = $user_id;
    $namedParameters[":image_title"] = $photo;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($user_id == $result['user_id'] && $photo == $result['image_title']){
        return true;
    } else {
        return false;
    }		
}
//Check if entry for group already exists
function groupExist ($user_id, $group){
    global $dbConn;
    $sql = "SELECT * FROM users INNER JOIN groups ON users.user_id = groups.user_id WHERE users.user_id=:user_id AND group_name=:groupName";
    $namedParameters = array();
    $namedParameters[":user_id"] = $user_id;
    $namedParameters[":groupName"] = $group;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($user_id == $result['user_id'] && $group == $result['group_name']){
        return (int)$result['group_id'];
    } else {
        return false;
    }	
}
//Check if entry for photo in group already exists
function photoGroupExist ($user_id, $photo, $group){
    global $dbConn;
    $sql = "SELECT * FROM users INNER JOIN photos ON users.user_id = photos.user_id INNER JOIN groups ON users.user_id = groups.user_id WHERE users.user_id=:user_id AND image_title=:image_title AND group_name=:groupName";
    $namedParameters = array();
    $namedParameters[":user_id"] = $user_id;
    $namedParameters[":image_title"] = $photo;
    $namedParameters[":groupName"] = $group;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($user_id == $result['user_id'] && $photo == $result['image_title'] && $group == $result['group_name']){
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
    //Verify that the file was uploaded
    if (file_exists($filename)) {  
        $sql = "INSERT INTO photos (image_title, user_id, description) VALUES(:image_title, :user_id,:description)";
        $namedParameters = array();
        $namedParameters[':image_title'] = $filename;
        $namedParameters[':user_id'] = $user_id;
        $namedParameters[':description'] = $_POST['description'];
        $stmt = $dbConn->prepare($sql);
        $stmt->execute($namedParameters);  
        
        //Check if previous execution affected the rows
        //if so, then get lastInsertId() as the photo_id
        //then insert that id into the photo_groups table
        if ($stmt->rowCount()){
            $photo_id = $dbConn->lastInsertId();
            $sql = "INSERT INTO group_photos (group_id, photo_id) VALUES(:group_id,:photo_id)";
            $namedParameters = array();
            $namedParameters[':group_id'] =  $groupId;  
            $namedParameters[':photo_id'] =  $photo_id;  
            $stmt = $dbConn->prepare($sql);
            $stmt->execute($namedParameters); 
            echo "success";  
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}
//Get group_id if it exist
function getGroupId($group, $user_id){
    global $dbConn;
    $sql = "SELECT * FROM users INNER JOIN groups ON users.user_id = groups.user_id WHERE users.user_id=:user_id AND group_name=:group_name";
    $namedParameters = array();
    $namedParameters[':group_name'] = $group;
    $namedParameters[':user_id'] = $user_id;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if($group = $result['group_name'] && $user_id = $result['user_id']){
        return $result['group_id'];
    } else {
        return false;
    }
}