@extends('layout')

@section('styles')
@endsection

@section('content')
	@include('sub.navbar', ['fullwidth' => false, 'req' => 'back'])
	
	<div class="container pl-4">
	  <div class="row">
	  @if($geojson || ($data['locations'] ?? null))
		<div class="col-7 mt-2">
	  @else
		<div class="col-12 mt-2">
	  @endif
		  <div class="row">
			<div class="col">
				<h2>{{ $data['Service Name'] }}</h2>
				<h6>Organization: <a href="/organization?searchBy=OrganizationName&OrganizationName={{ ($data['Organization Name']) }}" class="customized">{{ $data['Organization Name'] }}</a></h6>
				<p class="description85">{!! $data['Description'] !!}</p>
			</div>
		  </div>
		  <div class="row">
			<div class="col-{{ $data['regular_schedule'] ? 6 : 12 }} ml-0">
				@if($data['Phones'] ?? null)
					<p class="mb-0">tel:&nbsp;&nbsp;{{ implode(', ', (array)$data['Phones']) }}</p>
				@endif
					
				@if($data['Website'] ?? null)
					<p class="mb-0">url:&nbsp;&nbsp;<a href="{{ $data['Website'] }}" class="customized" target="_blank">{{ $data['Website'] }}</a></p>
				@endif
				
				@if($data['Languages'] ?? null)
					<p class="mb-3">languages:&nbsp;&nbsp;{{ implode(', ', (array)$data['Languages']) }}</p>
				@endif
				
				@if($data['Category'] ?? null)
				  <p class="mt-3 mb-3">
					<span style="font-size: 1.25rem; margin-right: 16px; min-width: 70px; display: inline-block;">Category:</span>
					@foreach ((array)$data['Category'] as $taxonomyItem)
						<a href="/services?searchBy=TaxonomyName&strict=true&family=true&TaxonomyName={{ $taxonomyItem }}" class="badge badge-info mr-1" title="{{ $taxonomyItem }}">
							{{ strlen($taxonomyItem) > 25 ? substr($taxonomyItem, 0, 22) . '...' : $taxonomyItem }}
						</a>
					@endforeach
				  </p>
				@endif
				@if($data['Eligibility'] ?? null)
				  <p class="mt-3 mb-3">
					<span style="font-size: 1.25rem; margin-right: 16px; min-width: 70px; display: inline-block;">Eligibility:</span>
					@foreach ((array)$data['Eligibility'] as $taxonomyItem)
						<a href="/services?searchBy=TaxonomyName&strict=true&family=true&TaxonomyName={{ $taxonomyItem }}" class="badge badge-info mr-1" title="{{ $taxonomyItem }}">
							{{ strlen($taxonomyItem) > 25 ? substr($taxonomyItem, 0, 22) . '...' : $taxonomyItem }}
						</a>
					@endforeach
				  </p>
				@endif
			</div>
			@if($data['regular_schedule'] ?? null)
				<div class="col-6">
					<div class="card bg-light mb-3" style="max-width: 18rem;">
					  <div class="card-body">
						<p class="card-text">{!! $data['regular_schedule'] !!}</p>
					  </div>
					</div>
				</div>
			@endif
		  </div>
		  
		  @if($data['Details'])
			<div class="row">
			<div class="col-12">
			<h5 class="mt-3 mb-3">Details</h5>
				@foreach ((array)$data['Details'] as $detType=>$detText)
				  <p>
					<h6 style="display: inline-block;">{{ $detType }}:</h6>&nbsp;&nbsp;{{ $detText }}
				  </p>
				@endforeach
				
			</div>
			</div>
		  @endif
			
		</div>

		@if($geojson || ($data['locations'] ?? null))
		  <div class="col-5">
		    <div class="row mt-3">
			  <div class="col">
				@if($geojson)
					<div class="sticky-top" style="height:49vh; position: -webkit-sticky; position: sticky;" id="map">
						<script type="text/javascript">
							window.onload = function() {
								newMap()
								var geojson = {!! json_encode($geojson) !!}
								drawMarkers(geojson)
							}
						</script>
					</div>
				@else	
					<div id="basicMapPlaceholder">
						<p class="mb-0">Geocoordinates not available<br/>
							@foreach ($data['locations'] ?? [] as $loc)
								@if ($loc['physical_address'])
									<small><a href="https://www.google.com.ua/maps/place/{{ $loc['physical_address'] }}">See on Google Maps</a></small>
									@php
										break;
									@endphp
								@endif
							@endforeach
						</p>
					</div>
				@endif	
			  </div>
		    </div>
		  
		  
		    <div class="row mt-3 py-2">
			  <div class="col">
			  @if($data['locations'] ?? null)
				<h6>Location</h6>
				@foreach ($data['locations'] ?? [] as $loc)
					<h5 class="mt-1">{{ $loc['name'] }}</h5>
					<p class="address">
						@if($loc['display_pin'] ?? null)
							<img src="/img/markerR.png" height="16" width="16" class="mr-1">
						@endif
						{{ $loc['physical_address']  ?? ''}}
						@if($loc['physical_address'] ?? null || $loc['display_pin'] ?? null)
							<br/>
						@endif
						@if($loc['phones'] ?? null)
							<i class="bi-telephone mr-1"></i>
							{{ $loc['phones'] }}
							<br/>
						@endif
						@if($loc['regular_schedule'] ?? null)
							<i class="bi-clock mr-1"></i>
							<span class="p-0" style="inline-block">
								{!! $loc['regular_schedule'] !!}
							</span>
						@endif
					</p>
				@endforeach
			  @endif
			  </div>
		    </div>
		  </div>
		@endif

	  </div>
	  
	</div>		
		
@endsection

@section('scripts')
@endsection
