<?php session_start();?>
<?php include("head.php");?>
  <body>
      <?php include("header.php");?>
    <section class="jumbotron text-xs-center">
      <div class="container">
        <h1 class="jumbotron-heading">Welcome to <strong>NatureSnap</strong></h1>
        <p class="lead text-muted">Something short and leading about the collection below—its contents, the creator, etc. Make it short and sweet, but not too short so folks don't simply skip over it entirely.</p>
        <p>
          <a href="https://github.com/nishi101/NatureSnap-API" class="btn btn-primary">Fork Project on GitHub</a>
        </p>
      </div>
    </section>
<?php include("footer.php");?>
  </body>
</html>



<!DOCTYPE html>
<html lang="en">

<body>

    <div class="album">
    <div class="container">
        <div class="row"></div>
    </div>
</div>
<div class="container">


     
<div class="uploader">
    
      <!--div class="form">
          <h3>Upload Image</h3>
      <form method="POST" enctype="multipart/form-data" action="api/v1/imageUploader.php">
          <input type="file" name="filename" />
          <br>Latitude: <br><input type="text" name="latitude" /><br>
          <br>Longitude: <br><input type="text" name="longitude" /><br>
          <br>Group Name: <br><input type="text" name="groupName" /><br>
          <br>Description: <br><input type="text" name="description" /><br>
          Private: <input type="checkbox" name="private" /><br><br>
          <input type="submit" value="Upload" name="uploadForm" />
      </form>
</div-->

      <!--div class="form">
          <h3>Update Image</h3>
      <form method="post" enctype="multipart/form-data" action="updateImage.php">
          <input type="file" name="filename" />
          <br>Photo ID: <br><input type="text" name="photo_id" />
          <br>Description: <br><input type="text" name="description" /><br>
          <input type="submit" value="Update" name="updateForm" />
      </form>
</div-->

      <!--div class="form">
          <h3>Delete Image</h3>
      <form method="post" action="api/v1/deletePhoto.php">
          Photo ID: <br><input type="text" name="photo_id" />
          <br><input type="submit" value="Delete" name="deleteForm" />
      </form>
</div-->
</div>
<br>
<div class="uploader">
      <!--div class="form">
          <h3>Create Group</h3>
      <form method="post" action="group.php">
          Group Name: <br><input type="text" name="groupName" />
          <br><input type="submit" value="Create" name="addGroup" />
      </form>
</div-->
  <div class="form">
      <h3>Delete Group</h3>
  <form method="post" action="group.php">
      Group Name: <br><input type="text" name="groupName" />
      <br><input type="submit" value="Delete" name="deleteGroup" />
  </form>
</div>


  <div class="form">
      <h3>Update/Edit Group</h3>
  <form method="post" action="group.php">
      Old Group Name <br><input type="text" name="oldGroup" />
      <br>Old New Name <br><input type="text" name="newGroup" />
      <br><input type="submit" value="Edit" name="editGroup" />
  </form>
</div>
</div>
<br>
<!-- HANDLES COMMENTS (ADDING, DELETING, EDITING/UPDATING) -->

<!--div class="comment">

      <div class="form">

          <h3>Add Comment</h3>

      <form method="post" action="comments.php">

        Photo ID: <br /><input type="text" name="photoId" /><br/>

          Add a comment: <br><input type="text" name="commentText" />

          <br><input type="submit" value="Create" name="addComment" />

      

      </form>

</div-->
    



</div>
</div>

    
</body>



<script>

    
latest();
    login();
</script>


</html>
