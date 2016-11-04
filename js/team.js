function getTeam(user){
  //Github
  $.ajax({
    url: 'https://api.github.com/users/'+user,
    dataType: 'json',
    type: 'GET',
    success: function(data) {  
        var photo = $("<img class='card-img-top'>").css("width","100%").attr("src",data.avatar_url);
        var card_title = $("<h4 class='card-title'>").append(data.name);
        var card_text = $("<p class='card-text'>").append(data.bio);
        var card_block = $("<div class='card-block'>").append(card_title);
        var card = $("<div class='card'>").css("width","22%").append(photo).append(card_block);
        $("#team").append(card);
    } 
  }); 
}
getTeam("juanv911");
getTeam("firestar");
getTeam("nishi101");
getTeam("marielaceja");