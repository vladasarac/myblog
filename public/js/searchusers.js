//ovde su hendleri za unos u text input u allusers.blade.php za pretragu 'users' tabele,fajl je okacen u dashboard.blade.php layoutu
$(document).ready(function(){	
  //varijabla u koju ubacujemo userov unos u input #searchuser u allusers.blade.php
  var searchinput = '';
  //ovo je za paginaciju koju radi metod searchUsers() AuthorsControllera
  var limit = 3;
  //offset se povecava za 3 u hendleru za klik na <h4> #moreres koji je vidljiv ako ima jos rezultata
  var offset = 0;
  //varijablu koristi hendler za klik na <h4> #moreres da zna koliko je redova vraceno do sada iz kontrolera
  var loaded = limit; 
  
  //hendler za unos necega(keyup) u text input #searchuser u allusers.blade.php, posle svakog unetog karaktera ide novi AJAX
  $('#searchuser').on('keyup', function(e){
  	searchinput = $(this).val();//uzimamo sta je trenutno u inputu
    offset = 0; // offset je 0 posto je ovo prvi deo rezultata bez offseta(prva 3 koje nadje, u hendleru za klik na More Res se povecava)
    //ovde opet resetujemo ovu vraijablu posto je mozda kliknuto na More Res.. pa je promenila vrednost tako da ako se unese novi pojam 
    //moramo je resetovati da bi radilo kako treba
    loaded = limit;
  	//alert(searchinput);
    //saljemo AJAX u searchusers() metod AuthorsControllera preko rute /searchusers 
  	$.ajax({ 
  	  method: 'POST',
  	  url: url, //varijable url i _token su definisane na dnu vjua allusers.blade.php,pored njih saljemo searchinput i limit(3) i offset(0)
  	  data: { search: searchinput, limit: limit, offset: offset, _token: token }
    })
    //kad kontroller vrati nesto(vraca usere koje nadje(prva 3) i ukupan broj(count) bez limita i offseta)
    .done(function(o){
      console.log(o);

      var content = ''; //varijabla u koju cemo ubaciti HTML koji cemo izgenerisati koristeci podatke koje je vratio kontroler
      if(o['users'].length == 0){ // ako ne nadje nista u bazi pod unetim pojmom ispisujemo da nije nista nasao 
        content += '<h3 class="text-danger text-center">No Results, Try Again...</h3>'; 
      }else{ // ako nadje neke usere, na vrhu ispisujemo sta je uneto u input i koliko je nadjeno usera(count bez limita i offseta)
        content += '<h3 class="text-success text-center">Search results for: <span class="text-info">'+searchinput+'<span>';
        content += '<span class="text-info"> ('+o['count']+' users found)<span></h3>';	
        content += "<div id='reslist'>";//u ovaj div cemo ubacivati usere
        for(var i = 0; i < o['users'].length; i++){ //iteriramo kroz usere koje nasao kontroler i za svakog pisemo ime i odgovarajuce btn-e     
          content += "<div class='user' id='user"+o['users'][i]['id']+"'>";
          content += "<h3 class='text-primary' style='display: inline;'>"+parseInt(i + 1)+'. '; //ovo je redni broj(nije id)
          content += o['users'][i]['name']+"</h3> ";//ime usera(name kolona 'users' tabele)
          content += "<small id='role"+o['users'][i]['id']+"'>("+o['users'][i]['role']+")</small>"; // rola usera(role kolona 'users' tabele) i zatim btn za Delete usera     
          content += " <button type='submit' deletename='"+o['users'][i]['name']+"' deleteid='"+o['users'][i]['id']+"' class='deleteuser btn btn-danger pull-right'>Delete</button>";
          if(o['users'][i]['role'] == 'admin'){ //ako je user admin pravimo btn kojim se proglasava za authora
            content += "<button type='submit' subscribername='"+o['users'][i]['name']+"' subscriberid='"+o['users'][i]['id']+"' class='adminbtn makeauthor btn btn-info pull-right'>Author</button>";
          }
          if(o['users'][i]['role'] == 'author'){ //ako je user autor pravimo btn kojim se proglasava za admina
            content += "<button type='submit' subscribername='"+o['users'][i]['name']+"' subscriberid='"+o['users'][i]['id']+"' class='adminbtn makeadmin btn btn-success pull-right'>Admin</button>";
          }//ako je subscriber pravimo btn-e kojim se moze proglasiti za authora ili za admina
          if(o['users'][i]['role'] == 'subscriber'){ 
            content += "<button type='submit' subscribername='"+o['users'][i]['name']+"' subscriberid='"+o['users'][i]['id']+"' class='adminbtn makeadmin btn btn-success pull-right'>Admin</button>";
            content += "<button type='submit' subscribername='"+o['users'][i]['name']+"' subscriberid='"+o['users'][i]['id']+"' class='adminbtn makeauthor btn btn-info pull-right'>Author</button>";
          }
          content += '</div>';
          //content += "<hr>";
        }
        content += "</div>";//kraj diva #reslist  
        if(o['count'] > limit){ 
        // ako ima vise od 3 rezultata dodajemo <h4> #moreres koji kad se klikne poziva novi hendler koji salje AJAX na istu rutu samo sada sa
        //novim offsetom(ovo koristimo umesto klasicne paginacije, hendler za klik na ovaj <h4> je ispod ovog hendlera)
          content += "<h4 id='moreres' class='text-center text-info'>More Results <span class='glyphicon glyphicon-triangle-bottom'></sapn></h4>";
        }
      }
      // izgenerisani HTML ubacujemo u div .searchresults koji je za sada prazan u allusers.blade.php
      $('.searchresults').html(content);
    });
  });

  //----------------------------------------------------------------------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------------------------------------------------------------------

  //hendler za klik na <h4> #moreres(njega je izgenerisao prethodni hendler ako je nadjeno vise od 3 usera tako da je ovo kao paginacija)
  $('body').on('click', '#moreres', function(e){ 
    offset = offset + limit;//povecavamo offset za 3
    $('#moreres').remove();//uklanjamo <h4> #moreres posto cemo na dnu napraviti novi ako treba tj ako ima jos rezultata koji nisu prikazani
    //saljemo AJAX u searchusers() metod AuthorsControllera preko rute /searchusers sve je isto samo je offset povecan za 3
    $.ajax({ 
      method: 'POST',
      url: url,
      data: { search: searchinput, limit: limit, offset: offset, _token: token }
    })
    //kad kontroller vrati nesto(vraca usere koje nadje(saada ih offsetuje) i ukupan broj(count) bez limita i offseta)
    .done(function(o){
      console.log(o);
      //ovde vodimo evidenciju koliko je usera do sada prikazano(na vrhu je ovoj varijabli data vrednost 3 tj jeednaka je linitu i sada je opet
      //povecavamo za toliko) ona nam treba da bi dole znali da li smo prikazali usera koliko ih ima u count i ako jesmo onda vise ne prikazujemo
      // <h4> #moreres
      loaded = loaded + limit;
      //u ovu varijablu cemo generisati HTML sa podatcima koje vraca kontroler i to cemo append-ovati na div #reslist koji smo izgenerisali u 
      //prethodnom hendleru u koji su ubaceni i prethodni rezultati
      var content = '';
      if(o['users'].length == 0){  // ako ne nadje nista u bazi pod unetim pojmom ispisujemo da nije nista nasao
        content += '<h3 class="text-danger text-center">No Results, Try Again...</h3>'; 
      }else{ // ako nadje nesto iteriramo kroz usere koji su vraceni iz kontrolera i za svakog pisemo ime i odgovarajuce btn-e  
        for(var i = 0; i < o['users'].length; i++){
          content += "<div class='user' id='user"+o['users'][i]['id']+"'>";
          content += "<h3 class='text-primary' style='display: inline;'>"+parseInt(i + offset  + 1)+'. '; //ovo je redni broj(nije id)
          content += o['users'][i]['name']+"</h3> ";//ime usera(name kolona 'users' tabele)
          content += "<small  id='role"+o['users'][i]['id']+"'>("+o['users'][i]['role']+")</small>";// rola usera(role kolona 'users' tabele) i zatim btn za Delete usera       
          content += " <button type='submit' deletename='"+o['users'][i]['name']+"' deleteid='"+o['users'][i]['id']+"' class='deleteuser btn btn-danger pull-right'>Delete</button>";
          if(o['users'][i]['role'] == 'admin'){ //ako je user admin pravimo btn kojim se proglasava za authora
            content += "<button type='submit' subscribername='"+o['users'][i]['name']+"' subscriberid='"+o['users'][i]['id']+"' class='adminbtn makeauthor btn btn-info pull-right'>Author</button>";
          }
          if(o['users'][i]['role'] == 'author'){ //ako je user autor pravimo btn kojim se proglasava za admina
            content += "<button type='submit' subscribername='"+o['users'][i]['name']+"' subscriberid='"+o['users'][i]['id']+"' class='adminbtn makeadmin btn btn-success pull-right'>Admin</button>";
          }//ako je subscriber pravimo btn-e kojim se moze proglasiti za authora ili za admina
          if(o['users'][i]['role'] == 'subscriber'){ 
            content += "<button type='submit' subscribername='"+o['users'][i]['name']+"' subscriberid='"+o['users'][i]['id']+"' class='adminbtn makeadmin btn btn-success pull-right'>Admin</button>";
            content += "<button type='submit' subscribername='"+o['users'][i]['name']+"' subscriberid='"+o['users'][i]['id']+"' class='adminbtn makeauthor btn btn-info pull-right'>Author</button>";
          }
          content += "</div>";
          //content += "<hr>";  
        }//ako je count veci od broja do sada prikazanih usera(loaded varijabla) opet izbacujemo <h4> #moreres
        if(o['count'] > loaded){
          content += "<h4 id='moreres' class='text-center text-info'>More Results <span class='glyphicon glyphicon-triangle-bottom'></sapn></h4>";
        }
      }
      //izgenerisani HTML appendujemo na dno div-a #reslist koji smo izgenerisali u prethodnom hendleru
      $(content).appendTo('#reslist');
    });
  });

  //----------------------------------------------------------------------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------------------------------------------------------------------
  
  //kad se klikne btn Admin(.makeadmin) pored nekog usera kog je prikazao hendler za pretragu(ili paginaciju) tj proglasavanje usera za Admina
  //salje se AJAX u AuthorsController metodu makeadminorauthor() preko rute makeadminorauthor
  $('body').on('click', '.makeadmin', function(e){ 
    var id = $(this).attr('subscriberid'); //uzimamo id usera iz atributa subscriberid btn-a Admin
    var name = $(this).attr('subscribername');//uzimamo ime usera iz atributa subscribername btn-a Admin
    var url = makeadminorauthorurl;//url je definisan na dnu vjua allusers.blade.php(to je ruta preko koje saljemo AJAX u AuthorsController metodu makeadminorauthor())
    var adminorauthor = 'admin';//posto taj metod moze da proglasi usera i za admina i za authora ovde m saljemo admin da bi ga proglasio za admina
    //alert(name);
    //izbacujemo confirm i ako korisnik klikne OK onda se salje AJAX
    if(confirm("Are you sure you want to give admin credentials to "+name+"?")){
      //ovde dodajemo iza <small> koji u zagradi prikazuje rolu usera loading gif
      var content = "<img class='gif' id='gif"+id+"' width='25' height='25' src='http://myblog.dev/img/loaderadmin.gif'>";
      $(content).insertAfter('#role'+id);
      $(this).remove();
      $('#user'+id).css({"background-color": "#5cb85c"});//menjamo pozadinu diva koji prikazuje usera u zeleno
      $.ajax({ 
        method: 'POST',
        url: url,
        data: { userid: id, adminorauthor: adminorauthor, _token: token }
      })
      //kad se iz kontrolera vrati odgovor
      .done(function(o){
        console.log(o);
        $('#gif'+id).remove();//uklanjamo loading gif
        $('#user'+id).addClass('newcolor');//ova klasa je definisana u dashboard.blade.php(ima tranziciju pri promeni boje)
        $('#role'+id).text('(admin)');//menjamo text u <small> .role_idisera koji prikazuje trenutnu rolu usera 
      });
    }else{ // ako user klikne 'No' u confirmu
      return false;
    } 
  });

  //----------------------------------------------------------------------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------------------------------------------------------------------
  
  //kad se klikne btn Author(.makeauthor) pored nekog usera kog je prikazao hendler za pretragu(ili paginaciju) tj proglasavanje usera za Authora
  //salje se AJAX u AuthorsController metodu makeadminorauthor() preko rute makeadminorauthor
  $('body').on('click', '.makeauthor', function(e){ 
    var id = $(this).attr('subscriberid'); //uzimamo id usera iz atributa subscriberid btn-a Author
    var name = $(this).attr('subscribername');//uzimamo ime usera iz atributa subscribername btn-a Author
    var url = makeadminorauthorurl;//url je definisan na dnu vjua allusers.blade.php(to je ruta preko koje saljemo AJAX u AuthorsController metodu makeadminorauthor())
    var adminorauthor = 'author';//posto taj metod moze da proglasi usera i za admina i za authora ovde m saljemo author da bi ga proglasio za authora
    //alert(name);
    //izbacujemo confirm i ako korisnik klikne OK onda se salje AJAX
    if(confirm("Are you sure you want to give author credentials to "+name+"?")){
      //ovde dodajemo iza <small> koji u zagradi prikazuje rolu usera loading gif
      var content = "<img class='gif' id='gif"+id+"' width='25' height='25' src='http://myblog.dev/img/loader.gif'>";
      $(content).insertAfter('#role'+id);
      $(this).remove();
      $('#user'+id).css({"background-color": "#5bc0de"});//menjamo pozadinu diva koji prikazuje usera u plavo
      $.ajax({ 
        method: 'POST',
        url: url,
        data: { userid: id, adminorauthor: adminorauthor, _token: token }
      })
      //kad se iz kontrolera vrati odgovor
      .done(function(o){
        console.log(o);
        $('#gif'+id).remove();//uklanjamo loading gif
        $('#user'+id).addClass('newcolor');//ova klasa je definisana u dashboard.blade.php(ima tranziciju pri promeni boje)
        $('#role'+id).text('(author)');//menjamo text u <small> .role_idisera koji prikazuje trenutnu rolu usera 
      });
    }else{ // ako user klikne 'No' u confirmu
      return false;
    } 
  });

  //----------------------------------------------------------------------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------------------------------------------------------------------
  
  //hendler za klik na btn Delete tj .deleteuser koji je izgenerisao hendler za pretragu usera, salje AJAX metodu deleteuser() AuthorsControllera
  //preko rute 'deleteuser'
  $('body').on('click', '.deleteuser', function(e){ 
    var id = $(this).attr('deleteid'); //uzimamo id usera iz atributa deleteid btn-a Delete
    var name = $(this).attr('deletename');//uzimamo ime usera iz atributa deletename btn-a Delete
    var url = deleteuserurl;//url je definisan na dnu vjua allusers.blade.php(to je ruta preko koje saljemo AJAX u AuthorsController metodu deleteuser())
    if(confirm("Are you sure you want to delete "+name+"?")){
      $('#user'+id).css({"background-color": "#d9534f"});//menjamo pozadinu diva koji prikazuje usera u crveno
      $.ajax({ 
        method: 'POST',
        url: url,
        data: { userid: id, _token: token }
      })
      //kad se iz kontrolera vrati odgovor
      .done(function(o){
        console.log(o);
        $('#user'+id).addClass('newcolor');//ova klasa je definisana u dashboard.blade.php(ima tranziciju pri promeni boje)
        $('#user'+id).slideUp(function(){//slideUp-ujemo div koji je prikazivao obrisanog usera i kad se to zavrsi uklanjamo ga
          $('#user'+id).remove();
        });
      });
    }else{ // ako user klikne 'No' u confirmu
      return false;
    } 
  });


});