
<div class="navbar-collapse collapse inverse" id="navbar-header">
  <div class="container-fluid">
    <div class="about">
      <h4>About</h4>
      <p class="text-muted">Add some information about the album below, the author, or any other background context. Make it a few sentences long so folks can pick up some informative tidbits. Then, link them off to some social networking sites or contact information.</p>
    </div>
    <div class="social">
      <h4>Contact</h4>
      <ul class="list-unstyled">
        <li><a href="#">Follow on Twitter</a></li>
        <li><a href="#">Like on Facebook</a></li>
        <li><a href="#">Email me</a></li>
      </ul>
    </div>
  </div>
    
</div>
<div class="navbar navbar-static-top navbar-dark menu">
  <div class="container">
    <a href="index.php">Home</a>
      <a href="team.php">Team</a>

<?php if(empty($_SESSION['username'])):?>
<form class="navbar-form navbar-right form-signin" role="form" onclick="login();">
    <input id="username" type="text"  name="username" value="" placeholder="Username">                                        
    <input id="password" type="password"  name="password" value="" placeholder="Password">                                        
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<?php endif; ?>
<?php if(!empty($_SESSION['username'])):?>
      <div class="user">
            <div class="dropdown">
            <button class="dropbtn"><span class="name"><?php echo $_SESSION['username'] ?></span><span class="icon">&#9776;</span></button>
            <div class="dropdown-content">
              <a href="latest" data-toggle="modal" data-target="#uploadPhoto">Upload Photo</a>
              <a href="popular" data-toggle="modal" data-target="#createGroup">Create Group</a>
              <a href="api/v1/logout.php">Logout</a>
            </div>
          </div>
          </div>
<?php endif; ?>
</div>
</div>

<!-- Upload Photo Modal -->
<div class="modal fade" id="uploadPhoto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">Upload Image</h4>
      </div>
      <div class="modal-body">
             <form method="POST" enctype="multipart/form-data" action="api/v1/imageUploader.php">
          <input type="file" name="filename" />
          <br>Latitude: <br><input type="text" name="latitude" /><br>
          <br>Longitude: <br><input type="text" name="longitude" /><br>
          <br>Group Name: <br><input type="text" name="groupName" /><br>
          <br>Description: <br><input type="text" name="description" /><br>
          Private: <input type="checkbox" name="private" /><br><br>
          <input type="submit" value="Upload" name="uploadForm" />
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
    
    <!-- Create Group Modal -->
<div class="modal fade" id="createGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">Create Goup</h4>
      </div>
      <div class="modal-body">
      <form method="post" action="group.php">
          Group Name: <br><input type="text" name="groupName" />
          <br><input type="submit" value="Create" name="addGroup" />
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>