<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $design['head']['title'] }}</title>

	<script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=61660f404564d200122a7def&product=sop'></script>
    <!-- Fonts -->
    <!--<link rel="shortcut icon" href="/img/fachclogo.png" type="image/png" />-->
    <link rel="shortcut icon" href="{!! $design['head']['icon'] !!}" type="image/{{ preg_match('~\.png$~', $design['head']['icon']) ? 'png' : 'x-icon'}}" />
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">


    <!-- Styles -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
	<link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('css/loader.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
	@foreach ($design['fonts'] as $font)
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family={{ $font }}">
	@endforeach
	@yield('styles')
	<style>
		#topmenu {background-color: #{{ $design['topmenu']['bck_color'] }}}
		#topmenu {color: #{{ $design['topmenu']['site_name_color'] }}}
		/*#topmenu a.top-link {color: #{{ $design['topmenu']['site_name_color'] }}}*/
		nav.navbar {background-color: #{{ $design['navbar']['bck_color'] }}}
		@foreach ($design['styles'] as $style)
			{{ $style['selector'] }} {{!! $style['style'] !!}}
		@endforeach

		@php
			$cols = [
				'link' => ['a.customized', '#bottommenu a', '.goog-te-gadget-simple .goog-te-menu-value span:before'],
				'taxonomy-link' => ['#taxonomy button', '#taxonomy a'],
				'pagination-link' => ['.page-link'],
				#'button' => ['button.customized'],
			];
			$bcks = [
				'pagination-link' => ['.page-item.active .page-link'],
				'button' => ['button.customized'],
				'tag' => ['.badge-info'],
				'service-card' => ['#sGrid .card'],
			];
		@endphp

		@foreach ($design['style-colors'] as $element=>$cc)
		  @foreach (explode(', ', $cc) as $i=>$c)
			@foreach ($cols[$element] ?? [] as $sel)
			  {{ $sel }}{{ $i ? ':hover' : '' }} {color:#{!! $c !!};}
			@endforeach
			@foreach ($bcks[$element] ?? [] as $sel)
			  {{ $sel }}{{ $i ? ':hover' : '' }} {background-color:#{!! $c !!};}
			  {{ $sel }}{{ $i ? ':hover' : '' }} {border-color:#{!! $c !!};}
			@endforeach
		  @endforeach
		@endforeach

	</style>
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<!--
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.9.0/css/ol.css">
	<script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.9.0/build/ol.js"></script>
-->
</head>

<body>
	<!-- Loader -->
	<div class="loading" style="display:none;">Loading&#8230;</div>
	<!-- /Loader -->

    <div id="app" class="overflow_responsive">
		@yield('content')
    </div>

	<div class="container">
	  <div class="row mt-4 mb-5 justify-content-center">
		<div class="col-11 py-4 text-muted" style="border-top: 1px solid #ccc; text-align:center;">
			{!! $design['footer']['text'] !!}
		</div>
	  </div>
	</div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
	<link href="https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.css" rel="stylesheet">
	<script src="https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.js"></script>
	<!-- Google translate -->
	<script type="text/javascript">
		function googleTranslateElementInit() {
			new google.translate.TranslateElement(
				{
					pageLanguage: 'en',
					includedLanguages: '{{ $design['navbar']['translation_languages'] }}',
					layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
					multilanguagePage: true
				},
				'google_translate_element'
			);
		}
	</script>
    <script src="{{ asset('js/script.js') }}" defer></script>
	<script type="text/javascript" src=
		"https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
	</script>
	@yield('scripts')
</body>
</html>
