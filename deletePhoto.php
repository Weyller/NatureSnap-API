<?php
    require 'db.php';
    session_start();
    $username = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
	$dbConn = getConnection();	
	
	if(isset($_POST['deleteForm']) && !empty($_POST['photo_id']) && !empty($_SESSION['username'])) {

	//Retrieve image title using the photo_id parameter
	$sql = "SELECT image_title, username FROM photos INNER JOIN users ON users.user_id = photos.user_id WHERE photo_id=:photo_id AND users.user_id=:user_id";
    $namedParameters = array();
	$namedParameters[':photo_id'] = $_POST['photo_id'];
	$namedParameters[':user_id'] = $user_id;
    $stmt = $dbConn->prepare($sql);
    $stmt->execute($namedParameters);  
	$result = $stmt->fetch(); 
	
	//Prevent one user from deleting other user's photos
	if($result['username'] == $username){	
		//Unlink old photo before inserting new photo
		$oldFile = "".$result['image_title']."";
		unlink($oldFile);
			
		//Delete entry from database
		$sql = "DELETE FROM photos WHERE photo_id=:photo_id AND user_id=:user_id";
	    $namedParameters = array();
		$namedParameters[':photo_id'] = $_POST['photo_id'];
		$namedParameters[':user_id'] = $user_id;
		
	    $stmt = $dbConn->prepare($sql);
	    $stmt->execute($namedParameters);  
		echo "success";
	}
	else {
		echo "unauthorized";
	}

}
else {
	echo "invalid";
}


		
 	
?>
