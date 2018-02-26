<?php

namespace App;

//use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    //use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // metod koji proverava da li je useru dozvoljeno da kreira nove postove tj da li je u role koloni 'users' tabele upisano da je author ili admin ili je samo subscriber (koji ne moze da dodaje postove)
    // ovaj metod koristi create() metod backend PostsControllera
    public function can_post(){
      $role = $this->role; // uzmi sta pise u role koloni 'users' tabele  
      if($role == 'author' || $role == 'admin'){ // ako je user autor ili admin vrati true
        return true;
      }   
      // ako je subscriber vrati false
      return false;
    }

    // metod koji proverava da li je user admin tj da li u role koloni 'users' tabele pise 'admin'
    public function is_admin(){
      $role = $this->role; // uzmi sta pise u role koloni 'users' tabele  
      if($role == 'admin'){ // ako je admin vrati true
        return true;
      }    
      // ako nije admin vrati false
      return false;
    }

    // pravimo one-to-many relaciju sa 'posts' tabelom posto user moze da poseduje vise postova, po author_id koloni 'posts' tabele koja je foreign key od id kolone 'users' tabele
    public function posts(){
      return $this->hasMany('App\Posts', 'author_id');  
    } 

    //  pravimo one-to-many relaciju sa 'comments' tabelom posto user moze da poseduje vise komentara, po from_user koloni 'comments' tabele koja je foreign key od id kolone 'users' tabele
    public function comments(){
      return $this->hasMany('App\Comments', 'from_user');  
    } 
}
