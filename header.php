<div class="navbar navbar-static-top navbar-dark menu">
  <div class="container">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="team.php">Team</a></li>
        <?php if(!empty($_SESSION['username'])):?>
        <div class="user">
            <div class="dropdown">
            <button class="dropbtn"><span class="name"><?php echo $_SESSION['username'] ?></span><span class="icon">&#9776;</span></button>
            <div class="dropdown-content">
              <li data-toggle="modal" data-target="#uploadPhoto">Upload Photo</li>
              <li data-toggle="modal" data-target="#createGroup">Create Group</li>
              <li onclick="logout();">Logout</li>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </ul>

<?php if(empty($_SESSION['username'])):?>
<form class="navbar-form navbar-right form-signin" role="form" onclick="login();">
    <input id="username" type="text"  name="username" value="" placeholder="Username">                                        
    <input id="password" type="password"  name="password" value="" placeholder="Password">                                        
    <button type="submit" class="btn btn-primary">Login</button>
</form>
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
      <form method="POST" enctype="multipart/form-data">
          <input type="file" name="filename" />
          <br>Latitude: <br><input type="text" id="latitude" name="latitude" /><br>
          <br>Longitude: <br><input type="text" id="longitude" name="longitude" /><br>
          <br>Group Name: <br><input type="text" id="groupName" name="groupName" /><br>
          <br>Description: <br><input type="text" id="description" name="description" /><br>
          Private: <input type="checkbox" id="private" name="private" /><br><br>
          <input type="submit" value="Upload" name="uploadForm" />
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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