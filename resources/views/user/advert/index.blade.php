@extends('layouts.app')

@section('pageTitle')
Adverts
@endsection

@section('breadcrumbs')
{!! $breadcrumbs !!}
@endsection

@section('content')

<div class="row collapse" id="create-advert">  
    <div class="col-12">
    	<div class="card">
    		<div class="card-body">
      			@include('user.advert.create')
      		</div>  
    	</div>
  	</div>
</div>
<div class="row">  
    <div class="col-12">
    	<div class="card">
    	<div class="card-body">
    		<button data-toggle="collapse" data-target="#create-advert" type="button" class="btn btn-space btn-primary">New Advert</button>
      		<table class="table table-hover table-fw-widget table-responsive datatable">
				<thead>
					<tr>
						<th>Status</th>
						<th>Campaign</th>
						<th>Ad Group</th>
						<th>Ad Copy</th>
		        <th>Impressions</th>
		        <th>Clicks</th>
		        <th>Avg. Position</th>
		        <th>Cost</th>
		        <th>Avg. CPC</th>
		        <th>Conversions</th>
		        <th>Conv. Value</th>
		        <th>CPA</th>
		        <th>ROAS</th>
		        <th>Conv. Rate</th>
		        <th>Ctr</th>

					</tr>
				</thead>
				<tbody>
					@foreach($adverts as $advert)
					<tr>
						<td>
							<div class="btn-group btn-space">
								
								<button type="button" data-toggle="dropdown" class="btn btn-secondary dropdown-toggle" id="status-button-{{ $advert->id }}">
									
									
									@if($advert->status == 'enabled')
										<i class="fas fa-circle adwords-enabled"></i>
									@else
										<i class="fas fa-pause-circle adwords-paused"></i>
									@endif
									 <span class="icon-dropdown mdi mdi-chevron-down"></span>

								</button>

    					<div role="menu" class="dropdown-menu">
    						@if($advert->status == 'enabled')
								
									<a class="dropdown-item text-muted" data-scope="advert" data-id="{{ $advert->id }}" id="enable-link-{{ $advert->id }}"><i class="fas fa-circle adwords-enabled"></i> Enable</a>

										<a class="dropdown-item pause-adverts" data-scope="advert" data-id="{{ $advert->id }}" id="pause-link-{{ $advert->id }}"> <i class="fas fa-pause-circle adwords-paused"></i> Pause</a>
								
								@else
										<a class="dropdown-item enable-adverts" data-scope="advert" data-id="{{ $advert->id }}" id="enable-link-{{ $advert->id }}"><i class="fas fa-circle adwords-enabled"></i> Enable</a>

										<a class="dropdown-item text-muted" data-scope="advert" data-id="{{ $advert->id }}"  id="pause-link-{{ $advert->id }}">  <i class="fas fa-pause-circle adwords-paused"> Pause</a>

									@endif
    						
    					</div>
						</div>
							
						</td>
						<td>
							<a href="{{ url('user/adgroups/campaign/' . $advert->adgroup->campaign->id) }}">{{ $advert->adgroup->campaign->name }}
							</td>
						<td><a href="{{ url('user/adverts/' . $advert->adgroup->id) }}">{{ $advert->adgroup->name }}</a></td>

						@if(array_key_exists(0, $advert->final_urls))
							<td onclick="window.open('{{url($advert->final_urls[0]) }}')">
						@else
							<td>
						@endif
						
							<span class="adwords-headline">
							{{ $advert->headline_1 }}</span>
							<span class="adwords-headline">
							{{ $advert->headline_2 }}</span><br>
							<span class="adwords-description">{{ $advert->description }}</span>
							<br>
							<span class="adwords-url">
							{{ $advert->domain }}
							@if($advert->path_1)
								{{ $advert->path_1}}/{{ $advert->path_2}}
							@endif()
							</span>

						</td>
						<td class="text-right">
							{{ $advert->performance->present()->impressions }}
						</td>
						<td class="text-right">{{ $advert->performance->present()->clicks }}</td>
						<td class="text-right">{{ $advert->performance->present()->averagePosition }}</td>
						<td class="text-right">{{ $advert->performance->present()->cost }}</td>
						<td class="text-right">{{ $advert->performance->present()->averageCpc }}</td>
						<td class="text-right">{{ $advert->performance->present()->conversions }}</td>
						<td class="text-right">{{ $advert->performance->present()->conversionValue }}</td>
						<td class="text-right">{{ $advert->performance->present()->cpa }}</td>
						<td class="text-right">{{ $advert->performance->present()->roas }}</td>
						
						@if($advert->performance->conversion_rate_message == 'winning')
							
							<td class="text-right bg-success" data-toggle="tooltip" title="Based on 30 days data with a Statistical Significance of {{ $advert->performance->present()->conversionRateSignificance }}">
								
						@elseif($advert->performance->conversion_rate_message == 'losing')
								
								<td class="text-right bg-danger">

						@else
							
							<td class="text-right">
							
						@endif
							{{ $advert->performance->present()->conversionRate }}
							</td>
						
						@if($advert->performance->ctr_message == 'winning')
							<td class="text-right bg-success" data-toggle="tooltip" title="Based on 30 days data with a Statistical Significance of {{ $advert->performance->present()->ctrSignificance }}">
							
						@elseif($advert->performance->ctr_message == 'losing')
							<td class="text-right bg-danger">
							
						@else
							<td class="text-right">
						
						@endif
						

							{{ $advert->performance->present()->ctr }}
						</td>
						
					</tr>
					@endforeach
				</tbody>
      		</table>
      	</div>
      </div>
  </div>
</div>

@endsection

@section('javascript')
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
@endsection