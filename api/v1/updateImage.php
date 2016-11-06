<?php
require '../../../db.php';
session_start();
$dbConn = getConnection();	
	
if(isset($_POST['updateForm']) && !empty($_POST['photo_id']) && !empty($_SESSION['user_id']) || preg_match('/([a-zA-Z0-9_-]+)/s', isset($_POST['description']))) {
    $user_id = $_SESSION['user_id'];

    //Check if file is in a valid format
    if($_FILES['filename']['tmp_name']){
        $imageType = exif_imagetype($_FILES['filename']['tmp_name'] ); //Returns 1 if gif, 2 if jpg, 3 if png
        if($imageType !=1 && $imageType !=2 && $imageType !=3) {
            unlink($_FILES['filename']['tmp_name']);//Delete files
        }
    }
    //If file is in a valid format, then don't delete and move it to the appropriate location
    //Retrieve image title and user_id using the photo_id parameter
    if($_FILES['filename']['tmp_name'] || !empty($_POST['description'])){
        $sql = "SELECT image_title, users.user_id FROM photos INNER JOIN users ON users.user_id = photos.user_id WHERE photo_id=:photo_id AND users.user_id=:user_id";
        $namedParameters = array();
        $namedParameters[':photo_id'] = $_POST['photo_id'];
        $namedParameters[':user_id'] = $user_id;
        $stmt = $dbConn->prepare($sql);
        $stmt->execute($namedParameters);  
        $result = $stmt->fetch(); 

        //Check if photo belongs to group
        $group_id = checkGroupPhoto($user_id, $_POST['photo_id']);

        //Prevent one user from updating other user's photos
        if($result['user_id'] == $user_id){

            //Boolean for editing files
            $editFile = 0;

            $filename = null;
            if($_FILES['filename']['tmp_name']){
                //Uploads Directory, user folders will be created here for every user when they upload their first image
                $target_dir = "uploads";

                //If a group photo is updated, then move the uploaded file into
                //the correct group folder, do the same for regular photos
                if($group_id !=false){ //Move file into the group folder folder
                    $filename = $target_dir."/".$_SESSION['user_id']."/".$group_id."/".basename($_FILES['filename']['name']);
                    if(!file_exists($filename)){
                        //Unlink old photo before inserting new photo
                        $oldFile = "".$result['image_title']."";
                        if(file_exists($oldFile)){
                           unlink($oldFile);
                        }
                        move_uploaded_file($_FILES['filename']['tmp_name'], $filename );
                        $editFile = 1;
                    } else {
                        $filename = null;

                    }
                } else { //Move file into the user folder
                    $filename = $target_dir."/". $_SESSION['user_id'] . "/" .basename($_FILES['filename']['name']);
                    if(!file_exists($filename)){
                        //Unlink old photo before inserting new photo
                        $oldFile = "".$result['image_title']."";
                        if(file_exists($oldFile)){
                           unlink($oldFile);
                        }
                        move_uploaded_file($_FILES['filename']['tmp_name'], $filename );
                        $editFile = 1;
                    } else {
                        $filename = null;
                    }
                }
            }
            //Call function only if one of the two parameters is valid
            //Check if file has been proccesses or if a description is provided
            //One of both parameters can be sent
            if($editFile == true || !empty($_POST['description'] )){
                updatePhoto($editFile, $_POST['description'], $filename, $_POST['photo_id']);
            } else {
                echo json_encode("error");
            }
        } else {
            echo json_encode("unauthorized");
        }

    } else {
        echo json_encode("invalid");
   }
} else{ //No parameters are provided
    echo json_encode("invalid");
}

//Check if photo belongs to a group
function checkGroupPhoto($user_id,$photo_id){
    global $dbConn;
    $sql = "SELECT * FROM group_photos INNER JOIN photos ON group_photos.photo_id = photos.photo_id WHERE photos.user_id=:user_id AND group_photos.photo_id=:photo_id";
    $namedParameters = array();
    $namedParameters[':photo_id'] = $photo_id;
    $namedParameters[':user_id'] = $user_id;
    $stmt = $dbConn->prepare($sql);
    $stmt->execute($namedParameters);  
    $result = $stmt->fetch();   
    //Return group id if photo belongs to a group
    if($photo_id = $result['photo_id'] && $user_id = $result['user_id']){
        return $result['group_id'];
    } else {
        return false;
    }
}
//Edit group photos and regular photos
//If only description is provided, only edit description
//If only file is provided, only edit file
//If both parameters are sent, then edit both attributes
//It will return success if one of the parameters 
//succeeds even if the other fails
function updatePhoto($editFile, $description, $filename, $photo_id){
    global $dbConn;
    if(empty($description) && $editFile == 1){
        $sql = "UPDATE photos SET image_title=:image_title WHERE photo_id=:photo_id";
    } elseif(!empty($description) && $editFile == 0){
        $sql = "UPDATE photos SET description=:description WHERE photo_id=:photo_id";
    } elseif(!empty($description) && $editFile == 1){
        $sql = "UPDATE photos SET image_title=:image_title, description=:description WHERE photo_id=:photo_id";
    }
    $namedParameters = array();
    $namedParameters[':photo_id'] = $photo_id;
    if(empty($description) && $editFile == 1){
        $namedParameters[':image_title'] = $filename;
    } elseif(!empty($description) && $editFile == 0){
        $namedParameters[':description'] = $description;
    } elseif(!empty($description) && $editFile == 1){
        $namedParameters[':description'] = $description;
        $namedParameters[':image_title'] = $filename;
    }
    $stmt = $dbConn->prepare($sql);
    $stmt->execute($namedParameters);  
    echo json_encode("success");
}