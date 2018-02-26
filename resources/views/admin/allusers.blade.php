@extends('layouts.dashboard')

{{-- vju koji poziva metod allUsers() AuthorsCOntrollera kad se u admin dashboardu klikne link users(link je vidljiv samo adminima) i sluzi
  da se pronadje neki user i onda se moze obrisati ili proglasiti za Admina ili Authora, sve to rade hendleri u searchusers.js--}}
@section('content')
<div class="row">
  <div class="col-md-9">
    <h3>Find User</h3>
    <input type="text" name="searchuser" id="searchuser" class="form-control" placeholder="Find User...">
    <hr> 
  </div>
</div>

<div class="row">
  <div class="col-md-9">
  {{--div u koji ce se ubacivati HTML koji se generise posle slanja AJAX-a iz searchusers.js kad se unese nesto u input #searchuser--}}
    <div class="searchresults">
    
    </div>
  </div>
</div>

<script type="text/javascript">

    //ove varijable se koriste pri slanju AJAX-a iz searchusers.js
    var token = '{{ Session::token() }}';
    var url = '{{ route('searchusers') }}';
    var makeadminorauthorurl = '{{ route('makeadminorauthor') }}';
    var deleteuserurl = '{{ route('deleteuser') }}';
  </script>

@endsection