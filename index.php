<?php
	session_start();
	include 'functions.php';
	echo "max_input_time: ".ini_get('max_input_time');
	echo"<br>";
		echo "post_max_size: ".ini_get('post_max_size');
	echo"<br>";
		echo "upload_max_filesize: ".ini_get('upload_max_filesize');

	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container">
	<div class="form-signin">
<form method="post" action="login.php" enctype="multipart/form-data">
	<h3>Sign In</h3>
	Username: </br><input placeholder="jane" type="text" name="username" /></br>
	Password: </br><input placeholder="doe" type="password" name="password" /></br>
	<input type="submit" name="loginForm" value="Login">
</form>

      <p>User: jane</p>
      <p>Pass: doe</p>
     </div>
     <div class="form-signup">
     	<form method="post" action="register.php" enctype="multipart/form-data">

	<h3>Sign Up</h3>
	Name: </br><input type="text" name="name" /></br>
	Last Name: </br><input type="text" name="last_name" /></br>
	Email: </br><input type="text" name="email" /></br>
	Username: </br><input type="text" name="username" /></br>
	Password: </br><input type="password" name="password" /></br>
	<input type="submit" name="registerForm" value="Register">     	
     	</form>
     </div>
     <div class="search">
         <h3>Search Photos</h3>
      <form action="searchPhoto.php" method="GET" id="search">
      	<input placeholder="nature" type="text" name="search">
      </form>
      <button type="submit" form="search" value="Submit">Search</button>
</div>
      
      <?php if(!empty($_SESSION['username'])){?><h3 class="logged">Current logged in user: <?=$_SESSION['username']?></h3><?php }?>

      <div class="form">
          <h3>Upload Image</h3>
      <form method="post" enctype="multipart/form-data" action="imageUploader.php">
          <input type="file" name="filename" />
          <br>Description: <br><input type="text" name="description" /><br>
          <input type="submit" value="Upload" name="uploadForm" />
      </form>
</div>

      <div class="form">
          <h3>Update Image</h3>
      <form method="post" enctype="multipart/form-data" action="updateImage.php">
          <input type="file" name="filename" />
          <br>Photo ID: <br><input type="text" name="photo_id" />
          <br>Description: <br><input type="text" name="description" /><br>
          <input type="submit" value="Update" name="updateForm" />
      </form>
</div>

      <div class="form">
          <h3>Delete Image</h3>
      <form method="post" action="deletePhoto.php">
          Photo ID: <br><input type="text" name="photo_id" />
          <br><input type="submit" value="Delete" name="deleteForm" />
      </form>
</div>
<div class="items">
<?=getUserImages()?>
</div>
<br/>
<div class="items">
<?=geAllImages()?>
</div>
<br><br>
	<table border="1">
  <tr>
    <th>Description</th>
    <th>Status</th>
  </tr>
  <tr class="done">
    <td>Login</td>
    <td>Done</td>
  </tr>
  <tr class="done">
    <td>Registration</td>
    <td>Done</td>
  </tr>
    <tr class="done">
    <td>Image Upload</td>
    <td>Done</td>
  </tr>
  <tr class="done">
    <td>Create a folder for each user when they upload their first photo</td>
    <td>Done</td>
  </tr>
    <tr class="done">
    <td>Update Image &  Information</td>
    <td>Done</td>
  </tr>
    <tr class="done">
    <td>Delete Image &  database entry</td>
    <td>Done</td>
  </tr>
    <tr class="done">
    <td>Search Photos</td>
    <td>Done</td>
  </tr>
   <tr class="done">
    <td>Latest Photos</td>
    <td>Done</td>
  </tr>
     <tr class="working">
    <td>JSON Parsing</td>
    <td></td>
  </tr>
      <tr class="working">
    <td>API Endpoints Documentation</td>
    <td></td>
  </tr>
</table>
<br>

<table border="1">
  <tr>
  	<th>Name</th>
    <th>API Endpoints</th>
    <th>Parameters</th>
    <th>Type</th>
    <th>Format</th>
  </tr>
  <tr class="done">
  	<td>Search</td>
    <td>http://52.90.35.254/searchPhoto.php</td>
    <td>search</td>
    <td>GET</td>
    <td>JSON</td>
  </tr>
    <tr class="done">
  	<td>Latest Photos</td>
    <td>http://52.90.35.254/latest.php</td>
    <td>limit</td>
    <td>GET</td>
    <td>JSON</td>
  </tr>
    <tr class="done">
  	<td>Login</td>
    <td>http://52.90.35.254/login.php</td>
    <td>username, password</td>
    <td>POST</td>
    <td></td>
  </tr>
  <tr class="done">
  	<td>Register</td>
    <td>http://52.90.35.254/register.php</td>
    <td>name, last_name, email, username, password</td>
    <td>POST</td>
    <td></td>
  </tr>
  <tr class="done">
  	<td>Image Upload</td>
    <td>http://52.90.35.254/imageUploader.php</td>
    <td>filename, description</td>
    <td>POST</td>
    <td></td>
  </tr>
    <tr class="done">
  	<td>Image Delete</td>
    <td>http://52.90.35.254/deletePhoto.php</td>
    <td>photo_id, username, user_id</td>
    <td>POST</td>
    <td></td>
  </tr>
      <tr class="done">
  	<td>Image Update</td>
    <td>http://52.90.35.254/updateImage.php</td>
    <td>photo_id, filename, username, user_id</td>
    <td>POST</td>
    <td></td>
  </tr>
</table>
<br>

</div>


</body>
</html>