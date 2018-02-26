//fajl sluzi da hendluje klikove na like ili dislike btn ispod komentara postova koji su u show.blade.php, okacen je u app.blade.php layoutu na 
//koji se kaci show.blade.php vju koji prikazuje zaseban post i njegove komentare koji se mogu lajkovati ili dislajkovati
$(document).ready(function(){	

  //kad se klikne na like ili dislike btn (oba imaju klasu likedislikebtn samo razlicit atribut likeordislike (like ili dislike))
  $('.likedislikebtn').on('click', function(e){
  	var commentid = $(this).attr('commentid');//uzimamo id komentara
  	var likeordislike = $(this).attr('likeordislike');// uzimamo koju ikonu je kliknuo korisnik(moze biti like ili dislike)
  	//alert(likeordislike);
    //saljemo AJAX preko rute '/likeordislike koja gadja likeordislike() metod CommentsControllera, likeordislikeurl i _token su definisani na dnu
    //show.blade.php vjua, saljemo id komentara i u likeordislike saljemo da li je user lajkovao ili dislajkovao komentar da bi kontroler znao -
    //koju kolonu 'comments' tabele da poveca za 1
  	$.ajax({ 
  	  method: 'POST',
  	  url: likeordislikeurl,
  	  data: { likeordislike: likeordislike, commentid: commentid, _token: token }
    })
    //kad likeordislike() metod CommentsControllera vrati response
    .done(function(o){
      console.log(o);
      var content = '';//varijabla u koju cemo ubaciti novi HTML za div #likeordislike+IDKOMENTARA u show.blade.php
      if(o.likeordislike == 'like'){//ako je user lajkovao komentar
        content += '<p class="likeilidislike text-success text-center">Liked</p>';//novi HTML
        //u <span id="likes+IDKOMENTARA"> u show.blade.php je trenutni broj lajkova koje ima komentar
        var lajkova = parseInt($('#likes'+commentid).text());
        //alert(lajkova);
        //ako je komentar lajkovan taj broj povecavamo za 1
        $('#likes'+commentid).html(lajkova + 1);
      }else{//ako je user dislajkovao komentar
        content += '<p class="likeilidislike text-danger text-center">Disliked</p>';//novi HTML
        //u <span id="dislikes+IDKOMENTARA"> u show.blade.php je trenutni broj dislajkova koje ima komentar
        var dislajkova = parseInt($('#dislikes'+commentid).text());
        //alert(dislajkova);
        //ako je komentar dislajkovan taj broj povecavamo za 1
        $('#dislikes'+commentid).html(dislajkova + 1);
      }	
      //u div #likeordislike+IDKOMENTARA u show.blade.php ubacujemo novi HTML tj umesto ikona za Like i Dislike sada ce pisati Liked ili Dislkied
      $('#likeordislike'+commentid).html(content);

    });
  });

}); 