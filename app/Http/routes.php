<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::auth();

// ruta koja gadja getSearch() metod frontend PosdtsControllera koji ucitava vju za search tj search.blade.php iz foldera 'myblog\resources\views\posts'
Route::get('/searchform', 'PostsController@getSearch');
// ova ruta se poziva kad se submituje search forma u search.blade.php tj salje se ajax iz search.js iz foldera 'myblog\public\js' i gadja metod search frontend PostsControllera 
Route::post('/search', [
  'uses' => 'PostsController@search',
  'as'   => 'search'
]);
// ruta koja ide na author() metod frontend PostsControllera koji vadi po author_id koloni postove iz posts tabele i prikazuje ih u home.blade.php
Route::get('/author/{author_id}', [
  'uses' => 'PostsController@author',
  'as'   => 'author'
]);

// ruta koja ide na index() metod PostsControllera koji izvlaci 5 najnovijih odobrenih postova i salje u vju home.blade.php
Route::get('/', 'PostsController@index');
Route::get('/home', 'PostsController@index');
// ruta koja ide na show($slug) metod PostsControllera koji prikazuje jedan post a nalazi ga po slug koloni tj po $slug koji je stigao
Route::get('/{slug}', 'PostsController@show')->where('slug', '[A-Za-z0-9-_]+');

//ruta gadja likeordislike() metod CommentsControllera iz 'myblog\app\Http\Controllers', poziva se kad se klikne like ili dislike ikona u -
//show.blade.php ispod nekog komentara(salje se AJAX)
Route::post('/likeordislike', [
  'uses' => 'CommentsController@likeordislike',
  'as' => 'likeordislike'
]);


