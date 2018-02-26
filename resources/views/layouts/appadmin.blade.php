<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <link href="/css/clean-blog.css" rel="stylesheet">
  <script>
  	window.Laravel = <?php echo json_encode([
  	  'csrfToken' => csrf_token(),	
  	]); ?>
  </script>
</head>
<body>
  <div class="container">
  	<div class="row">
  	  <div class="col-md-12">
  	  	@yield('content')
  	  </div>	
  	</div>
  </div>	
  <script src="/js/app.js"></script>
</body>
</html>

