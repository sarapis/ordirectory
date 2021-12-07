@extends('layout')

@section('styles')
@endsection

@section('content')

	@include('sub.navbar', ['fullwidth' => false])
	
	<div class="container-fluid">
	  <div class="row mt-3 mb-5">
		<div class="col-8 mx-auto">
		<h1>{{ $design['actionbar']['main_text'] }}</h1>
		  <form action="{{ route('services') }}" method="GET">
			  <div class="row mt-5">
			  {{--
				<div class="col-6">
				    <input type="text" class="form-control" aria-label="Text input with checkbox" placeholder="Service Name" name="ServiceName">
				</div>
				<div class="col-6">
					<input type="text" class="form-control" aria-label="Text input with radio button" placeholder="... or Organization Name" name="OrganizationName">
				</div>
			  --}}
				<div class="col-12 namesearch-outer">
					<input type="text" class="form-control" aria-label="Text input with checkbox" placeholder="Search for resources" name="NameSearch" id="namesearch">
					<div class="reset-button">
						<button class="inactive" onclick="val_reset();" type="button">
							<i class="bi bi-x-square"></i>
						</button>
					</div>
				</div>
				<div class="col-6">
					<div class="input-group my-4 ">
						<button style="min-width:8em;" type="submit" class="btn btn-primary customized">{{ $design['actionbar']['submit_button_text'] }}</button>
							{{--<button style="min-width:8em;"  type="reset" class="btn btn-light ml-2" onclick="document.location.assign('search.php');">Reset</button>--}}
					</div>
				</div>
			  </div>
		  </form>
	    </div>
	  </div>
	</div>
	
	<div class="container-fluid">
	  <div class="row justify-content-center">
		<div class="col-8">
		  <div class="row justify-content-center">
		    <div class="col mx-auto">
			  <h4 class="pt-5 mb-5" style="border-top: 1px solid #ccc; text-align:center;">Browse by Category</h4>
		    </div>
		  </div>
		  <div id="taxonomy">
			  <div class="row">
				  @foreach ($data as $col)
					<div class="col-6">
					  @foreach ($col['items'] as $name=>$card)
						<div class="card mb-2">
							<div class="card-header" id="card-{{ $card['code'] }}">
							  <h5 class="mb-0">
								<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-{{ $card['code'] }}" aria-expanded="false" aria-controls="collapse-{{ $card['code'] }}">
								  +
								</button>
								<a href="{{ preg_replace('~.php~', '', $card['url']) }}" class="btn btn-link collapsed">{{ $card['name'] }}</a>
							  </h5>
							</div>

							<div id="collapse-{{ $card['code'] }}" class="collapse" aria-labelledby="card-{{ $card['code'] }}" data-parent="#taxonomy">
							  <div class="card-body">
								@foreach ($card['items'] as $item)
									@if($item['items'])
										<p>
										  <a data-toggle="collapse" href="#collapse-{{ $item['code'] }}" role="button" aria-expanded="false" aria-controls="collapse-{{ $item['code'] }}">
											<b class="fa"></b>&nbsp;
										  </a>
										  <a href="{{ preg_replace('~.php~', '', $item['url']) }}" class="btn btn-link collapsed">{{ $item['name'] }}</a>
										</p>
										<div class="collapse" id="collapse-{{ $item['code'] }}">
										  <div class="card card-body">
											@foreach ($item['items'] as $item2)
												@if($item2['items'])
													<p>
													  <a data-toggle="collapse" href="#collapse-{{ $item2['code'] }}" role="button" aria-expanded="false" aria-controls="collapse-{{ $item2['code'] }}">
														<b class="fa"></b>&nbsp;
													  </a>
													  <a href="{{ preg_replace('~.php~', '', $item2['url']) }}" class="btn btn-link collapsed">{{ $item2['name'] }}</a>
													</p>
													<div class="collapse" id="collapse-{{ $item2['code'] }}">
													  <div class="card card-body">
														@foreach ($item2['items'] as $item2)
															<p>—&nbsp;<a href="{{ preg_replace('~.php~', '', $item2['url']) }}" class="pl-2">{{ $item2['name'] }}</a></p>
														@endforeach
													  </div>
													</div>
												@else
													<p>—&nbsp;<a href="{{ preg_replace('~.php~', '', $item2['url']) }}" class="pl-1">{{$item2['name']}}</a></p>
												@endif

												{{--<p>—&nbsp;<a href="{{ preg_replace('~.php~', '', $item2['url']) }}" class="pl-1">{{ $item2['name'] }}</a></p>--}}
											@endforeach
										  </div>
										</div>
									@else
										<p>—&nbsp;<a href="{{ preg_replace('~.php~', '', $item['url']) }}" class="pl-1">{{$item['name']}}</a></p>
									@endif
								@endforeach
							  </div>
							</div>
						</div>
					  @endforeach
					</div>
				  @endforeach
			  </div>
		  </div>
	    </div>
	  </div>
	</div>
	
	
@endsection

@section('scripts')
	<script src="https://typeahead.js.org/releases/latest/typeahead.bundle.js"></script>
	<script src="{{ asset('js/search.js') }}"></script>
@endsection
