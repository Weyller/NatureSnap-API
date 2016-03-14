<?php
    require '../db.php';
    session_start();
    $username = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
	
	$description = $_POST['description'];
    $dbConn = getConnection();	
	
	if(isset($_POST['updateForm']) && !empty($_POST['photo_id']) && !empty($_SESSION['username'])) {
		
		if($_FILES['filename']['tmp_name']){
	        $imageType = exif_imagetype($_FILES['filename']['tmp_name'] ); //Returns 1 if gif, 2 if jpg, 3 if png
	        if($imageType !=1 && $imageType !=2 && $imageType !=3) {
	            unlink($_FILES['filename']['tmp_name']);//Delete files
	        }
		}
			
		if($_FILES['filename']['tmp_name'] || !empty($_POST['description'])){
			//Retrieve image title using the photo_id parameter
			$sql = "SELECT image_title, username FROM photos INNER JOIN users ON users.user_id = photos.user_id WHERE photo_id=:photo_id AND users.user_id=:user_id";
		    $namedParameters = array();
			$namedParameters[':photo_id'] = $_POST['photo_id'];
			$namedParameters[':user_id'] = $user_id;
		    $stmt = $dbConn->prepare($sql);
		    $stmt->execute($namedParameters);  
			$result = $stmt->fetch(); 
			
			//Prevent one user from updating other user's photos
			if($result['username'] == $username){
				if($_FILES['filename']['tmp_name']){
					//Unlink old photo before inserting new photo
					$oldFile = "".$result['image_title']."";
					unlink($oldFile);
	
					//Uploads Directory, user folders will be created here for every user when they upload their first image
				    $target_dir = "uploads";
	
					//Move file into the user folder
					$filename = $target_dir."/". $_SESSION['username'] . "/" .basename($_FILES['filename']['name']);
					move_uploaded_file($_FILES['filename']['tmp_name'], $filename );
				}
				//Insert entry into database
				if(empty($_POST['description'])){
					$sql = "UPDATE photos SET image_title=:image_title WHERE photo_id=:photo_id";
				}
				else if(!empty($_POST['description']) && empty($_FILES['filename']['tmp_name'])){
					$sql = "UPDATE photos SET description=:description WHERE photo_id=:photo_id";
				}
				else {
			    	$sql = "UPDATE photos SET image_title=:image_title, description=:description WHERE photo_id=:photo_id";
				}
			    $namedParameters = array();
				if($_FILES['filename']['tmp_name']){
			    	$namedParameters[':image_title'] = $filename;
				}
				if(!empty($_POST['description'])){
					$namedParameters[':description'] = $description;
				}
				$namedParameters[':photo_id'] = $_POST['photo_id'];
				
			    $stmt = $dbConn->prepare($sql);
			    $stmt->execute($namedParameters);  
				echo "success";
			}
			else {
				echo "unauthorized";
			}
			}
	else{
		echo "invalid";
	}
		}
//No parameters provided
else{
	echo "invalid";
}
