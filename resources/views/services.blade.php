@extends('layout')

@section('styles')
@endsection

@section('content')

	@include('sub.navbar', ['fullwidth' => true])
	
	@if($data['items'] ?? null)
	
		<div class="container-fluid mt-3" id="sGrid">
			<div class="row">
				<div class="col-7">
					@include('sub.pagination')

					@foreach($tiles as $row)
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
				  @if($geojson)
					<div class="sticky-top" style="height:99vh;" id="map">
						<script type="text/javascript">
							window.onload = function() {
								newMap()
								var geojson = {!! json_encode($geojson) !!}
								drawMarkers(geojson)
							}
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
