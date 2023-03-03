@extends('layouts.app')

@section('pageTitle')
Accounts
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
						<th>Account</th>
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
					@foreach($accounts as $account)
					<tr>
						<td><a href="{{ url('user/campaigns/' . $account->id) }}">{{ $account->name }}</a></td>
						<td class="text-right">{{ $account->performance->present()->impressions }}</td>
						<td class="text-right">{{ $account->performance->present()->clicks }}</td>
						<td class="text-right">{{ $account->performance->present()->averagePosition }}</td>
						<td class="text-right">{{ $account->performance->present()->cost }}</td>
						<td class="text-right">{{ $account->performance->present()->averageCpc }}</td>
						<td class="text-right">{{ $account->performance->present()->conversions }}</td>
						<td class="text-right">{{ $account->performance->present()->conversionValue }}</td>
						<td class="text-right">{{ $account->performance->present()->cpa }}</td>
						<td class="text-right">{{ $account->performance->present()->roas }}</td>
						<td class="text-right">{{ $account->performance->present()->conversionRate }}</td>
						<td class="text-right">{{ $account->performance->present()->ctr }}</td>
						<td>{{ $account->performance->present()->message }}</td>
					</tr>
					@endforeach
				</tbody>
      		</table>
      	</div>
      </div>
  </div>
</div>

@endsection