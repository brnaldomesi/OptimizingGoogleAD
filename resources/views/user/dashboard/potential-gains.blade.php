<div class="card">
  <div class="card-body">
    <h2>Potential gains</h2>
    <p>Your potential performance increase by pausing losing ads.
    @isset($potentialGain)

    <table class="table table-bordered">
    <tr>
    	<th class="text-right">Winners</th>
    	<th class="text-right">Clicks</th>
    	<th class="text-right">Conversions</th>
    	<th class="text-right">CPA</th>
    	<th class="text-right">Cost change</th>
    </tr>

    	<tr>
    		<td class="text-right">{{ $potentialGain->present()->winners }}</td>
    		<td class="text-right">{{ $potentialGain->present()->clicks }}</td>
    		<td class="text-right">{{ $potentialGain->present()->conversions }}</td>
    		<td class="text-right">{{ $potentialGain->present()->cpa }}</td>
    		<td class="text-right">{{ $potentialGain->present()->cost_change }}</td>
    	</tr>
        </table>
        <div class="clearfix">
            @if($potentialGain->winners > 0)
                <a href="{{ url('user/adgroups/account/' . $account->id .'/has-winners') }}" class="btn btn-sm btn-success float-right">Winners</a>
            @else
                <a href="{{ url('user/adgroups/account/' . $account->id .'/has-winners') }}" class="btn btn-sm btn-success float-right disabled">Winners</a>

            @endif

        </div>
    @else
    	
    	<p>No data is available at present.

    @endisset
    	
	
  </div>
</div>