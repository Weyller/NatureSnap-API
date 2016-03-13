<?php
	header('Content-type: application/json');
	require 'db.php';
	$dbConn = getConnection();	
	
	if($_GET['search']){
		$sql = "SELECT * FROM photos INNER JOIN users ON users.user_id = photos.user_id  WHERE image_title LIKE :search";
	    $namedParameters = array();
	    $namedParameters[":search"] = "%".$_GET['search']."%";
	    $stmt = $dbConn -> prepare($sql);
	    $stmt -> execute($namedParameters);
	    $result = $stmt->fetchAll(); 

		//Declaray PHP array
		$data = [];
		//SQL array to PHP array
		foreach($result as $photos){
			$data[] = [
				'photo_id'=>$photos['photo_id'],
				'name'=>$photos['name'],
				'image_name'=>$photos['image_title'],
				'description'=>$photos['description']
			];
		}
		//PHP array to JSON array
		echo json_encode(array(
			 'success' => true,
			 'data' => $data 
		));
	}
	else {
		echo json_encode(array(
			 'success' => false
		));
	}
