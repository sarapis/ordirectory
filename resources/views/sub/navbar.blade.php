@php
	use App\Custom\RequestMapper;
@endphp
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
