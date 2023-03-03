<div class="card">
	<div class="card-body">
        @if($performanceChange->ctr > 0)
        	<p class="text-success text-center dashboard-graph-metric">+ {{ $performanceChange->present()->ctr}}</p>
        @elseif($performanceChange->ctr < 0)
            <p class="text-danger text-center dashboard-graph-metric">{{ $performanceChange->present()->ctr}}</p>
        @else
            <p class="text-center dashboard-graph-metric">{{ $performanceChange->present()->ctr}}</p>
        @endif
  
    	<p class="text-center dashboard-graph-name">CTR Improvement
    	<p class="text-center dashboard-graph-baseline">Baseline {{ $performanceChange->present()->ctrBaseline }}
        <div id="ctr-graph" class="dashboard-graph-holder">
        </div>
  </div>
</div>