@php
	use App\Custom\RequestMapper;
@endphp
	@if ($design['topmenu']['turned_on'])
	  <div class="container{{ $fullwidth ? '-fluid' : '' }}">
		<div id="topmenu">
			<div class="flexmenu-group-left">
				<img src="{{ $design['topmenu']['logo_url'] }}" class="top-logo" />
				<h2>{{ $design['topmenu']['site_name'] }}</h2>
			</div>
			<div class="flexmenu-group-right">
				@foreach ($design['topmenu']['links'] as $link)
					<a class="top-link" href="{{ $link['url'] }}">{{ $link['text'] }}</a>
				@endforeach
			</div>
		</div>
	  </div>
	@endif

	<div class="container{{ $fullwidth ? '-fluid language_link' : '' }}">
		<nav class="navbar navbar-expand-lg" id="bottommenu">
			<div class="flexmenu-group-left">
				@if ($req && $req == 'back')
					<a class="nav-link pl-0" onclick="window.history.back();" href="#">&laquo; Back to search results</a>
				@elseif ($req && $req <> 'back')
					<a class="nav-link" href="{{ route('index') }}">&laquo; Back to search form</a>
					<a class="nav-link">{!! RequestMapper::titleEnc($req, true) !!}</a>
				@endif
			</div>
			<div class="flexmenu-group-right">
				<div class="nav-link dropdown">
				  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="bi-share" style="margin-right:.4rem;"></i>Share
				  </a>

				  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
					<div class="st-custom-button dropdown-item" data-network="facebook"><i class="bi-facebook" style="margin-right:.4rem;"></i>share</div>
					<div class="st-custom-button dropdown-item" data-network="twitter"><i class="bi-twitter" style="margin-right:.4rem;"></i>tweet</div>
					<div class="st-custom-button dropdown-item" data-network="pinterest"><i class="fab fa-pinterest-p" style="margin-right:.4rem;"></i>pin</div>
					<div class="st-custom-button dropdown-item" data-network="email"><i class="bi-envelope-fill" style="margin-right:.4rem;"></i>email</div>
					<div class="st-custom-button dropdown-item" data-network="linkedin"><i class="bi-linkedin" style="margin-right:.4rem;"></i>linkedin</div>
					<div class="st-custom-button dropdown-item" data-network="sharethis"><i class="bi-share-fill" style="margin-right:.4rem;"></i>sharethis</div>
				  </div>
				</div>
				@if ($csvLink ?? null)
					<div class="nav-link dropdown">
					  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="bi-download" style="margin-right:.4rem;"></i>Download
					  </a>

					  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
						<a class="dropdown-item" href="{!! $csvLink !!}"><i class="fas fa-file-csv" style="margin-right:.4rem;"></i>Download CSV</a>
						<a class="dropdown-item" href="{!! $pdfLink !!}"><i class="fas fa-file-pdf" style="margin-right:.4rem;"></i>Download PDF</a>
					  </div>
					</div>
				@endif
				<a class="nav-link " id="google_translate_element"></a>
			</div>
		</nav>
	</div>

