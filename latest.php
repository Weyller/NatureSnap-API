<?php
	header('Content-type: application/json');
	require 'db.php';
	$dbConn = getConnection();	
	$limit = $_GET['limit'];
	if($_GET['limit']){
		$sql = "SELECT * FROM photos INNER JOIN users ON users.user_id = photos.user_id  LIMIT ".$limit;
	    $stmt = $dbConn -> prepare($sql);
	    $stmt -> execute();
	    $result = $stmt->fetchAll(); 

		//Declaray PHP array
		$data = [];
		foreach($result as $photos){
			//SQL array to PHP array
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
