@php
	use App\Custom\RequestMapper;
@endphp
	@if ($design['topmenu']['turned_on'])
	  <div class="container{{ $fullwidth ? '-fluid' : '' }}">
		<div id="topmenu">
			<div id="top-title">
				<img src="{{ $design['topmenu']['logo_url'] }}" class="top-logo" />
				<h2>{{ $design['topmenu']['site_name'] }}</h2>
			</div>
			<div id="top-links">
				@foreach ($design['topmenu']['links'] as $link)
					<a class="top-link" href="{{ $link['url'] }}">{{ $link['text'] }}</a>
				@endforeach
			</div>
		</div>
	  </div>
	@endif

	<div class="container{{ $fullwidth ? '-fluid language_link' : '' }}">
		<nav class="navbar navbar-expand-lg">
			@if ($req && $req == 'back')
				<a class="nav-link" onclick="window.history.back();" href="#">&laquo; Back to search results</a>
			@elseif ($req && $req <> 'back')
				<a class="nav-link" href="{{ route('index') }}">&laquo; Back to search form</a>
				<a class="nav-link">{!! RequestMapper::titleEnc($req, true) !!}</a>
				<a class="nav-link white_space">&nbsp;</a>
			@else
				<a class="nav-link white_space">&nbsp;</a>
			@endif
		    <a class="nav-link " id="google_translate_element"></a>
		</nav>
	</div>

