<div class="card">
  <div class="card-body">
    <h2>Quick fixes</h2>
    <table class="table table-borderless">
	    <tr>
				<th>Missing ads</th>
	    	<td class="text-danger">{{ $numberOfAdgroupsWithTooFewAdverts }}</td>
	    	<td>
	    		@if($numberOfAdgroupsWithTooFewAdverts > 0)
	    			<a href="{{ url('user/adgroups/account/' . $account->id .'/too-few-ads') }}" class="btn btn-sm btn-success">Create Ads</a>
	    		@else
	    			<a href="{{ url('user/adgroups/account/' . $account->id .'/too-few-ads') }}" class="btn btn-sm btn-success disabled">Create Ads</a>
	    		@endif
	    	</td>
	    </tr>
	    <tr>
	    	<th>Too many ads</th>
	    	<td class="text-danger">{{ $numberOfAdgroupsWithTooManyAdverts }}</td>
	    	<td>
	    		@if($numberOfAdgroupsWithTooManyAdverts > 0)

	    			<a href="{{ url('user/adgroups/account/' . $account->id .'/too-many-ads') }}" class="btn btn-sm btn-success">Pause Ads</a>

	    		@else
	    			<a href="{{ url('user/adgroups/account/' . $account->id .'/too-many-ads') }}" class="btn btn-sm btn-success disabled">Pause Ads</a>
	    		@endif
	    	</td>
	    </tr>
	</table>
  </div>
</div>