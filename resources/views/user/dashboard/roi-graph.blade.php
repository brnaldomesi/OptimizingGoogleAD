<div class="card">
  <div class="card-body">
		@if($performanceChange->roas > 0)
        	<p class="text-success text-center dashboard-graph-metric">+ {{ $performanceChange->present()->roas }}</p>
        @elseif($performanceChange->roas < 0)
					<p class="text-danger text-center dashboard-graph-metric">{{ $performanceChange->present()->roas }}</p>
				@else
        	<p class="text-center dashboard-graph-metric">{{ $performanceChange->present()->roas }}</p>
        @endif
  
    	<p class="text-center dashboard-graph-name">Return on Investment
    	<p class="text-center dashboard-graph-baseline">Baseline {{ $performanceChange->present()->roasBaseline }}

    	<div id="roi-graph" class="dashboard-graph-holder"></div>  
  </div>
</div>