<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\DB;

use App\Posts;
use App\User;
use Redirect;

// frontend PostsController
class PostsController extends Controller
{
    // metod vadi 5 najnovijih postova iz 'posts' tabele i salje ih u vju home.blade.php, zovu ga rute '/' i '/home'
    public function index(){
      // izvuci najnovijih 5 postova iz 'posts' tabele kojima je kolona active = 1
      $posts = Posts::where('active', 1)->orderBy('created_at')->paginate(3);  
      //vadimo imena svih autora da bi app.blade.php layout mogao da prikaze padajucu listu sa imenima autora koji su linkovi ka ruti -
      //- author/id koja prikazuje sve tekstove jednog autora
      $authors = User::where('role', '!=', 'subscriber')->select('id', 'name')->get();
      // podesi naslov stranice
      $title = 'Latest Post';
      // ucitaj vju home.blade.php i posalji mu postove i naslov stranice
      return view('home')->withPosts($posts)->withTitle($title)->withAuthors($authors);  
    }
//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
    //metod ucitava admin dashboard tj index.blade.php iz foldera 'myblog\resources\views\admin', poziva ga ruta 'admin/index' koja je u auth middleware-u
    public function indexDashboard(Request $request){
      if($request->user()->can_post()){
        return view('admin/index');  
      }else{
        return redirect('/')->withErrors('You dont have permissions for editing post.');
      }  
    }

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------

    // metod koji izvlaci pojedinacan post po $slugu posta
    public function show($slug){
      // nadji post po slug koloni  
      $post = Posts::where('slug', $slug)->first();
      // ako ga ne nadje ucitaj home view tj idi na rutu '/' sa error porukom
      if(!$post){
        return redirect('/')->withErrors('requested page not found');
      }  
      //vadimo imena svih autora da bi app.blade.php layout mogao da prikaze padajucu listu sa imenima autora koji su linkovi ka ruti -
      //- author/id koja prikazuje sve tekstove jednog autora
      $authors = User::where('role', '!=', 'subscriber')->select('id', 'name')->get();
      // izvuci komentare iz 'comments' tabele dodate izvucenom postu
      $comments = $post->comments;
      // ucitaj vju show.blade.php iz foldera 'myblog\resources\views\posts' i posalji mu $post i $comments varijable
      return view('posts.show')->withPost($post)->withComments($comments)->withAuthors($authors);
    }

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------

    // metod koji vraca vju search.blade.php iz foldera 'myblog\resources\views\posts' u kom je forma za pretragu
    public function getSearch(){
      //vadimo imena svih autora da bi app1.blade.php layout mogao da prikaze padajucu listu sa imenima autora koji su linkovi ka ruti -
      //- author/id koja prikazuje sve tekstove jednog autora
      $authors = User::where('role', '!=', 'subscriber')->get();
      return view('posts/search')->withAuthors($authors);
    }

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
    // ovom metodu ztize AJAX iz search.js iz foldera 'myblog\public\js' kad se u vjuu search.blade.php klikne btn Search ispod forme za pretragu -
    // - ili kada se klikne link za paginaciju koji je jquery izgenerisao
    public function search(Request $request){
      $search = $request['search']; // uzimamo sta je AJAX poslao kao search pojam tj sta je user uneo u formu za pretragu
      // ako je kliknut btn Search onda nece biti $request['skip'] tj nije kliknut link za paginaciju tako da je offset(tj skip) 0
      // - a ako je klinut link za paginaciju stici ce skip pa cemo ga ubaciti u query koji radimo 
      if($request['skip']){ 
        $skip = $request['skip'];
      }else{
        $skip = 0;
      }

      // pravimo query tj JOIN 'posts' i 'users' tabele jer nam treba i ime autora posta, pretrazujemo po title i description kolonama 'posts' -
      // - tabele takodje i kolona active 'posts' tabele mora biti 1 tj post je objavljen, orderujemo po datumu i skipujemo kolio treba, join se -
      // - radi po author_id koloni 'posts' tabele i id koloni 'users' tabele  
      // $posts = DB::table('posts')
      //              ->join('users', 'posts.author_id', '=', 'users.id')
      //              ->select('posts.*', 'users.name')
      //              ->where('active', 1)
      //              ->where('title', 'like', '%'.$search.'%')
      //              //->orWhere('description', 'like', '%'.$search.'%')
      //              ->where('description', 'like', '%'.$search.'%')
      //              ->orderBy('created_at')
      //              ->skip($skip)->take(3)
      //              ->get();  

      $posts = DB::table('posts')
                   ->join('users', 'posts.author_id', '=', 'users.id')
                   ->select('posts.*', 'users.name')
                   ->where('active', 1)
                   //ovo pravi AND(WHERE title posts.like $search AND posts.description LIKE $search)
                   ->where(function($query) use ($search){
                      $query->where('title', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%');
                    })
                   ->orderBy('created_at')
                   ->skip($skip)->take(3)
                   ->get();              
      //$posts = DB::select(" SELECT * FROM posts ");
      // ovde vadimo koliko rezultata ukupno ima koji zadovoljavaju uslov bez joina sa 'users' tabelom i limit offseta da bi znali da li da -
      // - u search.js pravimo paginaciju             
      // $count = Posts::where('title', 'like', '%'.$search.'%')->where('description', 'like', '%'.$search.'%')
      //               ->where('active', 1)
      //               ->orderBy('created_at')
      //               ->count();
      $count = Posts::where('active', 1)
                    ->where(function($query) use ($search){
                      $query->where('title', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%');
                    })
                    ->orderBy('created_at')
                    ->count();   
      // vracamo odgovor search.js - u koji ce dalje raditi sta treba
      return response()->json(['posts' => $posts, 'count' => $count]); 
    }

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------

  //metod koji vadi postove jednog autora i salje ih u home.blade.php da ih prikaze(malo sam izmenio home.blade.php da na vrhu prikaze ime -
  // - autora postova ako ga poziva ovaj metod tj ako postoji varijabla $author)
  public function author($author_id){
    //vadimo imena svih autora da bi app.blade.php layout mogao da prikaze padajucu listu sa imenima autora koji su linkovi ka ruti -
    //- author/id koja prikazuje sve tekstove jednog autora
    $authors = User::where('role', '!=', 'subscriber')->select('id', 'name')->get();
    $author = $author_id;
    //$authorbio = User::where('id', $author_id)->get();
    $posts = Posts::where('author_id', $author_id)->orderBy('created_at')->paginate(3);  
    return view('home')->withPosts($posts)->withAuthor($author)->withAuthors($authors);
  }

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
  
}

