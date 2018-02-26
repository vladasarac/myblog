@extends('layouts.dashboard')

{{--vju koji prikazuje sve postove ili one koji su zadovoljili pretragu, poziva ga index() metod backend PostsControllera tj iz foldera 'myblog\app\Http\Controllers\Auth'--}}
{{-- takodje ga moze pozvati i metod authorPosts() AuthorsControllera kad u tabeli u koloni Author kliknemo ime autora i onda prikazuje samo postove tog autora  --}}
@section('content')

{{--dodavanje tinymce plugina za formu za dodavanje autorove biografije--}}
  <script type="text/javascript" src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script type="text/javascript">
    tinymce.init({
      selector : "textarea",
      plugins : ["advlist autolink lists link image charmap print preview anchor", "searchreplace visualblocks code fullscreen", "insertdatetime media table contextmenu paste"],
      toolbar : "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
      // ovako mozemo slici koju dodajemo dodati klasu img-responsive da bi se prilagodjavala velicini ekrana
      image_class_list: [
        {title: 'None', value: ''},
        {title: 'responsive', value: 'img-responsive'},
      ],
      
    });
  </script>

  {{-- ako neki metod AuthorsControllera vrati success ili error poruku prikazi ih ovde --}}
    @if(Session::has('error')) 
      <div class="alert alert-danger" role="alert">
        <p>Error:{{ Session::get('error') }}</p>
      </div>
    @endif
    @if(Session::has('success')) 
      <div class="alert alert-success" role="alert">
        <p>Success:{{ Session::get('success') }}</p>
      </div>
    @endif
    
  {{-- ako smo u vju dosli kroz authorPosts() metod AuthorsControllera prikazujemo i formu za dodavanje slike autora i za brisanje autora--}}
  @if(isset($title))

    {{-- prikazi trenutnu sliku autora --}}
    <img src="{{ asset('img/authors/' . $title->image) }}" id="showimages" height="90" width="70"><br><br>
    {{-- forma za upload slike autora koja ide na uploadImage() metod AuthorsCOntrollera--}}
    <form action="{{ url('/admin/authors/uploadimage') }}" id="editauthorphoto" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="author_id" value="{{ $title->id }}">
      <div class="row">
        <div class="col-md-12">
          <input type="file" id="inputimages" name="images">  
        </div>
      </div>  <br>
      <input type="submit" name="publish" class="btn btn-success" value="Save Image">
      <a href="{{ url('admin/authors/deleteauthor/'.$title->id.'?_token='.csrf_token()) }}" onclick="return confirm('Are you sure to delete this author and his posts?');" class="btn btn-danger">Delete Author</a>
    </form>
    <hr>
    {{-- forma za dodavanje autorove biografije tj za kolonu bio 'users' tabele preko metoda addAuthorBio() AuthorsControllera --}}
    <form action="{{ url('/admin/authors/addauthorbio') }}" method="post" id="editauthorbio">
      <h2>Add or Edit Author Bio</h2>
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="author_id" value="{{ $title->id }}">
      <div class="row">
        <div class="col-md-12">
          <textarea class="form-control" rows="27" id="authorbio" name="authorbio">{{ $title->bio }}</textarea>
        </div>
      </div>  <br>
      <input type="submit" name="addbio" class="btn btn-success" value="Add Bio">
    </form>
    
    {{--ako postoji varijabla $title znaci da smo u vju usli kroz authorPosts() metod AuthorsControllera  tj da gledamo  postove jednog autora--}}
    <h2>{{ $title->name }} Posts</h2>
  @else
    {{--ako ne postoji varijabla$title znaci da smo u vju usli kroz index()metod backend PostsControllera tj da gledamo sve postove svihautora --}}
    <h2 class="sub-header">All Posts</h2>
  @endif

  <div class="row">
  	<div class="col-md-9">
  	  <a href="{{ url('admin/posts/new-post') }}" class="btn btn-primary btn-sm">Add New Post</a>	
  	</div>
  	<div class="col-md-3">
  	{{--instalirali smo LaravelColective paket pa sada mozemoi ovako da pravimo forme--}}
  	{{--forma za pretragu 'posts' tabele ide na rutu admin/posts tj index() metod PostsControllera za backend tj iz foldera 'myblog\app\Http\Controllers\Auth'--}}
  	  {!! Form::open(['method'=>'GET', 'url'=>'admin/posts/', 'class'=>'navbar-form navbar-left', 'role'=>'search']) !!}	
  	    <div class="input-group custom-search-form">
  	      <input type="text" name="search" class="form-control" placeholder="Search...">  	 
  	      <span class="input-group-btn">
  	      	<button type="submit" class="btn btn-default-sm">
  	      	  <i class="fa fa-search"></i>	
  	      	</button>
  	      </span>   	
  	    </div>
  	  {!! Form::close() !!}
  	</div>
  </div>
  {{--tabela koja prikazuje postove koje vrati metod index() PostsControllera za backend tj iz foldera 'myblog\app\Http\Controllers\Auth'--}}
  <div class="table-responsive">
  	<table class="table table-striped">
  	  <thead>
  	    <th>#</th>
        <th>Author</th>
  	    <th>Title</th>
  	    <th>Description</th>
  	    <th>Post</th>	
  	    <th>Url's</th>
  	    <th>Image</th>
  	    <th>Created</th>
  	    <th>Actions</th>
  	  </thead>
  	  <tbody>
  	    <?php $no = 1; ?>
  	  	@foreach($posts as $post)
  	  	  <tr>
  	  	  	<td>{{ $no++ }}</td>
            <td><a href="{{ url('admin/authors/posts/'.$post->author->id.'?_token='.csrf_token()) }}">{{ $post->author->name }}</a></td>
  	  	  	<td>{{ $post->title }}</td>
  	  	  	<td>{{ $post->description }}</td>
  	  	  	{{--skracena body kolona 'posts' tabele na 120 karaktera--}}
  	  	  	<td>{{ str_limit($post->body, $limit = 120, $end = '...') }}</td>
  	  	  	{{--link ide na show() metod frontend PostsControllera koji pronalazi post po slugu i prikazuje ga u vjuu show.blade.php iz foldera 'myblog\resources\views\posts' --}}
  	  	  	<td>{!! ('<a href='.url("/".$post->slug).'>'.$post->slug.'</a>') !!}</td> 
  	  	  	<td><img src='{{ url("img/".$post->images) }}' id="showimages" style="max-width:100px;max-height:50px;float:left;"></td>
  	  	  	<td>{{ $post->created_at }}</td>
  	  	  	<td>
  	  	  	  {{--u ovoj koloni su dugmad za edit i delete posta--}}
  	  	  	  <form class="" action="" method="post">
  	  	  	  	<input type="hidden" name="_method" value="delete">
  	  	  	  	<input type="hidden" name="_token" value="{{ csrf_token() }}">
                {{-- post moze editovati samo kreator posta tj ako je id ulogovanog usera == author_id koloni 'posts' tabele --}}
                @if($post->author->id == Auth::user()->id)
                  {{-- link ka edit.blade.php tj ka ruti'admin/posts/editpost/{slug}' koja gadja edit() metod backend PostsControllera --}}
  	  	  	  	  <a href="{{ url('admin/posts/editpost/'.$post->slug) }}" class="btn btn-primary btn-xs">
  	  	  	  	    <span class="glyphicon glyphicon-edit"></span>	
  	  	  	  	  </a>
                @endif
                {{-- post moze brisati samo kreator posta ili admin --}}
                @if($post->author->id == Auth::user()->id || Auth::user()->role == 'admin')
                  {{-- link ka ruti'admin/posts/deletepost/{id}' za brisanje posta koja gadja destroy() metod backend PostsControllera --}}
  	  	  	  	  <a href="{{ url('admin/posts/deletepost/'.$post->id.'?_token='.csrf_token()) }}" onclick="return confirm('Are you sure to delete this post?');" class="btn btn-danger btn-xs">
  	  	  	  	    <span class="glyphicon glyphicon-trash"></span>	
  	  	  	  	  </a>
                @endif
                <br><br>
                <a href="{{ url('admin/posts/editpostcomments/'.$post->id) }}" class="btn btn-primary btn-xs">
                  <span class="glyphicon glyphicon-comment"></span>  
                </a>
                {{-- prikazi broj komentatra koje ima post --}}
                <span><b>{{ count($post->comments) }}</b></span>
  	  	  	  </form>	
  	  	  	</td>
  	  	  </tr> 	  	  
  	  	@endforeach
  	  </tbody>	
  	</table>
  	{{--paginacija--}}
    <ul class="pager">
  	  {!! $posts->links() !!} 
    </ul>

    {{-- <ul class="pager">
      @if($posts->currentPage() != 1)
        <li>
          <a href="{{ $posts->previousPageUrl() }}" rel="prev">
            <span class="glyphicon glyphicon-menu-left" aria-hidden="true">Previous</span>  
          </a>
        </li>
      @else
        <li class="disabled">
          <span class="glyphicon glyphicon-menu-left" aria-hidden="true">Previous</span>
        </li>
      @endif
      @if($posts->hasMorePages())
        <a href="{{ $posts->nextPageUrl() }}" rel="next">
          <span class="glyphicon glyphicon-menu-right" aria-hidden="true">Next</span> 
        </a>
      @else
        <li class="disabled">
          <span class="glyphicon glyphicon-menu-right" aria-hidden="true">Next</span>
        </li>
      @endif
    </ul> --}}

  </div>{{-- kraj diva .table-responsive --}}
  

@endsection






