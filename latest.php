<?php
header('Content-type: application/json');
require '../db.php';
$dbConn = getConnection();	

//Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && $_GET['limit'] < 20 ? (int)$_GET['limit'] : 10;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

$sql = "SELECT * FROM photos INNER JOIN users ON users.user_id = photos.user_id NATURAL LEFT OUTER JOIN group_photos LIMIT {$start}, {$limit}";
$stmt = $dbConn -> prepare($sql);
$stmt -> execute();
$result = $stmt->fetchAll(); 

//Declaray PHP array
$data = [];
foreach($result as $photos){
//Add data to PHP array
$data[] = [
    'photo_id'=>$photos['photo_id'],
    'name'=>$photos['name'],
    'image_name'=>$photos['image_title'],
    'description'=>$photos['description'],
    'latitude'=>$photos['latitude'],
    'longitude'=>$photos['longitude'],
    'group_id'=>$photos['group_id'],
    'private'=>$photos['private'],
    'views'=>$photos['views']
];
}
//If photo does not belong to a group, then remove the group_id key from array
//So it doesn't return a null value for all non-group photos
for($i=0; $i<count($data); $i++){
   if(empty($data[$i]['group_id'])){
       unset($data[$i]['group_id']);
   } 
}
//PHP array to JSON array
echo json_encode(array(
 'data' => $data 
), JSON_NUMERIC_CHECK);