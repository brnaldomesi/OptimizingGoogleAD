@extends('layouts.app')

@section('pageTitle')
Campaigns
@endsection


@section('breadcrumbs')
{!! $breadcrumbs !!}
@endsection


@section('content')

<div class="row">  
    <div class="col-12">
    	<div class="card">
    	<div class="card-body">
      		<table class="table table-hover table-responsive table-fw-widget datatable">
				<thead>
					<tr>
						<th>Campaign</th>
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
				        <th>Message</th>
					</tr>
				</thead>
				<tbody>
					@foreach($campaigns as $campaign)
					<tr>
						<td><a href="{{ url('user/adgroups/campaign/' . $campaign->id) }}">{{ $campaign->name }}</a></td>
						<td class="text-right">{{ $campaign->performance->present()->impressions }}</td>
						<td class="text-right">{{ $campaign->performance->present()->clicks }}</td>
						<td class="text-right">{{ $campaign->performance->present()->averagePosition }}</td>
						<td class="text-right">{{ $campaign->performance->present()->cost }}</td>
						<td class="text-right">{{ $campaign->performance->present()->averageCpc }}</td>
						<td class="text-right">{{ $campaign->performance->present()->conversions }}</td>
						<td class="text-right">{{ $campaign->performance->present()->conversionValue }}</td>
						<td class="text-right">{{ $campaign->performance->present()->cpa }}</td>
						<td class="text-right">{{ $campaign->performance->present()->roas }}</td>
						<td class="text-right">{{ $campaign->performance->present()->conversionRate }}</td>
						<td class="text-right">{{ $campaign->performance->present()->ctr }}</td>
						<td>{{ $campaign->performance->present()->message }}</td>
					</tr>
					@endforeach
				</tbody>
      		</table>
      	</div>
      </div>
  </div>
</div>

@endsection