// ove rute mogu pozivati samo ulogovani useri posto su u 'auth' middleware-u
Route::group(['middleware' => ['auth']], function(){

  //ruta se poziva kad se sabmituje forma za pretragu komentara odredjenog posta u editpostcomments.blade.php
  Route::post('/searchdeletecomments', [
    'uses' => 'Auth\PostsController@searchDeleteComments',
    'as'   => 'searchdeletecomments'
  ]);

  //rute za datables paket
  Route::get('admin/posts/datatables', 'Auth\PostsController@datatables');
  Route::get('admin/posts/postsdata', 'Auth\PostsController@postsdata');

  // ruta za dodavanje komentara koja se poziva kad se submituje forma u show.blade.php, gadja store() metod u CommentsControlleru
  Route::post('comment/add', 'CommentsController@store');	

  //Ruta poziva metod editpostcomments() backend PostsControllera kad se u tabeli u allposts.blade.php klikne ikonica za komentare
  Route::get('admin/posts/editpostcomments/{id}', 'Auth\PostsController@editpostcomments');
  //admin/posts/deletecomment/'.$comment->id
  Route::get('admin/posts/deletecomment/{id}', 'Auth\PostsController@deletecomment');
  //ruta ide na deletecommentautor() metod backend PostsContrrollera koja brise autora nekg komentara i sve njegove komentare
  Route::get('admin/posts/deletecommentauthor/{id}', 'Auth\PostsController@deletecommentauthor');

  // ruta koja vodi na admin dashboard tj vju index.blade.php iz foldera 'myblog\resources\views\admin', gadja indexDashboard() metod PostsControllera 
  Route::get('admin/index', 'PostsController@indexDashboard');

  // resource rute za back end PostsController (folder'myblog\app\Http\Controllers\Auth')
  Route::resource('admin/posts/', 'Auth\PostsController');
  //ruta ide na index() metod backend PostsControllera koji prikazuje vju allposts.blade.php koji adminu prikazuje sve aktivne postove
  Route::get('admin/posts/allposts', 'Auth\PostsController@index');
  //Rute za kreiranje novog posta tj. ka create() i store() metodima backend PostsControllera 
  //ruta koja se poziva klikom na btn "Add New Post" u allposts.blade.php gadja create() metod backend PostsControllera koji prikazuje vju create.blade.php iz foldera 'myblog\resources\views\admin\posts' sa formom za kreiranje novog posta
  Route::get('admin/posts/new-post', 'Auth\PostsController@create');
  // ruta se poziva kad se submituje forma za kreiranje novog posta u create.blade.php iz foldera 'myblog\resources\views\admin\posts' gadja store() metod backend PostsControllera 
  Route::post('admin/posts/createpost', 'Auth\PostsController@store');
  //editovanje posta
  //ruta koja poziva edit() metod backend PostsControllera koji nalazi post u 'posts' tabeli poslug koloni i salje ga u vju edit.blade.php na editovanje
  Route::get('admin/posts/editpost/{slug}', 'Auth\PostsController@edit');
  // ruta koja se poziva kad se submituje forma za editovanje posta u edit.blade.php, gadja update() metod backend PostsControllera 
  Route::post('admin/posts/updatepost', 'Auth\PostsController@update');
  // ruta za brisanje posta, gadja destroy() metod backend PostsControllera
  Route::get('admin/posts/deletepost/{id}', 'Auth\PostsController@destroy');

  //ruta ide na authorPosts() metod AuthorsControllera koji vadi sve postove autora i njegove podatke da bi mogla da mu se menja slika i dodaje
  //biografije ili da se autor obrise
  Route::get('admin/authors/posts/{id}', 'Auth\AuthorsController@authorPosts');
  //upload slike u allposts.blade.php
  Route::post('admin/authors/uploadimage', 'Auth\AuthorsController@uploadImage');
  //kad se u allposts.blade.php klikne link za brisanje autora gajda se metod deleteAuthor() AuthorsControllera i salje mu se id autora za brisanje
  Route::get('admin/authors/deleteauthor/{id}', 'Auth\AuthorsController@deleteAuthor');
  //ruta ide na addAuthorBio() metod AuthorsControllera, poziva se kad se sabmituje forma za dodavanje biografije autoru u allposts.blade.php
  Route::post('/admin/authors/addauthorbio', 'Auth\AuthorsController@addAuthorBio');
  //
  Route::get('admin/authors/allauthors', 'Auth\AuthorsController@allAuthors');
  //ruta ka allUsers() metodu AuthorsControllera koji vraca vju allusers.blade.php u kom je forma za pretragu usera iz 'users' tabele
  Route::get('admin/authors/allusers', 'Auth\AuthorsController@allUsers');
  //ruta gadja searchUsers() metod AuthorsControllera za pretragu 'users' tabele po pojmu koji admin unese u input u allusers.blade.php,
  //koriste je hendleri u searchusers.js (hendler za keyup i hendler za paginaciju tj za klik na div 'moreres')
  Route::post('/admin/authors/searchusers', [
    'uses' => 'Auth\AuthorsController@searchUsers',
    'as'   => 'searchusers'
  ]);
  //ruta gadja makeadminorauthor() metod AuthorsControllera koji u zavisnosti od parametara koji mu stignu pravi od subscribera admina ili authora
  //koriste ga hendleri za klikove na btn-e Admin ili Authr u searchusers.js
  Route::post('/admin/authors/makeadminorauthor', [
    'uses' => 'Auth\AuthorsController@makeadminorauthor',
    'as'   => 'makeadminorauthor'
  ]);
  //rute gadja deleteuser() metod AuthorsControllera kad se u allusers.blade.php klikn btn Delete, searchusers.js salje AJAX sa id-em usera koji
  //se brise
  Route::post('/admin/authors/deleteuser', [
    'uses' => 'Auth\AuthorsController@deleteuser',
    'as'   => 'deleteuser'
  ]);

  //
  Route::get('admin/contactform', 'Auth\ContactController@getcontactform');
  //ruta se poziva kad se u contact.blade.php sabmituje forma za slanje maila i gadja sendmessage() u ContactControlleru
  Route::post('admin/sendmessage', 'Auth\ContactController@sendmessage');
  

});

