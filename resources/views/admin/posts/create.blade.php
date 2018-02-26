@extends('layouts.dashboard')

{{--vju za kreiranje novog posta (koristimo tinymce plugin da bi textarea za kreiranje posta izgledala kao text editor)--}}
@section('content')
  <h2>Create New Post</h2>
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
      ],
      
    });
  </script>
  {{-- forma za kreiranje posta, posto se mogu dodatui i slike mora biti enctype="multipart/form-data" --}}
  <form action="{{ url('/admin/posts/createpost') }}" method="post" enctype="multipart/form-data">
  	<input type="hidden" name="_token" value="{{ csrf_token() }}">
  	<div class="form-group">
  	  <p>Title</p>{{--nslov posta--}}
  	  <input type="text" name="title" value="{{ old('title') }}" required="required" placeholder="Enter title here" class="form-control">
  	  <br>
  	  <p>Description</p>{{--description posta--}}
  	  <input type="text" name="description" value="{{ old('description') }}" required="required" placeholder="Enter description here" class="form-control">	
  	  <br>
  	  <p>Thumbnail</p>
  	  <img src="http://placehold.it/100x100" id="showimages" style="max-width:200px;max-height:200px;float:left;">
  	  <div class="row">
  	  	<div class="col-md-12">
  	  	  <input type="file" id="inputimages" name="images">	
  	  	</div>
  	  </div>
  	</div>
  	{{--textarea za unos body-a posta--}}
  	<div class="form-group">
  	  <textarea name="body" class="form-control" rows="20"></textarea>	
  	</div>
  	{{--ako zelimo da objavimo post tj da active kolona 'posts' tabele bude 1 onda ovaj btn--}}
  	<input type="submit" name="publish" class="btn btn-success" value="Publish">
  	{{--ako zelimo samo da sacuvamo posta a kolona active 'posts' tabele ce biti 0 tj nece biti vidljiv obicnim posetiocima onda ovaj btn--}}
  	<input type="submit" name="save" class="btn btn-success" value="Save Draft">
  </form>

@endsection