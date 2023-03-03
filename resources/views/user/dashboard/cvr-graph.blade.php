<div class="card">
  <div class="card-body">
		@if($performanceChange->conversion_rate > 0)
        	<p class="text-success text-center dashboard-graph-metric">+ {{ $performanceChange->present()->conversionRate }}</p>
		@elseif($performanceChange->conversion_rate < 0)
			<p class="text-danger text-center dashboard-graph-metric">{{ $performanceChange->present()->conversionRate }}</p>
		@else
			<p class="text-center dashboard-graph-metric">{{ $performanceChange->present()->conversionRate }}</p>
		@endif
  
    	<p class="text-center dashboard-graph-name">CVR Increase
    	<p class="text-center dashboard-graph-baseline">Baseline {{ $performanceChange->present()->conversionRateBaseline}} 

    	<div  id="cvr-graph" class="dashboard-graph-holder"></div>   
  </div>
</div>