<!-- <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    
  </head>
  <body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>   -->





<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <!-- datatables css -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="{{ asset('css/ie10-viewport-bug-workaround.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('dashboard.css') }}" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="{{ asset('js/ie-emulation-modes-warning.js') }}"></script>
    <!-- jquery -->
    <script src="//code.jquery.com/jquery.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
      .plava-senka{
        background-color: #0074D9;
      }
      /*ovo koristi hendler u searchusers.js kad prikazuje usere koje pretraga pronadje*/
      .adminbtn{
        margin-right: 5px;
      }
      #moreres{
        cursor: pointer;
      }
      .user{
        padding-top: 20px;
        padding-bottom: 20px; 
        border-bottom: 1px solid #c1bfbf;
      }
      /*ovo koristi hendler u searchusers.js kad se menja rola usera(tj kad se user proglasava za admina ili autora)*/
      .newcolor{
        background-color: #fff !important;
        -webkit-transition: background-color 1700ms linear;
        -ms-transition: background-color 1700ms linear;
        transition: background-color 1700ms linear;
     }
     /**/
     .gif{
        margin-left: 25%;
     }
    </style>
  </head>

  <!-- layout na koji se kace backend vjuovi -->

  <body>

    <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <!-- logo sajta i link na rutu 'admin/index' koja ucitava admin dashboard tj ide na indexDashboard() metod PostsControllera -->
          <a class="navbar-brand" href="/admin/index">
            <img alt="brand" src="{{ asset('img/logo.png') }}">
          </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            @if(Auth::guest())
              <li class="dropdown">
              	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
              	  Memebers<span class="caret"></span>	
              	</a>
              	<ul class="dropdown-menu" role="menu">
              	  <li><a href="{{ url('/login') }}">Login</a></li>
                  <li><a href="{{ url('/register') }}">Register</a></li>
              	</ul>
              </li>
            @else
              <li class="dropdown">
              	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                  {{ Auth::user()->name }} <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                  <li><a href="{{ url('/') }}"><i class="fa fa-btn fa-newspaper-o"></i>Frontend</a></li>
                </ul>
              </li>
            @endif
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            {{--link ka ruti /admin/authors/posts/id koja prikazuje sve postove jednog autora i njegove podatke gadja authorPosts() metod -
            AuthorsControllera ovu rutu moze pozvati admin za sve autore i autor samo za sebe(pogledaj kontroler)--}}
            <li><a href="/admin/authors/posts/{{ Auth::user()->id }}">Profile</a></li>
            <li><a href="/admin/posts/allposts">All Posts</a></li>
            @if(Auth::user()->role == 'admin')
              <li><a href="/admin/authors/allauthors">All Authors</a></li>
              <li><a href="/admin/authors/allusers">Users</a></li>
            @endif        
            <li><a href="/admin/posts/datatables">Datatables</a></li>  
            <li><a href="/admin/posts/alldrafts">Drafts</a></li>
            <li><a href="/admin/posts/allcomments">Comments</a></li>
            <li><a href="#">Label</a></li>            
            <li><a href="#">Profile</a></li>
          </ul>
        </div>

        <div class="col-sm-9 col-md-10 main">
          @yield('content')        	
        </div>

      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="{{ asset('js/holder.min.js') }}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ asset('js/ie10-viewport-bug-workaround.js') }}"></script>

    <!--link ka fajlu searchcommnets.js iz 'myblog\public\js' u kom su hendleri za pretragu komentara posta u editpostcomments.blade.php iz'myblog\resources\views\admin\posts'-->
    <script src="{{ URL::to('js/searchcomments.js') }}"></script>
    {{--link ka fajlu searchusers.js iz 'myblog\public\js' u kom su hendleri za pretragu usera u formi koja je u all.users.blade.php iz
      'myblog\resources\views\admin'--}}
    <script src="{{ URL::to('js/searchusers.js') }}"></script>

    <!-- prikazi image ako vec ima dodat u create.blade.php -->
    <script type="text/javascript">
      function readURL(input){
        if(input.files && input.files[0]){
          var reader = new FileReader();
          reader.onload = function(e){
            $('#showimages').attr('src', e.target.result);
          }
          reader.readAsDataURL(input.files[0]);
        }
      }
      $("#inputimages").change(function(){
        readURL(this);
      });
    </script>
    <!-- jquery -->
    {{-- <script src="//code.jquery.com/jquery.js"></script> --}}
    <!-- Datatables -->
    <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
    @stack('scripts')

  </body>
</html>
