<?php session_start() ?>
<?php include("head.php");?>
<?php include("header.php");?>

<div class="album">
    <div class="container">
        <div class="row">
        <div id="photo"></div>
        </div>
    </div>
</div>

<?php include("footer.php");?>

<?php if(!empty($_GET['photo_id']) && (int)$_GET['photo_id']) : ?>
<script>
    <?php 
        $photo_id = $_GET['photo_id'];
        $username = "null";
           if( !empty($_SESSION['username'])) {
                $username = $_SESSION['username'];
            }
    ?>
    getPhoto(<?php echo $photo_id ?>, "<?php echo $username ?>");
        
</script>
<?php endif; ?>
