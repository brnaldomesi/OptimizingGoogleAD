<div class="card">
  <div class="card-body">
		@if($performanceChange->cpa > 0)
    	<p class="text-success text-center dashboard-graph-metric">+ {{ $performanceChange->present()->cpa}}</p>
			@elseif($performanceChange->cpa < 0)
			<p class="text-danger text-center dashboard-graph-metric">{{ $performanceChange->present()->cpa}}</p>
			@else
    	<p class="text-center dashboard-graph-metric">{{ $performanceChange->present()->cpa}}</p>
    @endif
  
    	<p class="text-center dashboard-graph-name">Cost Per Acquisition
    	<p class="text-center dashboard-graph-baseline">Baseline {{ $performanceChange->present()->cpaBaseline}} 

    	<div id="cpa-graph" class="dashboard-graph-holder"></div>

    
  </div>
</div>