<?php
/*
    Author: Mariela Ceja
    Project: NatureSnap API - Comments
    Version 1.0
*/
require '../../../db.php';
$dbConn = getConnection();

//  Will be using
//	commentText - Text stored for comment
//	addComment - Submit button for comments
// 	commentID - Id used to find comment to delete
//	deleteComment - deleting comment BUTTON
//	oldCommentId - ID of comment to be edited/updated
//	newCommentId - ID given to new comment
//  (Can't we just forget about giving it a new ID and simply
//	keep the old ID and use the SQL update function?)
//	preg_match('/([a-zA-Z0-9_-]+)/s', $_POST['groupName'])) Accepts only certain characters (sanitizes input) 
//	prevents empty spaces and no input
	
if(isset($_POST['addComment']) && !empty($_SESSION['user_id']) && preg_match('/([a-zA-Z0-9_-]+)/s', !empty($_POST['commentText'])) && !empty($_POST['photoId'])){
    insertComment();
}
function insertComment(){
    global $dbConn;
    $user_id = $_SESSION['user_id'];
    $photo_id = $_POST['photoId'];
    $comment = $_POST['commentText'];
    //Before adding a comment, check if
    if(isPhotoId($photo_id, $user_id)==TRUE){
        $sql = "INSERT INTO comment(photo_id,commentor_id,comment) VALUES(:photo_id,:commentor_id,:comment)";
        $namedParameters[":photo_id"] = $photo_id;
        $namedParameters[":commentor_id"] = $user_id;
        $namedParameters[":comment"] = $comment;
        $stmt = $dbConn -> prepare($sql);
        $stmt -> execute($namedParameters);
        if ($stmt->rowCount()){
            echo "success";  
        } else {
            echo "error";
        }
    } else{
        echo "failed";
    }
}
function isPhotoId($photo_id,$user_id){
    global $dbConn;
    $sql = "SELECT photo_id, user_id FROM photos WHERE photo_id=:photo_id AND user_id=:user_id";
    $namedParameters[":photo_id"] = $photo_id;
    $namedParameters[":user_id"] = $user_id;
    $stmt = $dbConn -> prepare($sql);
    $stmt -> execute($namedParameters);
    $record=$stmt->fetch();

    if($record['photo_id'] == $photo_id && $record['user_id'] == $user_id){
        return true;
    }
    else{
        return false;
    }
}
function editComment(){
    global $dbConn;
        $user_id = $_SESSION['user_id'];
        $photoId = $_POST['photoId'];
        $comment = $_POST['commentText'];
        echo "hello_bye";
        echo isPhotoId($photoId, $user_id);
        if(isPhotoId($photoId, $user_id)==true){
            echo "hello";
            $sql = "INSERT INTO comment(photo_id,commentor_id,comment) VALUES(:photoId,:user_id,:comment)";
            $namedParameters[":photo_id"] = $photo_id;
            $namedParameters[":commentor_id"] = $user_id;
            $namedParameters[":comment"] = $comment;
            $stmt = $dbConn -> prepare($sql);
            $stmt -> execute($namedParameters);
            if ($stmt->rowCount()){
                echo "success";  
            } else {
                echo "error";
            }
        }
        else{
            echo "failed";
        }
}
function deleteComment($commentor_id, $comment_id){
    global $dbConn;
        //echo "hello_bye";
        //echo isPhotoId($photoId, $user_id);
            echo "hello";
            $sql = "SELECT * FROM comment WHERE comment_id=:commentId AND user_id=:user_id";
            $stmt = $dbConn -> prepare($sql);
            $namedParameters[":comment_id"] = $comment_id; 
            $namedParameters[":commentor_id"] = $commentor_id;
            $stmt -> execute($namedParameters);
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if($comment_id==$result['comment_id'] && commentor_id==$result['commentor_id']){
                $sql = "DELETE FROM comment WHERE comment_id=:comment_id";
                $statement = $dbConn->prepare($sql);
                $namedParameters = array();
                $namedParameters[":comment_id"] = $comment_id; 
                $statement->execute();
            if ($statement->rowCount()){
                echo "success";  
            } else {
                echo "error";
            }

            }

            //$sql = "INSERT INTO  ";

        //}
        //else{
        //	echo "failed";
        //}
}
function displayComment(){
    global $dbConn;
        $user_id = $_SESSION['user_id'];
        $photoId = $_POST['photoId'];
        $comment = $_POST['commentText'];
        echo "hello_bye";
        echo isPhotoId($photoId, $user_id);
        if(isPhotoId($photoId, $user_id)==true){
            echo "hello";
            $sql = "INSERT INTO comment(photo_id,commentor_id,comment) VALUES(:photoId,:user_id,:comment)";
            $namedParameters = array();
            $namedParameters[":photo_id"] = $photo_id;
            $namedParameters[":commentor_id"] = $user_id;
            $namedParameters[":comment"] = $comment;
            $stmt = $dbConn -> prepare($sql);
            $stmt -> execute($namedParameters);
            if ($stmt->rowCount()){
                echo "success";  
            } else {
                echo "error";
            }
        }
        else{
            echo "failed";
        }
}