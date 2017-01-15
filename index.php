<?php session_start();?>
<?php include("head.php");?>
<?php include("header.php");?>
<section class="jumbotron text-xs-center">
  <div class="container">
    <h1 class="jumbotron-heading">Welcome to <strong>NatureSnap</strong></h1>
    <p class="lead text-muted">Something short and leading about the collection below—its contents, the creator, etc. Make it short and sweet, but not too short so folks don't simply skip over it entirely.</p>
    <p>
      <a href="https://github.com/juanv911/NatureSnap-API" class="btn btn-primary">Fork Project on GitHub</a>
    </p>
  </div>
</section>
<div class="album">
    <div class="container">
        <div class="row"></div>
    </div>
</div>
<?php include("footer.php");?>
<script>  
latest();

//Call likePhoto Function
$(document).on("click",".like",function(e) {
    e.preventDefault();
    var photo_id = $(this).attr("data-photo");
    likePhoto(photo_id);
});
</script>


