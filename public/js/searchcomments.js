// hendleri za editpostcomments.blade.php iz 'myblog\resources\views\admin\posts'
// varijabla tj array u koju ce uci id -evi komentara koji su nadjeni pretragom da bi ih opet AJAX-om poslali u metod searchDeleteComments() -
// - backend PostsControlelrra da budu obrisani ako kliknemo dugme za brisanje komentara 
var commentsids = []; 

// hendler za klik na search button u formi za pretragu u editpostcomments.blade.php
$('#searchcomments').on('click', function(e){
  e.preventDefault();
  var searchcomments = '';
  // uzmi userov unos u formu za pretragu kometara
  searchcomments = $('#searchcommentsinput').val();
  //alert(searchcomments);
  //saljemo AJAX metodu searchDeleteComments() backend PostsControllera preko rute searchdeletecomments koja je u auth middlewareu,
  // url, postid i token su definisani na dnu editpostcomments.blade.php
  if(searchcomments != ''){
  	if($('.noresult')){ // ako prethodna pretraga nije imala rezultate prvo uklanjamo div .noresult koji je zbog toga prikazan
      	  $('.noresult').remove();	
      	}
  	$.ajax({ 
  	  method: 'POST',
  	  url: url,
  	  data: { search: searchcomments, postid: postid, _token: token }
    })// kad metod searchDeleteComments() backend PostsControllera posalje response
    .done(function(o){
      console.log(o); 
      commentsids = []; 	
      var output = '';
      //u confirm i confirm2 je konfirmaciona poruka koja se izbacuje korisniku kad klikne link za brisanje komentara ili autora komentara
      var confirm = "'Are you sure you want to delete this comment?'";
      var confirm2 = "'Are you sure you want to delete this subscriber and his comments?'";
      if(o['comments'].length == 0){ // ako ne nadje nista u bazi pod unetim pojmom
        output += '<div class="alert alert-danger noresult" role="alert"><h4 class="text-danger text-center">No Results, Try Again...</h4></div>';
        $('.searchform').append(output); 
      }else{ // ako nadje komentare pod unetim pojmom
      	// iteriramo kroz vraceni objekat i prikazujemo body kometnara i na dnu button za brisanje pronadjenih komentara, takodje punimo-
      	// - commentsids array id-evima komentara koji su vraceni da bi ih ako kliknemo btn Delete All obrisao metod searchDeleteComments() backend PostsControllera
        //svaki komentar ima link za brisaje i ako je autor komentara subscriber moze se obrisati i autor
        for(var i = 0; i < o['comments'].length; i++){
          output += '<hr><p>'+o['comments'][i].body+'</p>';
          //link za brisanje komentara ide na deletecomment() metod Auth\PostsController-a
          output += '<a href="/admin/posts/deletecomment/'+o['comments'][i].id+'?_token='+token+'"';
          output += ' onclick="return confirm('+confirm+')"';
          output += ' class="text-danger">';
          output += 'Delete Comment<span class="glyphicon glyphicon-trash"></span></a>';
          //ime i id usera koji je dodao komentar
          output += '<h4><i>User Name:</i>'+o['comments'][i]['author'].name+', <i>User ID:</i> '+o['comments'][i]['author'].id+'</h4>';
          //ako je kreator komentara subscriber(a ne admin ili author)link koji ide na deletecommentautor() metod backend PostsContrrollera-
          //- koja brise autora tog komentara i sve njegove komentare
          if(o['comments'][i]['author'].role == 'subscriber'){
            output += '<a href="/admin/posts/deletecommentauthor/'+o['comments'][i]['author'].id+'?_token='+token+'"';
            output += ' onclick="return confirm('+confirm2+')"';
            output += ' class="text-danger">';
            output += 'Delete Comment Author<span class="glyphicon glyphicon-trash"></span></a>';  
          }
          //punimo array koji ce sluziti da se metodu searchDeleteComments() backend PostsControllera posalju id-evi komentara za brisanje-
          //-(taj metod brise vise komentara odjednom, tj komentare koji su vraceni po nekom pojmu...)
          commentsids.push(o['comments'][i].id);
        }//kad se klikne ovaj btn takodje ide AJAX u metod searchDeleteComments() backend PostsControllera, handler je ispod ovog
        output += "<br><br><a href='#' class='btn btn-danger btn-lg deleteall'><span class='glyphicon glyphicon-trash'></span>Delete All</a>";
        $('.commentslist').html(output);
        //alert(commentsids);
      }
    });
  }else{ // ako user nista nije uneo u polje za pretragu komentara
  	alert("You have to enter something in search input field!");
  }
  
});

//---------------------------------------------------------------------------------------------------------------------------------------

// klik na button za brisanje komentara koji odgovaraju trazenom pojmu(ovaj button generise handler za submit forme za pretragu komentara -
// - editpostcomments.blade.php koji je napisan iznad ovog handlera), saljemo takodje AJAX u metod searchDeleteComments() backend PostsControllera -
// - metod proverava da li u requestu postoji deleteids i ako postoji onda brise komentare iz 'comments' tabele ciji id -evi stignu u commentsids arrayu
$('body').on('click', '.deleteall', function(e){
  e.preventDefault();
  var deleteids = commentsids;//commentsids je array koji je napunjen id-evima komentara koji su stigli kad je pretrazeno po nekom pojmu
  //alert(deleteids);
  $.ajax({ 
  	  method: 'POST',
  	  url: url,
  	  data: { deleteids: deleteids, postid: postid, _token: token }
    })//kad stigne odgovor od kontrolera 
    .done(function(){
      location.reload(); // reloadujemo stranicu
    });
});
