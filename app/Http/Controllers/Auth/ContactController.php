<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Mail;
use Auth;

class ContactController extends Controller{

  //metod samo ucitava vju contact.blade.php iz 'myblog\resources\views' u kom je forma u kojoj ulogovani user moze napisati email MyBlog-u
  //metod se poziva kad se u navigaciji u frontendu klikne link Contact	
  public function getcontactform(Request $request){
    $authors = User::where('role', '!=', 'subscriber')->get();//ovo nam treba da bi navigacija u app.blade.php radila kako treba
    $contact = 1;//ovo saljemo takodje zbog app.blade.php layouta da bi znao da je u vjuu contact da bi prikazao odgovarajucu sliku
    //pozivamo vju contact.blade.php i saljemo mu $authors i $contact
    return view('contact')->withAuthors($authors)->withContact($contact);       
  }

  //----------------------------------------------------------------------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------------------------------------------------------------------

  //metod se poziva kad se u contact.blade.php sabmituje forma za slanje maila preko rute'admin/sendmessage'
  public function sendmessage(Request $request){
  	$this->validate($request, [  // prvo validacija unosa u formu u contact.blade.php
      'subject' => 'required|min:3',
      'message' => 'required|min:10'
    ]);	
    //uzimamo ime ulogovanog usera tj onog koji salje mail da bi ga dodali u mail kasnije, takodje isto za njegov email
    $name = Auth::user()->name;
    $useremail = Auth::user()->email;
    // array koji cemo poslati u vju 'usermessage.blade.php' iz 'myblog\resources\views\email' koji ce zapravo biti email
    $data = array(
      'subject' => $request->subject,//originalni subject koji je user uneo u formu
      //ovde subjectu koji je user uneo dodajemo njegov mail da bi admin video u naslovu maila ko mu ga salje
      'subject2' => '(message from ' . $useremail . ') ' . $request->subject,
      // VAZNO ne sme se dati naziv kljucu 'message' zato sto je to laravelova zasticena varijabla tako da cemo mi message zvati bodyMessage
      'bodyMessage' => $request->message,
      'user' => $name,//saljemu u vju koji je mail i ime i emial usera da bi ih ubacili u sadrzaj maila da bi admin znao od koga je mail
      'useremail' => $useremail
    ); 
    //pozivamo laravelov metod Mail (tj fasadu) i dajemo joj podatke koji su potrebni da napravi mail i posalje ga
    //(sam mail je vju usermessage.blade.php iz 'myblog\resources\views\email')
    Mail::send('email.usermessage', $data, function($message) use ($data){
      $message->from($data['useremail']);
      // ovo je obavezno da bi znao kome da salje mail , mogu ovde da napisem npr lacparacku@yahoo.com i onda ce tamo slati
      $message->to('vladasarac@hotmail.com');
      //$message->to('kantarion35@gmail.com');  // a mogu poslati poruku tj mail i samom sebi na kantarion35@gmail.com
      //$message->to('lacparacku@yahoo.com');   
      // $message->subject($data['subject'].'(message from '$data['useremail'].')');
      $message->subject($data['subject2']);
    });
    // na kraju success message i redirect
    Session::flash('success', 'Your Email was Sent!');// podesi poruku koju ce prikazati _messages.blade.php
    // redirectuj na pocetnu stranicu (on je ovo napisao return redirect->url('/') ali mi tako nije radilo pa sam prepravio da radi)
    return redirect()->back(); 
  }

}
