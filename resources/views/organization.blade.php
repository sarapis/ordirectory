@extends('layout')

@section('styles')
@endsection

@section('content')

	@include('sub.navbar', ['fullwidth' => true])

	@if ($data['items'] ?? null)
	
		<div class="container-fluid mt-3" id="sGrid">
			<div class="row">
				<div class="col-7">
					<div class="orgDetails">
						<h2>{{ $org['name'] }}</h2>
						<p class="description85">{{ $org['descr'] }}</p>
						@if($org['url'])
							<p class="mb-0">url:&nbsp;&nbsp;<a href="{!! $org['url'] !!}" class="customized" target="_blank">{{ $org['url'] }}</a></p>
						@endif
							
						@if($org['email'])
							<p class="mb-0">email:&nbsp;&nbsp;<a href="mailto:{!! $org['email'] !!}" class="customized" target="_blank">{{ $org['email'] }}</a></p>
						@endif
					</div>

					@include('sub.pagination')
					
					@foreach ($tiles as $row)
					  <a href="/service/{{ $row['id'] }}" class="cardlink">
						<div class="card mb-3 py-3">
						  <div class="card-body">
							<h5 class="title">
								{{ $row['name'] }}
								  @if($row['organization'] ?? null)
									<small>by {{ $row['organization'] }}</small>
								  @endif
							</h5>
							<p class="descr">{{ $row['descr'] }}</p>
							@foreach ($row['locations'] as $loc)
								<p class="address">
									@if($loc['display_pin'])
										<img src="/img/markerR.png" height="16" width="16" class="mr-1">
								    @else
										<span class="mr-1">&bullet;</span>
									@endif
									{{ $loc['physical_address'] }}
									@if($loc['phones'])
										<i class="bi-telephone ml-3 mr-1"></i>
										{{ $loc['phones'] }}
									@endif
								</p>
							@endforeach
							@if ($row['categories'] ?? null)
								<p class="badges">
									Category: 
									@foreach((array)$row['categories'] ?? [] as $taxonomy)
										<span class="badge badge-info mr-1" title="{{ $taxonomy }}">{{ $taxonomy }}</span>
									@endforeach
								</p>
							@endif
							@if ($row['eligibility'] ?? null)
								<p class="badges">
									Eligibility: 
									@foreach((array)$row['eligibility'] ?? [] as $taxonomy)
										<span class="badge badge-info mr-1" title="{{ $taxonomy }}">{{ $taxonomy }}</span>
									@endforeach
								</p>
							@endif
						  </div>
						</div>
					  </a>
					@endforeach 

					@include('sub.pagination')
				</div>
				
					
				<div class="col-5">
				  @if($mapcenter['scale'] ?? null)
					<div class="sticky-top" style="height:99vh;" id="map">
						
						<style>
							.olPopup {border-radius:15px;}
							.olPopupContent {padding:15px; overflow: hidden !important;}
							.olPopupContent a {text-decoration:initial; color:#002b80;}
							.olPopup h2 {font-size:1em;}
							.olPopup p {font-size:0.75em;}
						</style>
						<script src="./OpenLayers/OpenLayers.min.js"></script>
						
						<script type="text/javascript">
							var map, layer;

							map = new OpenLayers.Map("map");
							var mapnik = new OpenLayers.Layer.OSM();
							map.addLayer(mapnik);
							var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
							var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
							
							var newl = new OpenLayers.Layer.Text( "text", { location:"./resources/markers_org.txt"} );
							map.addLayer(newl);

							map.setCenter(new OpenLayers.LonLat({{ $mapcenter['cLon'] }},{{ $mapcenter['cLat'] }}).transform(fromProjection, toProjection), {{ $mapcenter['scale'] }});
						</script>
					</div>
				  @else
					<div class="sticky-top" style="height:99vh;" id="basicMapPlaceholder">
						<p>Geocoordinates not available</p>
					</div>
				  @endif
				</div>
			</div>
		</div>
	@else
		<div class="container mt-4">
		  <div class="alert alert-warning" role="alert">
			0 records found
		  </div>
		</div>
	@endif


@endsection

@section('scripts')
@endsection