@extends('layouts.app')

@section('head')
    <!-- Plotly.js -->
    <script src="{{ asset('assets/lib/plotly/plotly-latest.min.js') }}"></script>
@stop

@section('content')
@section('pageTitle')
{{ $account->name}}: Performance
<br><small>{{ $dateRange}}</small>
@endsection

<div class="row">

</div>
@isset($performanceChange)
<div class="row">

    <div class="col">
        @include('user.dashboard.ctr-graph')
    </div>
    <div class="col">
        @include('user.dashboard.cvr-graph')
    </div>
    <div class="col">
        @include('user.dashboard.cpa-graph')
    </div>
    <div class="col">
        @include('user.dashboard.roi-graph')
    </div>
</div>
@endisset
<div class="row">  
    <div class="col-md-5">
        @include('user.dashboard.quick-fixes')
    </div>
    <div class="col-md-7">
        @include('user.dashboard.potential-gains')
    </div>
</div>
<div class="row">  
    <div class="col-md-6">
    	@include('user.dashboard.recents')
    </div>
    <div class="col-md-6">
    	@include('user.dashboard.best-vs-worst')
    </div>
</div>

<div class="row">  
    <div class="col-md-6">
        @include('user.dashboard.kpi-tracker-graph')
    </div>
    <div class="col-md-6">
        @include('user.dashboard.budget-tracker-graph')
    </div>
</div>




@endsection
@section('javascript')
<script>
@isset($performanceChange)


    $("#ctr-graph").sparkline({{ $performanceChange->present()->ctrGraphDataCurrent }}
    ,
    {
        type: "line",
        fillColor: false,
        lineColor: "#34a853",
        width: "100%",
        height: "100%",
    }
  );
  $("#ctr-graph").sparkline(
    {{ $performanceChange->present()->ctrGraphDataPrevious }},
    {
        composite:true,
        fillColor:false,
        lineColor: "#4285f4",
        width: "100%",
        height: "100%"
    }
    );


    $("#cpa-graph").sparkline({{ $performanceChange->present()->cpaGraphDataCurrent }}
    ,
    {
        type: "line",
        fillColor: false,
        lineColor: "#34a853",
        width: "100%",
        height: "100%"
    }
  );

  $("#cpa-graph").sparkline(
    {{ $performanceChange->present()->cpaGraphDataPrevious }},
    {
        composite:true,
        fillColor:false,
        lineColor: "#4285f4",
        width: "100%",
        height: "100%"
    }
    );


    $("#cvr-graph").sparkline({{ $performanceChange->present()->conversionRateGraphDataCurrent }}
    ,
    {
        type: "line",
        fillColor: false,
        lineColor: "#34a853",
        width: "100%",
        height: "100%"
    }
  );
  $("#cvr-graph").sparkline(
    {{ $performanceChange->present()->conversionRateGraphDataPrevious }},
    {
        composite:true,
        fillColor:false,
        lineColor: "#4285f4",
        width: "100%",
        height: "100%"
    }
    );

  $("#roi-graph").sparkline({{ $performanceChange->present()->roasGraphDataCurrent }}
    ,
    {
        type: "line",
        fillColor: false,
        lineColor: "#34a853",
        width: "100%",
        height: "100%"
    }
  );
  $("#roi-graph").sparkline(
    {{ $performanceChange->present()->roasGraphDataPrevious }},
    {
        composite:true,
        fillColor:false,
        lineColor: "#4285f4",
        width: "100%",
        height: "100%"
    }
    );

@endisset
@isset($bestPerformer)
  $("#top-performer").click(function(){
    window.location.href = "{{ url('user/adverts/' . $bestPerformer->adgroup_id ) }}";
  });
@endisset

@isset($worstPerformer)
  $("#worst-performer").click(function(){
    window.location.href = "{{ url('user/adverts/' . $worstPerformer->adgroup_id ) }}";
  });
@endisset
</script>
@endsection