<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Posts;
use App\User;
use App\Comments;
use Redirect;

use Yajra\Datatables\Datatables;

// backend PostsController
class PostsController extends Controller
{

  //
  public function datatables(){
    return view('admin/posts/datatables');
  }

  //
  public function postsdata(){
    return Datatables::of(\App\Posts::all())->make(true);
  }


    // metod ako nije u allposts.blade.php uneto nesto u pretragu vraca sve odobrene postove a ako je uneto nesto nesto 
    // - radi query po naslovu i koloni active koja mora biti 1 tj odobren i radi paginaciju na 3 posta
    public function index(Request $request){
      $search = $request->get('search');
      $posts = Posts::where('title', 'like', '%'.$search.'%')->where('active', 1)->orderBy('created_at')->paginate(5);   
      // da bi samo admin ili author mogli na ovu stranicu(can_post() je metod User.php modela koji proverava da li je ulogovani user author ili admin)
      if($request->user()->can_post()){
        // vrati opet vju allposts.blade.php sa pronadjenim postovima
        return view('admin/posts/allposts')->withPosts($posts);
      }else{
        return redirect('/')->withErrors('You dont have permissions for editing post.');
      }
    }

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
    
    // metod koji vraca vju create.blade.php iz foldera 'myblog\resources\views\admin\posts' ako je user admin ili author tj to mu pise u role koloni 'users' tabele 
    public function create(Request $request){
      // proveri da li je user admin ili author i ako jeste vrati vju create.blade.php iz foldera 'myblog\resources\views\admin\posts'
      // metod can_post je u User.php modelu i on proverava da li je kolona role usera koji poziva ovaj metod author ili admin(oni mogu da pisu postove) ili subscriber(oni ne mogu da kreiraju postove) 
      if($request->user()->can_post()){
        return view('admin/posts/create');
      }else{ // ako nije salji ga na home page sa error porukom 
        return redirect('/')->withErrors('You dont have permissions for writing post.');
      }
    }

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
    // metod se poziva preko rute 'admin/posts/createpost' kad se submituje forma za kreiranje novog posta u create.blade.php iz foldera 'myblog\resources\views\admin\posts'
    public function store(Request $request){
      $post = new Posts(); // instanciraj Posts model tj. klasu
      $post->title = $request->get('title'); // podesi title kolonu 'posts' tabele onim sto je user uneo u title polje u formi 
      $post->description = $request->get('description'); // podesi description kolonu 'posts' tabele onim sto je user uneo u description polje u formi
      $post->body = $request->get('body');  // podesi body kolonu 'posts' tabele onim sto je user uneo u body textarea u formi
      $post->slug = str_slug($post->title); // napravi slug za slug kolonu 'posts' tavele
      $post->author_id = $request->user()->id; // uzmi userov id za kolonu author_id 'posts' tabele
      // ako je user uneo sliku u polje u formi (name="images")
      if($request->file('images')){
        $fileName = str_random(30); // napravi ime slike
        $request->file('images')->move("img/", $fileName); // prebaci sliku u folder 'myblog\public\img'
      }else{
        $fileName = 'nema slike'; // podesi images kolonu 'posts' tabele
      }
      $post->images = $fileName;
      // ako je kliknut btn "Save Draft" tj ne objavljujemo post nego cemo samo sacuvati uneto podesavamo active kolonu 'posts' tabele na 0
      if($request->has('save')){
        $post->active = 0;
        $message = 'Post is saved successfully!';
      }else{ // ako je kliknut btn "Publish" ispod forme onda podesavamo kolonu active na 1 i post ce biti objavljen tj vidljiv korisnicima
        $post->active = 1;
        $message = 'Post is published successfully!';
      }
      $post->save(); // upisujemo post u 'posts' tabelu
      // redirectujemo na edit.blade.php
      return redirect('admin/posts/editpost/'.$post->slug)->withMessage($message);
    }

//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
   // ovaj metod poziva metod store() kad upise post i moze se pozvati iz admin dashboarda tj allposts.blade.php kad se klikne link za editovanje postta
   // poziva ga ruta 'admin/posts/editpost/{slug}'
    public function edit(Request $request ,$slug){
      // nadji post po slug koloni 'posts' tabele
      $post = Posts::where('slug', $slug)->first();
      // ako je post nadjen i user je kreator posta ili je User.php model vratio true u funkciji is_admin tj user je admin (is_admin() je metod User.php modela koji proverava da li je user admin i vraca true ili false)
      if($post && ($request->user()->id == $post->author_id || $request->user()->is_admin()))
        // pozovi vju edit.blade.php i posalji mu post da njime popuni formu za editovanje 
        return view('admin/posts/edit')->with('post', $post);
      //ako user nije kreator posta ili admin vrati na home stranicu sa error porukom 
      return redirect('/')->withErrors('You dont have permissions to update this post!');
    }


//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
   // metod se poziva kad se submituje forma za update posta u edit.blade.php preko rute 'admin/posts/updatepost'
    public function update(Request $request){
      // uzmi id posta koji je stigao u requestu
      $post_id = $request->input('post_id'); 
      $post = Posts::find($post_id);// i nadji taj post u 'posts' tabeli
      // ako nadje post i ako je autor posta onaj koji je trenutno ulogovan ili ako je admin (is_admin() je metod User.php modela koji proverava da li je user admin i vraca true ili false)
      if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin())){
        $title = $request->input('title'); // uzmi sta je uneto u formi u edit.blade.php u title polje u formi
        $slug = str_slug($title); // napravi slug
        // pretrazi posts tabelu po slug koloni da bi videli da li je taj slug vec negde koriscen
        $duplicate = Posts::where('slug', $slug)->first();
        // ako vec postoji post sa istim slugom u 'posts' tabeli
        if($duplicate){
          // ako to nije ovaj post koji trenutno updateujemo
          if($duplicate->id != $post_id){
            // vrati na edit.blade.php sa porukom da je title vec zauzet
            return redirect('admin/posts/editpost/'.$post->slug)->withErrors('Title already exists!')->withInput();
          }else{ // ako je to ovaj post podesi mu slug kolonu
            $post->slug = $slug;
          }
        }
        $post->title = $title; //podesi title kolonu
        // ako je user uneo sliku u polje u formi (name="images")
        if($request->file('images')){
          $fileName = str_random(30); // napravi ime slike
          $request->file('images')->move("img/", $fileName); // prebaci sliku u folder 'myblog\public\img'
        }else{
          $fileName = $post->images; // podesi images kolonu 'posts' tabele
        }
        $post->images = $fileName;
        // podesi descriptioon i body kolone 'posts' tabele onim sto je uneto u formu za update
        $post->description = $request->input('description');
        $post->body = $request->input('body');
        // ako je kliknuto dugme save a ne publish (znaci da user jos nece da objavi post nego samo da ga sacuva)
        if($request->has('save')){
          $post->active = 0; // active kolona ce biti 0 i redirectovacemo na edit.blade.php
          $message = 'Post is saved successfully';
          $goto = 'admin/posts/editpost/'.$post->slug;
        }else{
          // ako je post vec objavljen ili nije bio ali je user kliknuo btn Publish na dnu forme 
          // active kolona cve biti 1 i redirectovacemo na allposts.blade.php
          $post->active = 1;
          $message = 'Post is updated successfully';
          $goto = 'admin/posts/allposts/';
        }
        $post->save(); // updateuj red u 'posts' tabeli
        // redirectuj gde treba (zavisno od $goto varijable na edit.blade.php ili allposts.blade.php) sa porukom
        return redirect($goto)->withMessage($message);
      }else{ // ako user nije admin ili kreator posta vrati ga na home stranicu sa ovom porukom
        return redirect('/')->withErrors('You dont have permissions to update this post!');
      }
    }

    
