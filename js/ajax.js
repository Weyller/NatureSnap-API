/*
    Author: Juan Vargas
    Project: NatureSnap API
    Version 1.0
*/
function getPhoto(photo_id,username){
    $.ajax({
        url:'api/v1/photo.php',
        type:'GET',
        dataType:'JSON',
        data:{
            photo_id: photo_id
        },
        success: function(data){
            $("<img>").attr("src",data.image_name).appendTo("#photo");
            if(username==data.username){
                //Create Delete Button for logged in user
                var options = $("<div class='card'>");
                var deleteBtn = $("<a class='btn btn-danger'>").attr("href", photo_id).html("Delete").appendTo(options);
                var updateBtn = $("<a class='btn btn-primary'>").attr("href", photo_id).html("Update").appendTo(options);
                $(".row").append(options);
            } 
        }
    });
}

function login(){
    $('.form-signin').submit(function(){      
      $.ajax({
        url: 'api/v1/login.php', 
        type: 'POST',
        dataType:'JSON',
        data: {
          username:$("#username").val(),
          password:$("#password").val()
        },
        success: function(data){
          if(data.success == true){
            location.reload(); 
          } else {
            $(".form-signin #username").addClass("red"); 
            $(".form-signin #password").addClass("red"); 
          }
        }
      });
      return false;
    });
}
function logout(){
    $.ajax({
        url:'api/v1/logout.php',
        dataType:'JSON',
        success: function(data){
            if(data=="logout"){
                window.location.href="index.php";
            }
        }
    });
}
function latest(){
  $.ajax({
    url:'api/v1/latest.php',
    type:'GET',
    dataType:'JSON',
    success: function(response){
      $.each(response.data, function(i, item) {
        var author = $("<span>").append(item.name);
        var image = $("<img class='card-img-top'>").attr("src",item.image_name);
        var description = $("<p class='card-text'>").append(item.description);
        var photo_id = $("<span>").append(item.photo_id);
        var likeIcon = $("<li>").append("<span data-photo='"+item.photo_id+"'"+"class='like fa fa-heart-o'>").append(item.likes);
        var commentIcon = $("<li>").append("<span class='like fa fa-comment-o'>");
        var link = $("<a class='date'>").attr("href","page.php?photo_id="+item.photo_id).html(item.timestamp);
        var photo_link = $("<li class='right'>").append(link);
        var statsList = $("<ul class='stats'>").append(likeIcon,commentIcon,photo_link)
        var timestamp = $("<span>").append(item.timestamp);
        var card = $("<div class='card-block'>").append(statsList,description);
        var item = $("<div class='card'>").append(image,card);
        $(".row").append(item);
        var $container = $('.album .row');
        $container.imagesLoaded( function() {
          $container.masonry({
            itemSelector: '.card'
          }); 
        });
      });
    }
  });    
}
function registration(){
    
}
function uploadPhoto(event){
    event.stopPropagation();
    event.preventDefault();
    $.ajax({
        url:'api/v1/imageUploader.php',
        type:'POST',
        dataType:'JSON',
        cache: false,
        processData: false,
        contentType: false,
        data:{
            uploadForm:'',
            latitude: $("#latitude").val(),
            longitude: $("#longitude").val(),
            private: $("#private").val(),
            groupName: $("#groupName").val()
        },
        success: function(data){
            
        }
    });
}
function updatePhoto(){
    
}
function deletePhoto(photo_id){
    $.ajax({
        url:'api/v1/deletePhoto.php',
        type:'POST',
        dataType:'JSON',
        data:{
            deleteForm: '',
            photo_id: photo_id
        },
        success: function(data){
            if(data=="success"){
               window.location.href="index.php";
            }
        }
        
    });
}
function likePhoto(photo_id){
    $.ajax({
        url:'api/v1/likes.php',
        type:'POST',
        dataType:'JSON',
        data:{
            likeForm: '',
            photo_id: photo_id
        },
        success: function(data){
            if(data=="success"){
               window.location.href="index.php";
            }
        },
        error: function(){
            alert("error");
        }
        
    });
}
function createGroup(){
    
}
function deleteGroup(){
    
}
function editGroup(){
    
}
function addComment(){
    
}
function editComment(){
    
}
function deleteComment(){
    
}