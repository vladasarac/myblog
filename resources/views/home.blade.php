@extends('layouts.app')

{{-- fajl prikazuje poslednjih 5 postova koje mu salje index() metod PostsControllera a yielduje ga layout app.blade.php --}}

@section('header')
  <div class="site-heading">
    <h1>MyBlog</h1>
    <hr class="small">
    <span class="subheading">A Clean Blog Theme by Start Bootstrap</span>
  </div>
@endsection

@section('content')

  {{--prikazi errore ako ih ima, ja dodo--}}
  @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- metod author() frontend PostsCntrollera vadi postove jednog autora i salje ih u ovaj vju i ovde prikazujemo ime tog autora --}}
  {{-- medjutim posto kad se iz index metoda poziva ovaj vju ne stize $author varijabla jer ionako u tom slucaju ne treba da stampa ime autora--}}
  @if(!empty($author)) {{--u author se nalazi zapravo id autora ali je to samo provera da vju zna koji metod ga je pozvao--}}
  {{--ovde stampamo ime autora prvog posta (posto ionako u ovom slucaju svi postove imaju istog autora)--}}
  @if(count($posts) <= 0)
    There is no post till now. Login and write a new post now!!!
  @else
    <h1 class="text-info">Postovi Autora {{ $posts[0]->author->name }}</h1><hr>
  @endif
    {{-- dodajemo da prikaze bio kolonu 'users' tabele tj biografiju autora cije postove gledamo, ovo se prikazuje samo ako kolona bio u -
       -'users' tabeli nije NULL tj ako je autoru dodata biografija--}}
    @if(!empty($posts[0]->author->bio)) 
      <div id="authorbio">
        {!! $posts[0]->author->bio !!}      
      </div>
    @endif
  @endif

  {{--ako index() metod PostsControllera nije nista vratio ispisi da nema postova--}}
  @if(count($posts) <= 0)
    There is no post till now. Login and write a new post now!!!
  @else
    {{--ako je nesto vratio prikazi te postove--}}
    @foreach($posts as $post)
      <h2 class="post-title">{{--naslov posta--}}
        <a href="{{ url('/'.$post->slug) }}">{{ $post->title }}</a>    
      </h2>
      <p class="post-subtitle">{{--prvih 120 karaktera body-a posta sa linkom Read More--}}
        {!! str_limit($post->body, $limit = 120, $end = '..... <a href='.url("/".$post->slug).'>Read More</a>') !!}    
      </p>
      <p class="post-meta">{{--datum kreiranja posta, sa imenom autora koji ide na rutu '/user'--}}
        {{ $post->created_at->format('M d,Y \a\t h:i a') }} By <a href="{{ url('/author/'.$post->author_id) }}">{{ $post->author->name }}</a>  
      </p>
    @endforeach   
  @endif
@endsection

{{--paginacija--}}
@section('pagination')
  <div class="row">
    <hr>
    {!! $posts->links() !!}    
  </div>
@endsection

