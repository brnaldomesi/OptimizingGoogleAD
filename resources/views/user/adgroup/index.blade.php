@extends('layouts.app')

@section('pageTitle')
Ad Groups
@endsection


@section('breadcrumbs')
{!! $breadcrumbs !!}
@endsection

@section('content')

<div class="row">  
    <div class="col-12">
      <div class="card">
        <div class="tab-container">
          <ul role="tablist" class="nav nav-tabs" id="adgroup-tabs">
            <li class="nav-item"><a href="#all" data-toggle="tab" role="tab" class="nav-link">All</a></li>
            
            <li class="nav-item"><a href="#too-few-ads" data-toggle="tab" role="tab" class="nav-link">Too few ads</a></li>
            
            <li class="nav-item"><a href="#has-winners" data-toggle="tab" role="tab" class="nav-link">Winners</a></li>

            <li class="nav-item"><a href="#too-many-ads" data-toggle="tab" role="tab" class="nav-link">Too many ads</a></li>
          </ul>

          <div class="tab-content">
            <div id="all" role="tabpanel" class="tab-pane active">
              {!! $adgroupsTable !!}
              
            </div>
            <div id="too-few-ads" role="tabpanel" class="tab-pane">
              {!! $adgroupsWithTooFewAdvertsTable !!}
            </div>

            <div id="has-winners" role="tabpanel" class="tab-pane">
              {!! $adgroupsWithWinnersTable !!}
            </div>

            <div id="too-many-ads" role="tabpanel" class="tab-pane">
              {!! $adgroupsWithTooManyAdvertsTable !!}
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
@section('javascript')
<script>
  $('#adgroup-tabs a[href="#{{$tab}}"]').tab("show");
</script>
@endsection