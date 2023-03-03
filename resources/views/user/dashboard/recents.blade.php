<div class="card">
  <div class="card-body">
    <h2>Recently viewed</h2>
    <div class="tab-container">
      <ul role="tablist" class="nav nav-tabs">
            
        <li class="nav-item"><a href="#recent-campaigns" data-toggle="tab" role="tab" class="nav-link active">Campaigns</a></li>
            
        <li class="nav-item"><a href="#recent-adgroups" data-toggle="tab" role="tab" class="nav-link">Ad Groups</a></li>
              
      </ul>

      <div class="tab-content">
        
        <div id="recent-campaigns" role="tabpanel" class="tab-pane active">
        	<table class="table table-sm table-responsive">
            <thead>
              <tr>
                <th>Campaign</th>
                <th class="text-right">Impressions</th>
                <th class="text-right">Clicks</th>
                <th class="text-right">Spend</th>
                <th class="text-right">CTR</th>
                <th class="text-right">CVR</th>
                <th class="text-right">CPA</th>
                <th class="text-right">ROAS</th>
              </tr>
            </thead>
            <tbody>
                @foreach($recentCampaigns as $campaign)
                  @if($campaign!=null)
                  <tr>
                    <td>
                      <a href="{{ url('user/adgroups/campaign/' . $campaign->id)}}">{{ $campaign->name }}</a></td>
                    <td class="text-right">{{ $campaign->performance->present()->impressions }}</td>
                    <td class="text-right">{{ $campaign->performance->present()->clicks }}</td>
                    <td class="text-right">{{ $campaign->performance->present()->cost }}</td>
                    <td class="text-right">{{ $campaign->performance->present()->ctr }}</td>
                    <td class="text-right">{{ $campaign->performance->present()->conversionRate }}</td>
                    <td class="text-right">{{ $campaign->performance->present()->cpa }}</td>
                    <td class="text-right">{{ $campaign->performance->present()->roas }}</td>
                  </tr>
                  @endif
                @endforeach       
            </tbody>
          </table>
        </div>
        <div id="recent-adgroups" role="tabpanel" class="tab-pane">
        	<table class="table table-sm table-responsive">
            <thead>
              <tr>
                <th>Ad Group</th>
                <th class="text-right">Impressions</th>
                <th class="text-right">Clicks</th>
                <th class="text-right">Spend</th>
                <th class="text-right">CTR</th>
                <th class="text-right">CVR</th>
                <th class="text-right">CPA</th>
                <th class="text-right">ROAS</th>
              </tr>
            </thead>
            <tbody>
              
                @foreach($recentAdgroups as $adgroup)
                @if($adgroup!=null)
                <tr>
                  <td><a href="{{ url('user/adverts/' . $adgroup->id) }}">{{ $adgroup->name }}</a></td>
                  <td class="text-right">{{ $adgroup->performance->present()->impressions }}</td>
                  <td class="text-right">{{ $adgroup->performance->present()->clicks }}</td>
                  <td class="text-right">{{ $adgroup->performance->present()->cost }}</td>
                  <td class="text-right">{{ $adgroup->performance->present()->ctr }}</td>
                  <td class="text-right">{{ $adgroup->performance->present()->conversionRate }}</td>
                  <td class="text-right">{{ $adgroup->performance->present()->cpa }}</td>
                  <td class="text-right">{{ $adgroup->performance->present()->roas }}</td>
                </tr>
                @endif
                @endforeach
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>