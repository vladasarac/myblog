
{{--ovaj vju je zapravo mail koji koristi sendmessage() metod ContactControllera kada user salje mail MyBlog-u, vjuu se salju podatci usera
-koji je ulogovan tj koji salje mail(ime i email adresa da bi admin znao ko mu salje mail) i takodje userov unos u formu u contact.blade.php
-sto ce zapravo biti subject i body maila--}}

<h1>You have a new contact message from user: {{ $user }}, {{$useremail}}</h1>

<h3>{{ $subject }}</h3>
<div>
  {{ $bodyMessage }}	
</div>




