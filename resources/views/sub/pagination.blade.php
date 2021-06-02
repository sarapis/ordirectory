	@php
		use App\Custom\RequestMapper;

		$num = $data['page'];
		$size = $data['per_page'];
		$totalItems = $data['total_items'];
		$isFirst = $num == 1;
		$total = $size ? ceil($totalItems / $size) : 0;
		$isLast = $num == $total;
		$shorten = $total >= 10;
		$min = 1 + $size * ($num - 1);
		$max = $size * $num;
	@endphp

	@if ($total > 1)

	  <div class="row">
		<div class="col-3">
			Results {{ $min }} - {{ $max }} of {{ $totalItems }}
		</div> 
		<div class="col-9">
			<nav aria-label="Page navigation">
			  <ul class="pagination justify-content-end">
				@if ($isFirst)
				  <li class="page-item disabled">
				    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
				@else
				  <li class="page-item">
				    <a class="page-link" href="/services?{!! RequestMapper::getEnc($req, ['page' => $num - 1]) !!}">
				@endif
					  Previous
				  </a>
				</li>
			  
				@for($i=1; $i<=$total; $i++)
				  @if($shorten && $i>2 && $i<$num-2)
				    <li class="page-item disabled">
				      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
					    ...
				      </a>
					</li>
					@php
						$i = $num - 3;
					@endphp
				  @elseif($shorten && $i>$num+2 && $i<$total-1)
				    <li class="page-item disabled">
				      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
					    ...
				      </a>
					</li>
					@php
						$i = $total - 2;
					@endphp
				  @elseif($num == $i)
					<li class="page-item active"><a class="page-link" href="#">{{ $i }}</a></li>
				  @else
					<li class="page-item">
					  <a class="page-link" href="/services?{!! RequestMapper::getEnc($req, ['page' => $i]) !!}">
						{{ $i }}
					  </a>
					</li>
				  @endif
				  
				@endfor  
				
				@if ($isLast)
				  <li class="page-item disabled">
				    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
				@else	  
				  <li class="page-item">
				    <a class="page-link" href="/services?{!! RequestMapper::getEnc($req, ['page' => $num + 1]) !!}">
				@endif
					  Next
				  </a>
				</li>
			  </ul>
			</nav>
	    </div>	
	  </div>	
	@endif
