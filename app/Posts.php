<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    // kolone zabranjene za modifikovanje 
    protected $guarded = [];

    // one to many relacija sa 'comments' tabelom (preko kolone on_post koja je foreign key na id kolonu 'posts' tabele)
    public function comments(){
      return $this->hasMany('App\Comments', 'on_post');
    }
    //posto post pripada useru koji je author pravimo relaciju sa 'users' tabelom preko author_id kolone
    public function author(){
      return $this->belongsTo('App\User', 'author_id');	
    }
}
