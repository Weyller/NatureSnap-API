<?php
 	session_start();
    require '../db.php';
    $dbConn = getConnection();	
	$username = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
			
	function getUserImages(){
		global $dbConn;
		global $user_id;
		global $username;
	    $sql = "SELECT * FROM photos INNER JOIN users ON users.user_id = photos.user_id WHERE users.user_id=:user_id";
	    $namedParameters = array();
	    $namedParameters[":user_id"] = $user_id;
	    $stmt = $dbConn -> prepare($sql);
	    $stmt -> execute($namedParameters);
	    $result = $stmt->fetchAll(); 
		if(!empty($result)){
			echo "<h3>".$username."'s images</h3>";
		foreach($result as $photos){
			echo '<div class="item">';
			echo '<img src='.$photos["image_title"].'>';
			echo "<p><strong>Author</strong>: ".$photos['name']." ".$photos['last_name'];
			echo "<br>";
			echo "<strong>Image Path:</strong> ".$photos['image_title'];
			echo "<br>";
			echo "<strong>image Id:</strong> ".$photos['photo_id'];
			if(!empty($photos['description'])){
				echo "<br>";
				echo "<strong>Description:</strong> ".$photos['description'];
			}
			echo '</p></div>';
			}
		}
	}

	function geAllImages(){
		global $dbConn;
	    $sql = "SELECT * FROM photos INNER JOIN group_photos ON group_photos.photo_id = photos.photo_id INNER JOIN users ON users.user_id = photos.user_id INNER JOIN groups on groups.group_id = group_photos.group_id ORDER BY photos.photo_id DESC";
	    $stmt = $dbConn->prepare($sql);
	    $stmt->execute();
	    $result = $stmt->fetchAll(); 
		echo "<h3>All images</h3>";
		
		foreach($result as $photos){
			echo '<div class="item">';
			echo '<img src='.$photos["image_title"].'>';
			echo "<p><strong>Author</strong>: ".$photos['name']." ".$photos['last_name'];
			echo "<br>";
			echo "<strong>Image Path:</strong> ".$photos['image_title'];
			echo "<br>";
			echo "<strong>image Id:</strong> ".$photos['photo_id'];
            echo "<br>";
            if(!empty($photos['group_name'])){
               echo "<strong>Goup Name:</strong> ".$photos['group_name']; 
            }
			if(!empty($photos['description'])){
				echo "<br>";
				echo "<strong>Description:</strong> ".$photos['description'];
			}
			echo '</p></div>';
		}
	}


?>
