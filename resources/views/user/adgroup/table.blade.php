<table class="table table-hover table-responsive table-fw-widget datatable">
	<thead>
		<tr>
	        <th>Campaign</th>
	        <th>Ad Group</th>
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
	        <th>Actions</th>
		</tr>
	</thead>
	<tbody>	
	@foreach($adgroups as $adgroup)
	
	<tr>
		<td><a href="{{ url('user/adgroups/campaign/'.$adgroup->campaign->id)}}">{{ $adgroup->campaign->name }}</a></td>
		
		<td><a href="{{ url('user/adverts/' . $adgroup->id) }}">{{ $adgroup->name }}</a></td>
		
		<td class="text-right">{{ $adgroup->performance->present()->impressions }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->clicks }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->averagePosition }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->cost }}</td>
		
		<td class="text-right">{{ $adgroup->performance->present()->averageCpc }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->conversions }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->conversionValue }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->cpa }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->roas }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->conversionRate }}</td>
		<td class="text-right">{{ $adgroup->performance->present()->ctr }}</td>
		
		<td>

			@if($adgroup->performance->message == 'too_few_ads')
				<a href="{{ url('user/adverts/' . $adgroup->id .'/create') }}" class="btn btn-sm btn-success">Create Ads</a>
			@endif
			@if($adgroup->performance->message == 'too_many_ads')
				<a href="{{ url('user/adverts/' . $adgroup->id) }}" class="btn btn-sm btn-success">Pause Ads</a>
			@endif
			@if($adgroup->performance->message == 'has_winners')
				<a href="{{ url('user/adverts/' . $adgroup->id) }}" class="btn btn-sm btn-success">Pause Ads</a>
			@endif
			
		</td>
	</tr>

	@endforeach
	</tbody>
</table>