<?php
header('Content-type: application/json');
require '../db.php';
$dbConn = getConnection();	

//Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && $_GET['limit'] < 20 ? (int)$_GET['limit'] : 10;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

//Pages
$rows = $dbConn->query("SELECT count(photo_id) FROM  photos")->fetchColumn();
$total_pages = ceil($rows/$limit);

//Fetch attributes related to each photo
//Display group_id and group_name for each item that belongs to a group
//If photo has no group, then remove those values from the specific item
$sql = "SELECT photo_id,name,image_title,description,latitude,longitude,group_id,group_name, private, views FROM photos NATURAL JOIN users NATURAL LEFT JOIN group_photos NATURAL LEFT JOIN groups LIMIT {$start}, {$limit}";
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
    'group_name'=>$photos['group_name'],
    'private'=>$photos['private'],
    'views'=>$photos['views']
];
}
//If photo does not belong to a group, then remove the group_id key from array
//So it doesn't return a null value for all non-group photos
for($i=0; $i<count($data); $i++){
   if(empty($data[$i]['group_id']) || $data[$i]['group_id'] == null){
       unset($data[$i]['group_id']);
       unset($data[$i]['group_name']);
   } 
}
//PHP array to JSON array
echo json_encode(array(
 'data' => $data 
), JSON_NUMERIC_CHECK);