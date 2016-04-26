<?php
header('Content-type: application/json');
require '../db.php';
$dbConn = getConnection();	

if(!empty($_GET['photo_id']) && (int)$_GET['photo_id']){
    views($_GET['photo_id']);
    getPhoto();
} elseif(!empty($_GET['group_id']) && (int)$_GET['group_id']){
    getGroupPhotos();
} else {
    echo json_encode(["success"=>false]);
}

//Get one photo
function getPhoto(){
    global $dbConn;
    $sql = "SELECT * FROM photos INNER JOIN users ON users.user_id = photos.user_id WHERE photos.photo_id=:photo_id";
    $namedParameters = array();
    $namedParameters[":photo_id"] = $_GET['photo_id'];
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetch();
    if(!empty($result)){
        //Initialize PHP array
        $data = array(
            'photo_id'=>$result['photo_id'],
            'name'=>$result['name'],
            'image_name'=>$result['image_title'],
            'description'=>$result['description'],
            'priv'=>$result['private'],
            'views'=>$result['views'],
            'success'=>true
        );
        //PHP array to JSON array
        echo json_encode($data, JSON_NUMERIC_CHECK);
    } else {
        echo json_encode(array('success'=>false));
    }
}

//Get all photos from group
function getGroupPhotos(){
    global $dbConn;
    $sql = "SELECT * FROM photos INNER JOIN group_photos ON photos.photo_id = group_photos.photo_id INNER JOIN users ON users.user_id = photos.user_id INNER JOIN groups ON groups.group_id = group_photos.group_id WHERE group_photos.group_id =:group_id";
    $namedParameters = array();
    $namedParameters[":group_id"] = $_GET['group_id'];
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $result = $stmt->fetchAll();

    //Declaray PHP array
    $data = [];
    if(!empty($result)){
        //Add data to PHP array
        foreach($result as $photos){
            $data[] = [
                'photo_id'=>$photos['photo_id'],
                'name'=>$photos['name'],
                'group_name'=>$photos['group_name'],
                'group_id'=>$photos['group_id'],
                'image_name'=>$photos['image_title'],
                'description'=>$photos['description'],
                'priv'=>$photos['private'],
                'views'=>$photos['views']
            ];
        }
        //PHP array to JSON array
        echo json_encode(array(
            'data' => $data,
            'success' => true
        ), JSON_NUMERIC_CHECK);
    } else {
        echo json_encode(["data"=>[]]);
    }
} 
//Increase views counter every time a GET request for a photo is sent
function views($photo_id){
    global $dbConn;
    //Get Number of views of photo
    $sql = "SELECT * FROM photos WHERE photo_id=:photo_id";    
    $namedParameters = array();
    $namedParameters[':photo_id'] = $photo_id;
    $stmt = $dbConn->prepare($sql);
    $stmt->execute($namedParameters); 
    $result = $stmt->fetch();
    $views = $result['views'];
    
    //Increment views counter
    $sql = "UPDATE photos SET views=:views WHERE photo_id=:photo_id";
    $namedParameters = array();
    $namedParameters[':views'] = $views + 1;
    $namedParameters[':photo_id'] = $photo_id;
    $stmt = $dbConn->prepare($sql);
    $stmt->execute($namedParameters); 
}
