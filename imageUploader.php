<?php
    require '../db.php';
    session_start();
    $username = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
	$description = $_POST['description'];
    $dbConn = getConnection();	

    //Upload image form
    if(isset($_POST['uploadForm']) && !empty($_SESSION['username'])) {
    	if($_FILES['filename']['tmp_name']){
	        $imageType = exif_imagetype($_FILES['filename']['tmp_name'] ); //Returns 1 if gif, 2 if jpg, 3 if png
	        if($imageType !=1 && $imageType !=2 && $imageType !=3) {
	            unlink($_FILES['filename']['tmp_name']);//Delete files
	        }	
			else {
				//Uploads Directory, user folders will be created here for every user when they upload their first image
			    $target_dir = "uploads";
				
			    //Create directory for each user
			    if (!file_exists($target_dir."/".$_SESSION['username'])) {    
			      mkdir($target_dir."/".$_SESSION['username'], 0777, true); 
			   }
				
				//Set 777 permisions to each user folder to allow deleting and updating folder and files
				$old = umask(0);
				chmod($target_dir."/".$_SESSION['username'], 0777);
				umask($old);
				
				//Check if file in user folder already exists
				if(file_exists( $target_dir."/". $_SESSION['username'] . "/" . basename($_FILES['filename']['name'] ))){
					echo "exists";
				}
				else {
					//Move file into the user folder
					$filename = $target_dir."/". $_SESSION['username'] . "/" .basename($_FILES['filename']['name']);
					move_uploaded_file($_FILES['filename']['tmp_name'], $filename );
					
					//Insert entry into database
				    $sql = "INSERT INTO photos (image_title, user_id, description) VALUES(:image_title, :user_id,:description)";
				    $namedParameters = array();
				    $namedParameters[':image_title'] = $filename;
				    $namedParameters[':user_id'] = $user_id;
					$namedParameters[':description'] = $description;
				    $stmt = $dbConn->prepare($sql);
				    $stmt->execute($namedParameters);  
					echo "success:".$dbConn->lastInsertId();
				
				}
			}
		}
		else {
			echo "nothing";
		}
    }
	else {
		echo "invalid";
	}
