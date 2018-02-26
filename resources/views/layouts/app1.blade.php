<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>MyBlog</title>
  <!-- Bootstrap Core CSS -->
  <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Theme CSS -->
  <link href="{{ asset('css/clean-blog.min.css') }}" rel="stylesheet">
  <!-- Custom Fonts -->
  <link href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

  {{-- ja dodao moj stajling --}}
  <link href="{{ asset('css/zatinyimg.css') }}" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

{{--layout na koji se kace frontend vjuovi--}}

<body>

  <!-- Navigation -->
  <nav class="navbar navbar-default navbar-custom navbar-fixed-top">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header page-scroll">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          Menu <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand" href="/">My Blog</a>
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="/">Home</a></li>
          <li><a href="/searchform">Search</a></li>
          <li><a href="/admin/contactform">Contact</a></li>
          {{--dropdown koji ce prikazivati imena autora postova koji ce biti linkovi ka njihovim postovima tj ka ruti /author/{author_id}--}}
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Authors<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              @foreach($authors as $author)
                <li><a href="{{ url('/author/'.$author->id) }}">{{ $author->name }}</a></li>
              @endforeach
            </ul>
          </li>
          {{--linkovi za login, register i logout--}}
          @if(Auth::guest()) {{--ako je user Ne Ulogovan prikazuju se linkovi za login i register--}}
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Members<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ url('/login') }}">Login</a></li>
                <li><a href="{{ url('/register') }}">Register</a></li>
              </ul>
            </li>
          @else  {{--ako je user Ulogovan prikazuje se link za logout--}}
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }}<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ url('/logout') }}">Logout</a></li>
                {{--link ka admin dashboardu tj ide na indexDashboard() metod frontend POstsControllera --}}
                <li><a href="/admin/index ">Admin Dashboard</a></li> 
              </ul>
            </li>
          @endif
        </ul>
      </div>
    <!-- /.navbar-collapse -->
    </div>
  <!-- /.container -->
  </nav>
  <!-- Page Header -->
  <!-- Set your background image for this header on the line below. -->
  <header class="intro-header" style="background-image: url('img/search.jpg')">
  <!-- <header class="intro-header" style="background-image: url({{ asset('/img/about-bg.jpg') }})"> -->
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
          @yield('header')
          {{--ovaj div smo premestili u home.blade.php u @section('header') i u show.blade.php isto u @section('header')--}}
          {{-- <div class="site-heading">
            <h1>MyBlog</h1>
            <hr class="small">
            <span class="subheading">A Clean Blog Theme by Start Bootstrap</span>
          </div> --}}
        </div>
      </div>
    </div>
  </header>
  <!-- Main Content -->
  <div class="container">
    <div class="row">
      <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
        <div class="post-preview">

          @yield('content')
          
          
        </div>
        @yield('pagination')
      </div>
    </div>
  </div>
  <hr>
  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
          <ul class="list-inline text-center">
            <li>
              <a href="#">
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
                </span>
              </a>
            </li>
            <li>
              <a href="#">
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
                </span>
              </a>
            </li>
            <li>
              <a href="#">
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-github fa-stack-1x fa-inverse"></i>
                </span>
              </a>
            </li>
          </ul>
          <p class="copyright text-muted">Copyright &copy; Your Website 2016</p>
        </div>
      </div>
    </div>
  </footer>
  <!-- jQuery -->
  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
  <!-- Contact Form JavaScript -->
  <script src="{{ asset('js/jqBootstrapValidation.js') }}"></script>
  <script src="{{ asset('js/contact_me.js') }}"></script>
  <!-- Theme JavaScript -->
  <script src="{{ asset('js/clean-blog.min.js') }}"></script>
  <script src="{{ URL::to('js/search.js') }}"></script>
</body>

</html>
