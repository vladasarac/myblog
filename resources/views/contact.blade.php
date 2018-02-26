@extends('layouts.app')
{{-- vju poziva getcontactform() metod ContactControllera kad se u navigaciji klikne link 'contact' ako user zeli da napise mail MyBlog-u to
moze uraditi tako sto popuni formu u ovom vjuu, forma gadja rutu /admin/sendmessage koja gadja sendmessage() metod ContactControllera koji dalje 
salje mail na adresu koja je tamo upisana--}}
@section('header')
  <div class="site-heading">
    <h1 style="color: #337ab7;">Contact Us</h1> 
    <h2 class="subheading">Send EMail to MyBlog</h2> 
  </div>
@endsection

@section('content')

  {{--prikazi errore ako ih ima, tj ako validacija koju radi sendmessage() metod ContactControllera ne prodje--}}
  @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  {{--ako je mail uspesno poslat tj sendmessage() metod ContactControllera ubacuje u Session success poruku a ovde je prikazujemo--}}
  @if(Session::has('success')) 
    <div class="alert alert-success" role="alert">
      <strong>Success:</strong>{{ Session::get('success') }}
    </div>
  @endif
  {{--forma ima input za naslov maila i textarea za sadrzaj koju user popunjava kad salje mail, gadja metod sendmessage() ContactControlerra
  preko rute'/admin/sendmessage'--}}
  <form action="{{ url('/admin/sendmessage') }}" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <h3 style="color: #337ab7;">Subject:</h3>
    <div class="form-group">
	    <input id="subject" name="subject" class="form-control" value="{{ old('subject') }}">
	  </div>
    <h3 style="color: #337ab7;">Message:</h3>
    <div class="form-group">
      <textarea rows="15" id="message" name="message" class="form-control">{{ old('message') }}</textarea>
	  </div>
	  <input type="submit" value="Send Message" class="btn btn-success">
  </form>
@endsection





































