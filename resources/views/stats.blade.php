@extends('layout')

@section('styles')
@endsection

@section('content')

	@include('sub.navbar', ['fullwidth' => false])

	<div class="container mt-4">
		<h4>Stats</h4>
		<table class="table">
		  <tbody>
			<tr>
			  <td>Organizations</td>
			  <td>{{ $data['orgs'] }}</td>
			</tr>
			<tr>
			  <td>Services</td>
			  <td>{{ $data['services'] }}</td>
			</tr>
			<tr>
			  <td>Locations</td>
			  <td>{{ $data['locs'] }}</td>
			</tr>
			<tr>
			  <td>Last Updated</td>
			  <td>{{ $data['last_updated_fmt'] }}</td>
			</tr>
		  </tbody>
		</table>
	</div>		
	
@endsection

@section('scripts')
@endsection
