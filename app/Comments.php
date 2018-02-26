<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    // kolone zabranjene za modifikovanje 
    protected $guarded = [];

    //posto komentar pripada useru koji je subscriber pravimo relaciju sa 'users' tabelom preko from_user kolone
    public function author(){
      return $this->belongsTo('App\User', 'from_user');	
    }
    //posto komentar pripada postu pravimo relaciju sa 'posts' tabelom preko kolone on_post
    public function post(){
      return $this->belongsTo('App\Posts', 'on_post');	
    }
}
