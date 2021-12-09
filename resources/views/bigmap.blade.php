@extends('layout')

@section('styles')
@endsection

@section('content')
<div class="container-fluid mt-3" id="sGrid">
	<div class="row">
		<div class="col-12">
			<div class="sticky-top" style="height:99vh;" id="map">
				<script type="text/javascript">
					window.onload = function() {
						newMap()
						var geojson = {!! json_encode($geojson) !!}
						drawMarkers(geojson)
					}
				</script>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
@endsection
