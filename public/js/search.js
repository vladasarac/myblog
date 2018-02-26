
// ovde su hendleri na klik na btn Search u search.blade.php iz foldera 'myblog\resources\views\posts' i na linkove za paginaciju -
// - ako su izgenerisani, hendleri salju AJAX search() metodu frontend PostsControllera koji vraca postove i radi JOIN sa 'users' tabelom -
// - i vraca imena autora postova i vraca broj postova ukupno bez paginacije da bi znali da generisemo linkove za paginaciju 

var globalsearch = ''; // varijabla u kojoj se cuva userov unos u search polje u search.blade.php da bi paginacija kad salje ajax imala pojam za pretragu
var brpostr = 3; // br postova postranici

// hendler na link btn search u search.blade.php
$('#search').on('click', function(e){
  e.preventDefault();
  // praznimo div .paginacija ako je prethodni search imao paginaciju da ne bi ostali linkovi od njega
  $('.paginacija').html('');
  //alert(token);
  globalsearch = $('#searchinput').val() // podesi var globalsearch userovim unosom u polje za pretragu u search.blade.php
  // salji ajax search() metodu frontend PostsControllera (url i token su definisani na dnu vjua search.blade.php)
  $.ajax({ 
  	method: 'POST',
  	url: url,
  	data: { search: $('#searchinput').val(), _token: token }
  })
  // kad stigne odgovor od search() metoda frontend PostsControllera (u o['posts'] su postovi a u o['count'] ukupan broj pronadjenih postova -
  // - bez paginacije, da bi mogli da izracunamo koliko nam linkova za paginaciju treba)
  .done(function(o){
  	console.log(o);  
    var count = parseInt(o['count']); // koliko ukupno ima postova koji zadovoljavaju pretragu bez paginacije
    //alert(count);
    var output = ''; // pravimo output koji cemo ubaciti u div .body u search.blade.php
    if(o['posts'].length == 0){ // ako ne nadje nista u bazi pod unetim pojmom
      output += '<h2 class="text-danger">No Results, Try Again...</h2>'; 
    }
    // iteririamo kroz postove koje je vratio kontroler i prikazujemo ih(od naslova pravimo link ka show() metodu frontend PostsControlera -
    // - kom saljemo slug posta kao argument posto po slugu nalazi post za prikazivanje u bazi)
    for(var i = 0; i < o['posts'].length; i++){
      output += "<a href='"+homeurl+"/"+o['posts'][i]['slug']+"'>";
      output += "<h2 class='text-primary'>"+o['posts'][i]['title']+"</h2>";
      output += "</a>";
      output += "<p>"+o['posts'][i]['description'].substring(0,150)+"</p>"
      var date = dateformating(o['posts'][i]['created_at']); // pozovi funkciju koja formatira datum da od stringa napravi citljiv datum
      output += "<p><i>Created at:</i> <strong>"+date+"</strong> ";
      //napravi link ka metodu athor() frontend PostsControllera koji ce izvuci sve postove ovog autora po author_id koloni 'posts' tabele -
      // - i poslace ih an prikazivanje u home.blade.php
      output += "<a href='author/"+o['posts'][i]['author_id']+"'>";
      output += "<i>By </i><strong>"+o['posts'][i]['name']+"</strong></p><hr>";
      output += "</a>";
      //output += '<p>'+count+'</p>';
      
    }
    $('.body').html(output); // ubacujemo izgenerisani output u div .body
    // ako je nadjeno vise postova nego sto je dozvoljeno po stranici pravimo paginaciju
    if(count > brpostr){
      brlinkova = Math.ceil(count / brpostr); // odredjujemo br linkova za paginaciju
      // pravimo html tj linkove za paginaciju (hendler za njih je ispod)
      var paginacija = "<div class='row'><ul class='pagination'>";
      for(var p = 1; p <= brlinkova; p++){
        paginacija += "<li name='"+p+"' id='link_"+p+"' class='paginacijalink ";
        if(p == 1){
          paginacija += " active"; //dodaj prvom linku klasu active i u isto vreme ga disableuj
        }
        paginacija += "'><a class='linkzapaginaciju' href='#'>"+p+"</a></li>";
        
      }
      paginacija += "</ul></div>";
      // ubacujemo paginaciju u div .paginacija u search.blade.php
      $('.paginacija').html(paginacija);

    }

  });

});



