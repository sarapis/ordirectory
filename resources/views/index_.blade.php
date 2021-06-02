@extends('layout')

@section('styles')
@endsection

@section('content')
	<main>
        <div class="top_header">
            <div class="container">
                @include('sub.overlaymenu')
            </div>
        </div>
        <div class="container main_area">
            <div class="row justify-content-start align-items-start">
                <div class="col-md-3 sidenav" id="mySidenav">
                    @include('sub.menu', ['active' => 'dashboard'])
                </div>
                <div class="col-md-9 rightside_area">
                    <h4>Dashboard</h4>
                    @foreach($cc as $c)
                        @if($c['type'] == 'Intro')
                            @if($c['link'])
                                <a href="{{ $c['link'] }}" target="_blank" class="link_blue">{{ $c['heading'] }}
                                </a>
                            @else
                                <a class="link_blue" href="javascript:void(0)">{{ $c['heading'] }}</a>
                            @endif
                            <p class="lead">{{ $c['content'] }} <small>{{ $c['date'] }}</small></p>
                        @endif
                    @endforeach

                    @if($ee)
						@if($stats)
							<div class="col-md-12 site_detaildata mb-4">
								<h5>Your Clinics ({{ array_sum($stats['clinics']) }} sites)</h5>
                                <table class="table table-sm ev_stats m-0 table_border">
                                    <thead class="grid_responsive">
                                        <tr>
                                            <th scope="col">Open</th>
                                            <th scope="col">Partially Open</th>
                                            <th scope="col">Suspended</th>
                                        </tr>
                                    </thead>
                                    <tbody class="grid_responsive">
                                        <tr>
                                            <th>{{ $stats['clinics']['fully'] }}</th>
                                            <th>{{ $stats['clinics']['partial'] }}</th>
                                            <th>{{ $stats['clinics']['suspended'] }}</th>
                                        </tr>
                                    </tbody>
									{{--
                                    <tbody>
                                        <tr class="bg_none">
                                            <th colspan="4">Sites Responded in last 48 hours: {{ $stats['clinics']['responded48'] }}</td>
                                        </tr>
                                    </tbody>
									--}}
                                </table>
							</div>
							<div class="col-md-12 site_detaildata mb-4">
								<h5>Your School Based Sites ({{ array_sum($stats['schools']) }} sites)</h5>
                                <table class="table table-sm ev_stats m-0 table_border">
                                    <thead class="grid_responsive">
                                        <tr>
                                            <th scope="col">Open</th>
                                            <th scope="col">Partially Open</th>
                                            <th scope="col">Suspended</th>
                                        </tr>
                                    </thead>
                                    <tbody class="grid_responsive">
                                        <tr>
                                            <th>{{ $stats['schools']['fully'] }}</th>
                                            <th>{{ $stats['schools']['partial'] }}</th>
                                            <th>{{ $stats['schools']['suspended'] }}</th>
                                        </tr>
                                    </tbody>
									{{--
                                    <tbody>
                                        <tr class="bg_none">
                                            <th colspan="4">Sites Responded in last 48 hours: {{ $stats['schools']['responded48'] }}</td>
                                        </tr>
                                    </tbody>
									--}}
                                </table>
							</div>
							<hr class="border_bottom"/>
						@endif

						@if($ee)
							<h4>Events</h4>

							@foreach($ee as $e)
								<div class="col-md-7 p-0">
									<h6 class="mb-0 float-left" style='width:100%'>
										@if($e['link'])
											<a href="{{ $e['link'] }}" target="_blank" class="link_blue">
												{{ $e['name'] }}
											</a>
										@else
											<p class="link_blue" style="font-size:16px;">{{ $e['name'] }}</p>
										@endif
										<p class="float-right start_enddate">{{ $e['start'] }}{{ $e['end'] ? ' - ' . $e['end'] : '' }}</p>
									</h6>
									<p>{{ $e['description'] }}</p>
								</div>
								<div class="clearfix"></div>
							@endforeach
						@endif
                    @endif

                    @php
                        $i = 0;
                    @endphp
                    @if(count($cc) > 1)
                        <hr class="border_bottom" style="float: left;width: 100%;"/>
                        <h4>Updates</h4>
                        @foreach($cc as $c)
                            @if($c['type'] == 'Update')
                                <div class="col-md-7 p-0">
                                    <h6 class="mb-0  float-left" style='width:100%'>
                                        @if($c['link'])
                                            <a href="{{ $c['link'] }}" target="_blank" class="link_blue">
                                                {{ $c['heading'] }}
                                            </a>
                                        @else
                                            <p class=" float-left">{{ $c['heading'] }}</p>
                                        @endif
                                        <p class="float-right">{{ $c['date'] }}</p>
                                    </h6>
                                    <p>{{ $c['content'] }}</p>
                                </div>
                                @php
                                    $i += 1;
                                @endphp
                            @endif
                        @endforeach
                    @endif

                    @if(count($cc) > (1 + $i))
                        <hr class="border_bottom"/>
                        <h4>Resources</h4>
                        @foreach($cc as $c)
                            @if($c['type'] == 'Resources')
                                <div class="col-md-7 p-0">
                                    <h6 class="mb-0  float-left" style='width:100%'>
                                        @if($c['link'])
                                            <a href="{{ $c['link'] }}" target="_blank"  class="link_blue">
                                                {{ $c['heading'] }}
                                            </a>
                                        @else
                                            <p class=" float-left">{{ $c['heading'] }}</p>
                                        @endif
                                        <p class="float-right">{{ $c['date'] }}</p>
                                    </h6>
                                    <p>{{ $c['content'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
	</main>
@endsection

@section('scripts')
@endsection
