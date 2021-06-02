@extends('layout')

@section('styles')
@endsection

@section('content')

	@include('sub.navbar', ['fullwidth' => false])

	<div class="container mt-4">
	  <div class="alert alert-danger" role="alert">
		{{ $alert }}
	  </div>
	</div>		
	
	@include('sub.actualDateNotification')

@endsection

@section('scripts')
@endsection
