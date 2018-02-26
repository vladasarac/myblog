<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Posts;
use App\User;
use Mail;

class AuthorsController extends Controller
{

//metod se poziva kad se klikne link u allposts.blade.php, salje mu id autora da bi prikazao sve postove samo tog autora 
public function authorPosts(Request $request, $id){
  // izvadi postove po pristiglom id autora 	
  $posts = Posts::where('author_id', $id)->orderBy('created_at')->paginate(3);
  // mora se biti admin ili trenutno ulogovan kao taj autor
  if($request->user()->is_admin() || $request->user()->id == $id){
    // vrati opet vju allposts.blade.php sa pronadjenim postovima, takodje vracamo ovu varijablu $title da bi allposts.blade.php znao da -
    // - prikazuje samo postove jednog autora i onda postoji i forma za dodavanje slike autora i za brisanje autora 
    $title = User::where('id', $id)->first();
    return view('admin/posts/allposts')->withPosts($posts)->withTitle($title);
  }else{
    return redirect('/')->withErrors('You dont have permissions for editing post.');
  }
}

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
   
//metod za upload slike kad se u allposts.blade.php submituje forma za upload slike autora
public function uploadImage(Request $request){
  // ako je user uploadovao sliku	
  if($request->file('images')){
  	// nalazimo usera u 'users' tabeli
    $user = User::find($request['author_id']);
    $fileName = str_random(30).'.jpg'; // napravi ime slike
    // ako je user vec ranije uploadovao sebi sliku tj kolona image nije jednaka 'default.jpg' onda brisemo tu sliku iz foldera 'myblog\public\img\authors'
    if($user->image != 'default.jpg'){
      unlink(public_path('img/authors/'.$user->image));	
    }
    // ubaci sliku u folder 'myblog\public\img\authors'
    $request->file('images')->move("img/authors/", $fileName); // prebaci sliku u folder 'myblog\public\img'
    $user->image = $fileName; // podesavamo image kolonu 'users' tabele
    $user->save(); // cuvamo promenu, pravimo Session::flash sa success porukom i vracamo na allposts.blade.php
    Session::flash('success', 'You have successfully upload user image!');
    return redirect()->back(); //vrati se nazad tj na editpostcomments.blade.php
  }else{ // ako user submituje formu a nije uploadovao fajl tj sliku vrati ga nazad i ubaci error message u Session
    Session::flash('error', 'You have to upload user image!');
    return redirect()->back(); //vrati se nazad tj na allposts.blade.php
  }	
}

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
   
//kad se kline link btn u allposts.blade.php (link je vidljiv samo ako smo usli kroz authorPoasts() metod ovog ontrolera), stize id autora za brisanje
public function deleteAuthor(Request $request, $id){
  //nalazimo u 'users' tabeli usera kog traeba obrisati
  $user = User::find($id);	
  // ako je ulogovan user admin i ako ne pokusava da sam sebe obrise onda je moguce brisanje
  if($request->user()->is_admin() && $user->id != $request->user()->id){
    if($user->image != 'default.jpg'){ // ako user ima dodatu sliku brisemo je iz foldera 'myblog\public\img\authors'
      unlink(public_path('img/authors/'.$user->image));	 	
    }
    $user->delete(); // brisemo usera iz 'users' tabele
    Session::flash('success', 'You have succesfully deleted user!');
    return redirect('/admin/posts/allposts'); //vrati se nazad tj na allposts.blade.php
  }else{ // ako user nije admin ili ako pokusava da sam sebe obrise
    return redirect('/')->withErrors('You dont have permissions for deleting Authors.'); //vrati se na home page u frontendu sa error porukom
  }
}

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad se u allposts.blade.php sabmituje forma za dodavanje autorove biografije, ova forma je vidljiva ako je vju pozvan iz me-
//toda authorPosts() ovog kontrolera tj ako prikazuje postove jednog autora
public function addAuthorBio(Request $request){
  //nalazimo usera u tabeli 'users'
  $user = User::find($request['author_id']);
  //uzimamo unos u formu iz allposts.blade.php
  $user->bio = $request['authorbio'];
  $user->save(); // cuvamo promenu, pravimo Session::flash sa success porukom i vracamo na allposts.blade.php
  Session::flash('success', 'You have successfully add user biography!');
  return redirect()->back(); //vrati se nazad
}

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
      
//kad se u admin dashboardu klikne link All Authors(koji je vidljiv samo adminima) vadi sve autore i admine(ne vadi subscribere)
public function allAuthors(Request $request){
  $authors = User::where('role', '!=', 'subscriber')->orderBy('created_at')->get();
  if($request->user()->is_admin()){//proveri da li je requester admin
    // vrati opet vju allposts.blade.php sa pronadjenim postovima
    return view('admin/allauthors')->withAuthors($authors);
  }else{
    //ako requester nije admin vrati ga na index stranicu sa error porukkom
    return redirect('/')->withErrors('You dont have permissions for editing authors.');
  }
}

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
      
//metod se poziva kad se u admin dashboardu klikne link Users i vraca vju all.users.blade.php iz 'myblog\resources\views\admin', taj link -
//je vidljiv samo adminima
public function allUsers(Request $request){
  if($request->user()->is_admin()){//proveri da li je requester admin
    return view('admin/allusers');
  }else{
    //ako requester nije admin vrati ga na index stranicu sa error porukkom
    return redirect('/')->withErrors('You dont have permissions for editing users.');
  }
}

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
      
//metodu stize AJAX iz searchusers.js kad se unosi nesto u input #searchuser u allusers.blade.php, takodje radi i sa paginacijom kad se klik
//-ne <h4> #moreres koji je vidljiv ako ima vise od 3 rezultata
public function searchUsers(Request $request){
  $search = $request['search']; // pojam koji je user uneo u input #searchuser u allusers.blade.php
  $limit = $request['limit'];//limit(za sada je 3, definisan je u searchusers.js)
  $offset = $request['offset'];//offset je promenljiv
  //vadimo po 3 usera iz 'users' tabele, pretrazujemo name kolonu po unetom tekstu u input #searchuser u allusers.blade.php
  $users = User::where('name', 'like', '%'.$search.'%')->orderBy('created_at', 'desc')->take($limit)->skip($offset)->get();
  //ukupan broj rezultata bez limita i offseta da bi mogli da pravimo paginaciju u searchusers.js
  $userscount = User::where('name', 'like', '%'.$search.'%')->orderBy('created_at', 'desc')->count();
  return response()->json(['users' => $users, 'count' => $userscount]);//vracamo nadjeno u searchusers.js na dalju obradu...
}

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
  
//metod koriste btn-i koje generise searchusers.js kad admin pretrazuje usere u allusers.blade.php pored svakog usera postoje btn-i Admin, Author i
//Delete(ako je subscriber, a ako je admin onda Author i Delete ili ako je Author onda Admin i Delete), kad se klikne btn Admin ili Author salje
//se AJAX preko rute 'makeadminorauthor' i u njemu id usera i varijabla adminorauthor (koja moze biti admin(onda menjamo role kolonu users 
//tabeleu admin) ili author(onda menjamo role kolonu users tabele u author))
public function makeadminorauthor(Request $request){
  //ako je requester admin
  if($request->user()->is_admin()){
    $id = $request['userid'];//uzimamo id usera koji je stigao AJAX-om 
    $adminorauthor = $request['adminorauthor'];//proveravamo da li usera proglasavamo za admina ili authora
    $user = User::find($id);//nalazimo usera u 'users' tabeli po id
    if($adminorauthor == 'admin'){//ako ga proglasavamo za admina
      $user->role = 'admin';//nova vrednost role kolone
      $user->save();//cuvamo
      //Saljemo mail na adresu novog admina koristimo Mail facade, sadrzaj maila je u newadmin.blade.php iz 'myblog\resources\views\email'
      Mail::send('email.newadmin', ['user' => $user], function($m) use ($user){
        //sa ove adrese saljemo mail
        $m->from('kantarion35@gmail.com');
        //na ovu adresu saljemo mail(email kolona users tabele)
        $m->to($user->email, $user->name);
        //naslov maila
        $m->subject('Congratulation, you are MyBlog Admin!');
      });
      return response()->json(['role' => $adminorauthor]);//vracamo poruku u searchusers.js na dalju obradu...
    }else{// ako ga proglasavamo za authora
      $user->role = 'author';//nova vrednost role kolone
      $user->save();//cuvamo promenu
      //Saljemo mail na adresu novog auhtora koristimo Mail facade, sadrzaj maila je u newauthor.blade.php iz 'myblog\resources\views\email'
      Mail::send('email.newauthor', ['user' => $user], function($m) use ($user){
        //sa ove adrese saljemo mail
        $m->from('kantarion35@gmail.com');
        //na ovu adresu saljemo mail(email kolona users tabele)
        $m->to($user->email, $user->name);
        //naslov maila
        $m->subject('Congratulation, you are MyBlog Author!');
      });
      return response()->json(['role' => $adminorauthor]);//vracamo poruku u searchusers.js na dalju obradu...
    }
  }else{
    //ako requester nije admin vrati ga na index stranicu sa error porukkom
    return redirect('/')->withErrors('You dont have permissions for editing users.');
  }
}  

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
  
//metod za brisanje usera kad se u allusers.blade.php klikne link Delete koji pravi hendler za pretragu, stize AJAX preko rute 'deleteuser'
// i u njemu id usera koji se brise, AJAX salje hendler za klik na btn Delete tj .deleteuser takodje iz searchusers.js
public function deleteuser(Request $request){
  //nalazimo usera kog treba obrisati u 'users' tabeli pomocu id-a koji je stigo AJAX-om
  $user = User::find($request['userid']);  
  // ako je ulogovan user admin i ako ne pokusava da sam sebe obrise onda je moguce brisanje
  if($request->user()->is_admin() && $user->id != $request->user()->id){
    if($user->image != 'default.jpg'){ // ako user ima dodatu sliku brisemo je iz foldera 'myblog\public\img\authors'
      unlink(public_path('img/authors/'.$user->image));   
    }
    $user->delete(); // brisemo usera iz 'users' tabele
    return response()->json(['delete' => 1]);//vracamo poruku u searchusers.js na dalju obradu...
  }else{
    //ako requester nije admin vrati ga na index stranicu sa error porukkom
    return redirect('/')->withErrors('You dont have permissions for deleting this user.');
  }
}

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------



}
