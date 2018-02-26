@extends('layouts.dashboard')

@section('content')
<div class="row">
  <div class="col-md-9">
  @foreach($authors as $author)

    <ul class="list-group">
      <li class="list-group-item">
        
        <span class="badge">{{ $author->posts()->count() }}</span>
        <p class="lead">
        <img style="float:left" src="{{ asset('img/authors/' . $author->image) }}" class="img-circle" id="showimages" height="50" width="50">
        <a class="link" style="color: black; text-decoration: none" href="{{ url('admin/authors/posts/'.$author->id.'?_token='.csrf_token()) }}">
        &nbsp; &nbsp; {{ $author->name }},
        </a>
        <i>email: </i>{{ $author->email }},
        <i>Broj Postova: </i>
        </p>
      </li>
    </ul>
    
  @endforeach
  </div>
</div>
<script src="//code.jquery.com/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $('.link').mouseenter(function(){
  	$(this).parents('li').addClass('plava-senka');
  });	
  $('.link').mouseleave(function(){
    $(this).parents('li').removeClass('plava-senka');
  }); 
});	
</script>

@endsection
