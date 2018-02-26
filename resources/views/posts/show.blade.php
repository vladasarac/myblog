@extends('layouts.app')

{{--vju koji prikazuje jedan post i dodate komentare--}}

@section('header')
  <div class="post-heading">
    <h1>{{ $post->title }}</h1> {{--naslov posta--}}
    <h2 class="subheading">{{ $post->description }}</h2> {{--podnaslov tj description--}}  
  </div>
@endsection

@section('content')
  @if($post)  {{-- ako ima trazenog posta --}}
    <div class="">
      {{--datum kreiranja posta i podatci autora koji su link ka ruti 'author' koja gadja author() metod frontend PostsControllera koji prikazuje sve tekstove jednog autora--}}
      <span class="meta">
        {{ $post->created_at->format('M d,Y \a\t h:i a') }} By 
        <a href="{{ url('/author/'.$post->author_id) }}">
          <b>{{ $post->author->name }}</b>
          <img src="{{ asset('img/authors/' . $post->author->image) }}" class="img-circle" id="showimages" height="50" width="50"><br><br>
        </a>  
      </span>
      {!! $post->body !!}	
    </div>
    {{--ako user nije ulogovan ne moze komentarisati post, mora biti ulogovan (barem kao subscriber)--}}
    @if(Auth::guest())
 	  <p>
 	  	Please Login to Comment
 	  </p>
    @else
      {{--ako je ulogovan moze komentarisati--}}
      <div class="">
        <h3>Leave a Comment</h3>	
      </div>
      <div class="panel-body">
        {{--forma za dodavanje komentara--}}
      	<form class="" action="/comment/add" method="post">
      	  {{--skrivena polja u kojima su _token, id posta i slug posta(???)--}}
      	  <input type="hidden" name="_token" value="{{ csrf_token() }}">  
      	  <input type="hidden" name="on_post" value="{{ $post->id }}">  
      	  <input type="hidden" name="slug" value="{{ $post->slug }}">  	
      	  <div class="form-group">
      	    {{--text aerea u koju se upisuje komentar--}}
      	  	<textarea required="required" placeholder="Enter comment here" name="body" class="form-control"></textarea>
      	  </div>
      	  <input type="submit" name="post_comment" class="btn btn-success" value="Post">
      	</form>
      </div>
    @endif
    <div class="">{{--ako ima komentara prikazi ih--}}
      @if($comments)
        <ul style="list-style: none; padding: 0">
          @foreach($comments as $comment)
            <li class="panel-body">
              <div class="list-group">
              	<div class="list-group-item">
              	  <h3>{{ $comment->author->name }}</h3>
              	  <p>{{ $comment->created_at->format('M d,Y \a\t h:i a') }}</p>	
              	</div>
              	<div class="list-group-item">
              	  <p>{{ $comment->body }}</p>	
                  
                  {{--btn tj ikone za Like ili Dislike komentara, prvo prikazuje koliko komentar vec ima lajkova i dislajkova a onda proverava
                  da li postoji cookie likeordislikeIDKOMENTARA i ako ima to znaci da je vec sa kompjutera lajkovan ili dislajkovan komentar -
                  -(to pise u kukiju 'like' ili 'dislike') a ako nije prikazuje ikone za like i dislike ciji su hendleri u likedislike.js--}}
                  <div id="like_dislike">
                    {{-- <p hidden="true" id="{{ $comment->id }}"></p> --}}
                    {{--prikaz broja lajkova i dislajkova komentara(kolone like i dislike 'comments' tabele),spanovi sa  id likes+IDKOMENTARA i 
                    id dislikes+IDKOMENTARA sluze da bi u likedislike.js mogli da promenimo broj trenutnih lajkova i dislajkova komentara kad
                    korisnik klikne neku ikonu--}}
                    <span class="text-success">Likes (<span id="likes{{$comment->id}}">{{ $comment->like }}</span>)</span>
                    <span class="text-danger pull-right">Dislikes (<span id="dislikes{{$comment->id}}">{{ $comment->dislike }}</span>)</span><br>
                    {{--kad korisnik lajkuje ili dislajkuje komentar i kad kontroler vrati odgovor manjamo sadrzaj ovog diva u likedislike.js
                    obrisacemo ikone i ako je lajkovao ispisati Liked ili ako je dislajkovao Disliked--}}
                    <div id="likeordislike{{ $comment->id }}">
                      {{--ako postooji cookie likeordislikeIDKOMENTARA i njegova vrednost je like--}}
                      @if (Cookie::get('likeordislike'.$comment->id) == 'like') 
                        <p class="likeilidislike text-success text-center">Liked</p>{{--prikazi tekst Liked--}}
                      {{--ako postooji cookie likeordislikeIDKOMENTARA i njegova vrednost je dislike--}}
                      @elseif (Cookie::get('likeordislike'.$comment->id) == 'dislike')
                        <p class="likeilidislike text-danger text-center">Disliked</p>{{--prikazi tekst Disliked--}}
                      @else{{--ako ne postooji cookie likeordislikeIDKOMENTARA prikazujemo ikone za Like i Dislike ispod komentara--}}
                        <span commentid="{{ $comment->id }}" likeordislike="like" class="likedislikebtn"><img alt="brand" src="{{ asset('img/like.png') }}" width="50" height="50"></span>
                        <span commentid="{{ $comment->id }}" likeordislike="dislike" class="likedislikebtn pull-right"><img alt="brand" src="{{ asset('img/dislike_red.png') }}"  width="50" height="50"></span>
                      @endif 
                    </div>
                  </div>

              	</div>
              </div>	
            </li>
          @endforeach	
        </ul>
      @endif	
    </div>
  @else {{--ako nije pronadjen post ???--}}

  @endif
  {{-- u app.blade.php layoutu je dodata <style> u kom pise da je sirina <iframe> 100% a ovde mu racunamo visinu tako sto podelimo tu vis-
  inu sa 1.73 i taj broj damo kao visinu <iframe> - a --}}
  <script type="text/javascript">
    $(document).ready(function(){
      var ifwidth = $('iframe').width();
      $("iframe").height(ifwidth / 1.73);
    }); 

    //ovo nam treba za lajkovanje i dislajkovanje komentara tj ove varijable koristi likedislike.js kad salje AJAX u likeordislike()CommentsControllera
    var token = '{{ Session::token() }}';
    var likeordislikeurl = '{{ route('likeordislike') }}';
  </script>
@endsection

