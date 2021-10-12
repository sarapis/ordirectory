@extends('layout')

@section('styles')
@endsection

@section('content')
	@include('sub.navbar', ['fullwidth' => false, 'req' => 'back'])
	
	<div class="container pl-4">
	  <div class="row">
		<div class="col-7 mt-2">
		  <div class="row">
			<div class="col">
				<h2>{{ $data['Service Name'] }}</h2>
				<h6>Organization: <a href="/organization?searchBy=OrganizationName&OrganizationName={{ ($data['Organization Name']) }}">{{ $data['Organization Name'] }}</a></h6>
				<p class="description85">{!! $data['Description'] !!}</p>
			</div>
		  </div>
		  <div class="row">
			<div class="col-{{ $data['Schedules'] ? 6 : 12 }} ml-0">
				@if($data['Phones'] ?? null)
					<p class="mb-0">tel:&nbsp;&nbsp;{{ implode(', ', (array)$data['Phones']) }}</p>
				@endif
					
				@if($data['Website'] ?? null)
					<p class="mb-0">url:&nbsp;&nbsp;<a href="{{ $data['Website'] }}">{{ $data['Website'] }}</a></p>
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
			@if($data['Schedules'] ?? null)
				<div class="col-6">
					<div class="card bg-light mb-3" style="max-width: 18rem;">
					  <div class="card-body">
						<p class="card-text">{!! $data['Schedules'] !!}</p>
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

		<div class="col-5">
		  <div class="row mt-3">
			<div class="col">
				@if($data['display_map'])
					<script src="/OpenLayers/OpenLayers.min.js"></script>
					<div id="basicMap"></div>
					<script>
						map = new OpenLayers.Map("basicMap");
						var mapnik         = new OpenLayers.Layer.OSM();
						var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
						var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
						{{--var position       = new OpenLayers.LonLat({{ is_array($data['Lng']) ? $data['Lng'][0] : $data['Lng'] }},{{ is_array($data['Lat']) ? $data['Lat'][0] : $data['Lat'] }}).transform( fromProjection, toProjection);--}}
						var zoom           = 13; 

						map.addLayer(mapnik);

						var size = new OpenLayers.Size(25,25);
						var offset = new OpenLayers.Pixel(-12,-25);
						var icon = new OpenLayers.Icon('/img/markerR.png',size,offset);
						var markers = new OpenLayers.Layer.Markers("Markers");
						map.addLayer(markers);
						@foreach($data['locations'] as $loc)
							markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat({{ $loc['longitude'] }},{{ $loc['latitude'] }}).transform( fromProjection, toProjection), icon));
						@endforeach

						//map.setCenter(position, zoom);
						map.setCenter(new OpenLayers.LonLat({{ $loc['longitude'] }},{{ $loc['latitude'] }}).transform( fromProjection, toProjection), zoom);
					</script>
				@else	
					<div id="basicMapPlaceholder">
						<p class="mb-0">Geocoordinates not available<br/>
							<small><a href="https://www.google.com.ua/maps/place/{{ (implode(', ', [$data['Address'],$data['City'],$data['State'],$data['Zip']])) }}">See on Google Maps</a></small>
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
						@if($loc['display_pin'])
							<img src="/img/markerR.png" height="16" width="16" class="mr-1">
						@endif
						{{ $loc['physical_address'] }}
						@if($loc['phones'])
							@if($loc['physical_address'] || $loc['display_pin'])
								<br/>
							@endif
							<i class="bi-telephone mr-1"></i>
							{{ $loc['phones'] }}
						@endif
					</p>
				@endforeach
			  @endif
			</div>
		  </div>
		
		</div>

	  </div>
	  
	</div>		
		
@endsection

@section('scripts')
@endsection
