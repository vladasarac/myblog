@if($paginator->hasPages())
  <ul class="pager">
    {{-- link Previous Page --}}
  	@if($paginator->onFirstrPage())
  	  <li class="disabled">
  	  	<span class="glyphicon glyphicon-menu-left" aria-hidden="true">Previous</span>
  	  </li>
  	@else
  	  <li>
  	  	<a href="{{ $paginator->previousPageUrl() }}" rel="prev">
  	  	  <span class="glyphicon glyphicon-menu-left" aria-hidden="true">Previous</span>	
  	  	</a>
  	  </li>
  	@endif
  	{{-- Linkovi ka stranicama sa brojevima --}}
  	@foreach($elements as $element)
  	  @if(is_string($element))
  	    <li class="disabled">
  	      <span>{{ $element }}</span>	
  	    </li>
  	  @endif
  	  @if(is_array($element))
  	    @foreach($element as $page => $url)
  	      @if($page == $paginator->currentPage())
  	        <li class="active my-active">
  	          <span>{{ $page }}</span>	
  	        </li>
  	      @else
  	        <li>
  	          <a href="{{ $url }}">{{ $page }}</a>	
  	        </li>
  	      @endif
  	    @endforeach
  	  @endif
  	@endforeach
    {{-- link Next Page --}}
  	@if($paginator->hasMorePages())
  	  <li>
  	  	<a href="{{ $paginator->nextPageUrl() }}" rel="next">
  	  	  <span class="glyphicon glyphicon-menu-right" aria-hidden="true">Next</span>	
  	  	</a>
  	  </li>
  	@else
  	  <li class="disabled">
  	  	<span class="glyphicon glyphicon-menu-right" aria-hidden="true">Next</span>
  	  </li>
  	@endif
  </ul>
@endif






