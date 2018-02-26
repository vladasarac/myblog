@extends('layouts.dashboard')

{{--ovo je vju u kom je forma za editovanje posta, poziva ga edit() metod backend PostsControllera i salje mu post za editovanje--}}
@section('content')
  <h2>Update Post</h2>
  {{--dodavanje tinymce plugina--}}
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
      ]
    });
  </script>
  {{-- forma za update posta, posto se mogu dodatui i slike mora biti enctype="multipart/form-data", ide na rutu '/admin/posts/updatepost' koja gadja update() metod backend PostsControllera --}}
  <form action="{{ url('/admin/posts/updatepost') }}" method="post" enctype="multipart/form-data">
  	<input type="hidden" name="_token" value="{{ csrf_token() }}">
  	<input type="hidden" name="post_id" value="{{ $post->id }}{{ old('$post->id') }}">
  	<input type="hidden" name="post_descriptions" value="{{ $post->id }}{{ old('$post->id') }}">
  	<div class="form-group">
  	  <p>Title</p>{{--nslov posta, ako je submitovano a nije upisano prikazi taj naslov a ako njega nema prikazi naslov koji je stigao iz edit() metoda tj onaj iz tabele posts--}}
  	  <input type="text" name="title" value="@if(!old('title')){{$post->title}}@endif{{old('title')}}" required="required" placeholder="Enter title here" class="form-control">
  	  <br>
  	  <p>Description</p>{{--description posta, ako je submitovano a nije upisano prikazi taj description a ako njega nema prikazi description koji je stigao iz edit() metoda tj onaj iz tabele posts--}}
  	  <input type="text" name="description" value="@if(!old('description')){{$post->description}}@endif{{old('description')}}" required="required" placeholder="Enter description here" class="form-control">	
  	  <br>
  	  <p>Thumbnail</p>{{--prikazi sliku koja je vec dodata postu--}}
  	  <img src="{{ url('img/'.$post->images) }}" id="showimages" style="max-width:200px;max-height:200px;float:left;">
  	  <div class="row">
  	  	<div class="col-md-12">
  	  	  <input type="file" id="inputimages" name="images">	
  	  	</div>
  	  </div>
  	</div>
  	{{--textarea za unos body-a posta--}}
  	<div class="form-group">
  	  <textarea name="body" class="form-control" rows="20">
  	    {{--ako nije submitovano tj ne postoji old(body) prikazi body koji je poslao edit() metod --}}
  	  	@if(!old('body'))
  	  	  {!! $post->body !!}
  	  	@endif 	
  	  	{{--ako je submitovano a nije upisano prikazi taj body--}}
  	  	  {!! old('body') !!}
  	  </textarea>	
  	</div>
  	@if($post->active == '1')
  	{{--ako je post vec objavljen tj active kolona 'posts' tabele je 1 tekst buttona ce biti Update--}}
  	  <input type="submit" name="publish" class="btn btn-success" value="Update">
  	@else
  	{{--ako post nije objavljen tj active kolona je 0 tekst buttona ce biti Publish--}}
  	  <input type="submit" name="publish" class="btn btn-success" value="Publish">
  	@endif
  	{{--ako user hoce samo da sacuva post a ne i da ga objavi--}}
      <input type="submit" name="save" class="btn btn-default" value="Save As Draft">
      {{-- link ka ruti'admin/posts/deletepost/{id}' za brisanje posta koja gadja destroy() metod backend PostsControllera --}}
      <a href="{{ url('admin/posts/deletepost/'.$post->id.'?_token='.csrf_token()) }}" onclick="return confirm('Are you sure to delete this post');" class="btn btn-danger">Delete</a>
  </form>
@endsection