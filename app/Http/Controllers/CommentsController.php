<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Posts;
use App\Comments;
use Redirect;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // metod se poziva kad se sabmituje forma za dodavanje komentara u show.blade.php koji prikazuje jedan text, preko rute 'comment/add' koja je u auth middlerware grupi
    public function store(Request $request){
      $input['from_user'] = $request->user()->id;// uzmi userov id za from_user kolonu 'comments' tabele
      $input['on_post'] = $request->input('on_post');// uzmi postov id za on_post kolonu 'comments' tabele
      $input['body'] = $request->input('body');// uzmi sadrzaj komentara id za body kolonu 'comments' tabele
      $slug = $request->input('slug');// uzmi slug koji je stigao u requestu da bi mogli da pozovemo show() metod PostsControllera da opet ucita show.blade.php posto on trazi slug posta
      Comments::create($input); // upisi novi red u 'comments' tabelu 
      // redirectuj na show() metod PostsControllera i posalji mu slug posta da opet prikaze post u show.blade.php vjuu
      return redirect($slug)->with('message', 'Comment published');
    }

    //-------------------------------------------------------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------------------------------------------------------
 
    //metod se koristi za Like-ovanje ili Dislike-ovanje nekog komentara u show.blade.php tj povecava za 1 like ili dislike kolonu 'comments' 
    //tabele tamo gde je id onaj koji je stigao AJAX-om iz likedislike.js u kom su handleri za klikove na Like i Dislike ikone
    //stize AJAX preko rute '/likeordislike' i u njemu je id komentara i likeordislike u kojoj pise like ili dislike tj da li je komentar 
    //lajkovan ili dislajkovan
    public function likeordislike(Request $request){
      //uzmi id koji je stigao AJAX-om iz likedislike.js
      $commentid = $request['commentid'];
      //proveravamo koja je ikona klinuta (Like ili Dislike) tj koju cemo kolonu da povecamo za 1
      $likeordislike = $request['likeordislike']; 
      if($likeordislike == 'like'){// ako je user lajkovao komentar povecavamo likes kolonu 'comments' tabele za 1
        Comments::where('id', $commentid)->increment('like');
        //$cookie = Cookie::forever('likeordislike', $likeordislike);
      }else{// ako je user dislajkovao komentar povecavamo dislikes kolonu 'comments' tabele za 1
        Comments::where('id', $commentid)->increment('dislike');
        //$cookie = Cookie::forever('likeordislike', $likeordislike);
      } 
      $minutes = 500000;// duzina trajanja cookie-a u minutima
      //vracamo odgovor likedislike.js-u i saljemo cookie
      return response()->json(['likeordislike' => $likeordislike])->cookie('likeordislike'.$commentid, $likeordislike, $minutes); 
    }

    //-------------------------------------------------------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------------------------------------------------------
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
