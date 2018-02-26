@extends('layouts.dashboard')

{{--vju koji prikazuje --}}

@section('content')
  
<h2>{{ $post->title }}</h2><br><hr>

{{--  --}}
<div class="row">
  <div class="col-md-9 searchform">
  <h3 class="text-info">Serach This Post Comments</h3>
  <form action="{{ url('/admin/posts/searchdeletecomments') }}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="form-group">
        <input type="text" id="searchcommentsinput" name="searchcommentsinput" required="required" placeholder="Find Comments" class="form-control">
      </div>
      <div class="form-group">
        <input type="submit" id="searchcomments" name="searchcomments" class="btn btn-success" value="Search">
      </div>
    </form>

    {{-- ako searchDeleteComments() metod backend PostsControllera uspesno obrise komentare u Session::flash ce ubaciti uspeh poruku koju ovde ispisujemo --}}
    @if(Session::has('success')) 
      <div class="alert alert-success" role="alert">
        <p>Success:{{ Session::get('success') }}</p>
      </div>
    @endif

  </div>
</div>




<div class="row">
  <div class="col-md-9 commentslist">

  @if(count($comments) <= 0)
    <h2 class="text-danger">This post does not have comments!</h2>  
  @else
    @foreach($comments as $comment)
  	  <p>{{ $comment->body }}</p>
  	  <a href="{{ url('admin/posts/deletecomment/'.$comment->id.'?_token='.csrf_token()) }}" onclick="return confirm('Are you sure to delete this comment?');" class="text-danger">
  	  	  	  	  Delete Comment<span class="glyphicon glyphicon-trash"></span>	
  	  </a>
  	  <h4><i>User Name:</i> {{ $comment->author->name }}, <i>User ID:</i> {{ $comment->author->id }}</h4>
  	  @if($comment->author->role == "subscriber")
  	  {{-- ovo je link za brisanje autora komentara ako mu je rola subscriber tj nije author ili admin --}}
 	    <a href="{{ url('admin/posts/deletecommentauthor/'.$comment->author->id.'?_token='.csrf_token()) }}" onclick="return confirm('Are you sure to delete this comment author?');" class="text-danger">
                  Delete Comment Author<span class="glyphicon glyphicon-trash"></span> 
      </a>	
  	  @endif

  	  <hr>
    @endforeach  
  @endif

  </div>
</div>

{{-- ovo je potrebno za AJAX-e koje salje searchcomments.js kad se pretrazuju ili brisu komentari nekog teksta --}}
<script>
  var postid = {{ $post->id }}
  var token = '{{ Session::token() }}';
  var url = '{{ route('searchdeletecomments') }}';     
</script>

@endsection