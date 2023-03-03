<div class="card">
  <div class="card-body">
    <h2>Best vs Worst</h2>
    <div class="tab-container">
      <ul role="tablist" class="nav nav-tabs">
            
        <li class="nav-item"><a href="#top-performer" data-toggle="tab" role="tab" class="nav-link active">Top Performer</a></li>
            
        <li class="nav-item"><a href="#worst-performer" data-toggle="tab" role="tab" class="nav-link">Worst Performer</a></li>
              
      </ul>

      <div class="tab-content">

        <div id="top-performer" role="tabpanel" class="tab-pane active">
          @isset($bestPerformer)

          <div class="d-flex flex-row">
              <div class="card w-25 m-1 dashboard-best-vs-worst-stats">
                <div class="card-header">
                  <h4>{{ $bestPerformer->present()->ctr }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">CTR
                </div>
              </div>
            
              <div class="card w-25 m-1 dashboard-best-vs-worst-stats">
                <div class="card-header">
                  <h4>{{ $bestPerformer->present()->conversionRate }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">CVR
                </div>
              </div>
            
              <div class="card w-25 m-1 dashboard-best-vs-worst-stats">
                <div class="card-header">
                  <h4>{{ $bestPerformer->present()->cpa }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">CPA
                </div>
              </div>
            
              <div class="card w-25 m-1 dashboard-best-vs-worst-stats">
                <div class="card-header">
                  <h4>{{ $bestPerformer->present()->roas }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">ROI
                </div>
              </div>
            
          </div>
            <p class="adwords-headline">{{ $bestPerformer->headline_1 }} | {{ $bestPerformer->headline_2 }}</p>
            <p class="adwords-url">{{ $bestPerformer->domain }}/{{ $bestPerformer->path_1 }}/{{ $bestPerformer->path_2 }}
            <p class="adwords-description">{{ $bestPerformer->description }}

            <p class="text-muted">Landing page: {{ $bestPerformer->final_urls[0] }}
          
          @else
            No data is available at present.
          @endisset

        </div>
        <div id="worst-performer" role="tabpanel" class="tab-pane">
        	@isset($worstPerformer)

            <div class="d-flex flex-row">
              <div class="card w-25 m-1 dashboard-best-vs-worst-stats">
                <div class="card-header">
                  <h4>{{ $worstPerformer->present()->ctr }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">CTR
                </div>
              </div>
            
              <div class="card w-25 m-1 dashboard-best-vs-worst-stats">
                <div class="card-header">
                  <h4>{{ $worstPerformer->present()->conversionRate }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">CVR
                </div>
              </div>
            
              <div class="card w-25 m-1 dashboard-best-vs-worst-stats">
                <div class="card-header">
                  <h4>{{ $worstPerformer->present()->cpa }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">CPA
                </div>
              </div>
            
              <div class="card w-25 m-1 dashboard-best-vs-worst-stats">
                <div class="card-header">
                  <h4>{{ $worstPerformer->present()->roas }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">ROI
                </div>
              </div>
          </div>
            
              <p class="adwords-headline">{{ $worstPerformer->headline_1 }} | {{ $worstPerformer->headline_2 }}</p>
              <p class="adwords-url">{{ $worstPerformer->domain }}/{{ $worstPerformer->path_1 }}/{{ $worstPerformer->path_2 }}
              <p class="adwords-description">{{ $worstPerformer->description }}

              <p class="text-muted">Landing page: {{ $worstPerformer->final_urls[0] }}

          @else
            No data is available at present.
          @endisset
        </div>
      </div>
    </div>
  </div>
</div>