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
                $(".row").prepend(options);
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
        var photo_link = $("<a>").attr("href","page.php?photo_id="+item.photo_id).html(item.timestamp);
        var timestamp = $("<span>").append(item.timestamp);
        var card = $("<div class='card-block'>").append(photo_link,description);

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
function uploadPhoto(){
    
}
function updatePhoto(){
    
}
function deletePhoto(){
    
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