// hendler za klik na link za paginaciju (ako je ima uopste...)
$('body').on('click', '.paginacijalink', function(e){

  e.preventDefault();
  // ako link paginacije koji je kliknut nema klasu active tj ako nismo kliknuli link stranice na kojoj smo
  if(!$(this).hasClass('active')){

    $('.active').removeClass('active');// linku koji je do sada imao klasu active i bio disableovan skini tu klasu
    $(this).addClass('active'); // linku koji je sada kliknut dodaj klasu active i disableuj ga
    var linkbr = $(this).attr('name'); // uzmi broj koji je link imao da bi izracunali skip tj offset pri novom queryu u search() metodu u kontroleru
    var skip = ((linkbr - 1) * brpostr); // izracunavamo koliko se postova preskace u novom queryu
    // saljemo novi AJAX u search() metod PostsControllera samo mu sada saljemo i skip(u searc() je definisano da ako mu ne stigne skip -
    // - skip bude 0 posto onda znaci da je kliknut btn search a ne link za paginaciju)(url i token su definisani na dnu vjua search.blade.php)
    $.ajax({ 
      method: 'POST',
      url: url,
      data: { search: globalsearch, skip: skip, _token: token }
    })
    //kad search() metod posalje response
    .done(function(o){
      console.log(o);  
      var output = ''; // pravimo novi output koji cemo ubaciti u div .body umesto sadasnjeg
      for(var i = 0; i < o['posts'].length; i++){
        output += "<a href='"+homeurl+"/"+o['posts'][i]['slug']+"'>";
        output += "<h2 class='text-primary'>"+o['posts'][i]['title']+"</h2>";
        output += "</a>";
        output += "<p>"+o['posts'][i]['description'].substring(0,150)+"</p><hr>"
        var date = dateformating(o['posts'][i]['created_at']); // pozovi funkciju koja formatira datum da od stringa napravi citljiv datum
        output += "<p><i>Created at:</i> <strong>"+date+"</strong> ";
        //napravi link ka metodu athor() frontend PostsControllera koji ce izvuci sve postove ovog autora po author_id koloni 'posts' tabele -
        // - i poslace ih an prikazivanje u home.blade.php
        output += "<a href='author/"+o['posts'][i]['author_id']+"'>";
        output += "<i>By </i><strong>"+o['posts'][i]['name']+"</strong></p><hr>";
        output += "</a>";
        //alert(date);
      }

      $('.body').html(output); // ubacujemo sve u div .body
      $("html, body").animate({ scrollTop: 0 }, "fast"); // skrolujemo ekran do vrha
    });

  }

});

// metod koji formatira datum koji je od kontrolera stigao u formi stringa u Date i onda ga formatira da lepse izgleda
dateformating = function(datestring){
  var newdate = new Date(datestring); // napravi od stringa date format
  newdate = newdate.toString();//prebaci ga opet u string
  newdate = newdate.substring(4, 15);//odseci prva 4 karaktera(to je dan u nedelji) do 15(tu se zavrsava godina)
  return newdate;
  // var day = newdate.getDate(); // izvuci dan
  // if(day < 10){ // ako je dan ispod 10-og dodaj 0 ispred
  //   day = "0" + day;
  // }
  // var month = newdate.getMonth(); // izvuci mesec
  // if(month < 10){ // ako je mesec ispod 10-og dodaj 0 ispred
  //  //month = "0" + month;
  //  month = month;
  // }
  // var year = newdate.getFullYear(); // izvuci godinu
  // newdate = day+'. '+month+'. '+year+'.'; // napravi datum koji funkcija vraca
  //return newdate;
};



