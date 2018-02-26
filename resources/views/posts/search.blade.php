@extends('layouts.app1')

@section('header')
  <div class="site-heading">
    <h1>Search</h1>
    <hr class="small">
    <span class="subheading">A Clean Blog Theme by Start Bootstrap</span>
  </div>
@endsection

@section('content')
  <div class="row">
  <div class="col-md-9">

  	<h2>Serach MyBlog</h2>
    <form action="{{ url('/admin/posts/createpost') }}" method="post" enctype="multipart/form-data">

  	  <input type="hidden" name="_token" value="{{ csrf_token() }}">

  	  <div class="form-group">
  	    <input type="text" id="searchinput" name="searchinput" required="required" placeholder="Enter something here" class="form-control">
      </div>
      <div class="form-group">
        <input type="submit" id="search" name="search" class="btn btn-success" value="Search">
      </div>
    </form>

  </div>{{-- kraj div .col-md-9 --}}
  </div>{{-- kraj div .row --}}

  <div class="body"></div>
  <div class="paginacija"></div>

  <script>
    var token = '{{ Session::token() }}';
  	var url = '{{ route('search') }}'; 
    var homeurl = '{{ url('/') }}'
    	
  </script>
@endsection