//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
    // kad se u allposts.blade.php klikne link delete ili u edit.blade.php klikne btn link Delete, request stize preko rute 'admin/posts/deletepost/{id}'
    public function destroy(Request $request, $id){
      //nadji post po id koji je stigao
      $post = Posts::find($id);
      // ako nadje post i ako je autor posta onaj koji je trenutno ulogovan ili ako je admin (is_admin() je metod User.php modela koji proverava da li je user admin i vraca true ili false)
      if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin())){
        if($post->images != 'nema slike'){ // brise sliku koja je dodata postu tj ako kolona images != 'nema slika' znaci da post ima dodatu sliku i brisemo je
          unlink(public_path('img/'.$post->images));  
        }
        $post->delete(); // obris post
        $data['message'] = 'Post deleted successfully!';
      }else{ // ako nema posta ili user nije kreator posta ili admin, podesi message
        return redirect('/')->withErrors('You dont have permissions to delete this post!');
      }
      // redirectuj na allposts.blade.php sa porukom 
      return redirect('admin/posts/allposts')->with($data);
    }

  //-------------------------------------------------------------------------------------------------------------------------------------------
  //-------------------------------------------------------------------------------------------------------------------------------------------
  // metod izvlaci post i komentare posta kad se u allposts.blade.php klikne ikona za postove u actions koloni tabele koja prikazuje postove 
  // - i salje ih u vju editpostcomments.blade.php iz 'myblog\resources\views\admin\posts' gde se mogu obrisati ako treba 
  public function editpostcomments(Request $request, $id){
    $post = Posts::find($id); //nadji post cije komentare hocemo da editujemo tj brisemo
    $comments = $post->comments; // uzmi komentare na post iz 'comments' tabele
    //dd($comments);
    // ako postoji post i ako je user koji je ulogovan autor ili admin tj nije obican subscriber koji samo moze da komentarise
    if($post && $request->user()->can_post()){
        return view('admin/posts/editpostcomments')->withPost($post)->withComments($comments);  
    }else{
      return redirect('/')->withErrors('You dont have permissions to edit this post comments!');
    }
  }

  //-------------------------------------------------------------------------------------------------------------------------------------------
  //-------------------------------------------------------------------------------------------------------------------------------------------
  
  //metod se poziva kad se u editpostcomments.blade.php klikne link Obrisi Komentar ispod nekog komentara, link salje _token i id komentara
  public function deletecomment(Request $request, $id){
    $comment = Comments::find($id);// nadji po id komentar koji treba obrisati
    // proveri da li user ima pravo da brise komentar tj da li can_post() metod User.php modela vraca true
    if($comment && $request->user()->can_post()){ 
      $comment->delete();//obrisi komentar
      //podesi Session::flash poruku koju ce prikazati editpostcomments.blade.php
      Session::flash('success', 'You have successfully deleted comment');
      return redirect()->back(); //vrati se nazad tj na editpostcomments.blade.php
    }else{
      // ako user nije author ili admin tj metod can_post() User.php modela vrati false redirectuj ga na homepage sa error porukom
      return redirect('/')->withErrors('You dont have permissions to delete this comment!');
    }
  }
  
  //-------------------------------------------------------------------------------------------------------------------------------------------
  //-------------------------------------------------------------------------------------------------------------------------------------------

  //metod se poziva kad se u editpostcomments.blade.php iz 'myblog\resources\views\admin\posts' klikne link za brisanje autora komentara-
  // - link postoji samo ako je rola autora komentara u 'users' tabeli == subscriber tj ne moze se obrisati neko kom je rola autor ili admin-
  // - u requestu stizu _token i id usera 
  public function deletecommentauthor(Request $request, $id){
    $author = User::find($id);
    if($author && $author->role == 'subscriber' && $request->user()->can_post()){
      $author->delete();
      //podesi Session::flash poruku koju ce prikazati editpostcomments.blade.php
      Session::flash('success', 'You have successfully deleted comment author and his comments');
      return redirect()->back(); //vrati se nazad tj na editpostcomments.blade.php
    }else{
      // ako user nije author ili admin tj metod can_post() User.php modela vrati false i ako author komentara nije samo subscriber tj ima -
      // - rolu admin ili author redirectuj ga na homepage sa error porukom
      return redirect('/')->withErrors('You dont have permissions to delete this comment author!');
    }
  }

  //-------------------------------------------------------------------------------------------------------------------------------------------
  //-------------------------------------------------------------------------------------------------------------------------------------------
  
  //metod pozivaju handleri na klik na btn za pretragu u editpostcomments.blade.php i klik na btn Dlete All ako pretraga nadje komentare koji 
  // - odgovaraju unetom pojmu, handleri koji salju AJAX-e su u searchcomments.js iz 'myblog\public\js'
  public function searchDeleteComments(Request $request){
    // ako request ima 'deleteids' znaci da je kliknut btn Delete All i stizu id-evi komentara koje treba obrisati
    if($request->has('deleteids')){
      $deleteids = $request['deleteids'];
      $postid = $request['postid'];
      Comments::destroy($deleteids); // brisemo komentare ciji su id-evi stigli AJAX-om
      //podesavamo Sesion::flash poruku i saljemo response cisto da AJAX u .done funkciji zna da je OK
      Session::flash('success', 'You have successfully deleted comments.');
      return response()->json(['status'=>'Hooray']);
    }else{//ako request nema 'deleteids' znaci da je submitovana forma za pretragu komentara u editpostcomments.blade.php 
      $search = $request['search']; // uzimamo uneti pojam u formu i id posta cije komentare pretrazujemo
      $postid = $request['postid'];
      // pretrazujemo 'comments' tabelu po unetom pojmu i koloni on_post posto je id posta stigao u requestu, takodje koristeci-
      //-::with('author') za svaki komentar vadimo i podatke usera koji ga je dodao
      $comments = Comments::with('author')->where('body', 'like', '%'.$search.'%')->where('on_post', $postid)->orderBy('created_at')->get();
      if($request->user()->can_post()){ 
        return response()->json(['comments' => $comments]);//vracamo odgovor AJAX-u iz searchcomments.js
      }else{// ako user nije admin ili author
        return redirect('/')->withErrors('You dont have permissions to edit this comments!');
      }
    }   
  }





}
