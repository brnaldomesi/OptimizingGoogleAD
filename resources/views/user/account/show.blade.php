@extends('layouts.app')

@section('pageTitle')
Accounts
@endsection


@section('breadcrumbs')
{!! $breadcrumbs !!}
@endsection

@section ('addAccountGraphsJavascript')

<script>
	addAccountGraphs();
function addAccountGraphs(){

	@if ($account->present()->kpi_actual_vs_target == "")
	var kpi_actual_vs_target = 0;
	@else
	var kpi_actual_vs_target = {{ $account->present()->kpi_actual_vs_target }}
	@endif

	@if ($account->present()->kpi_forecast_vs_target == "")
	var kpi_forecast_vs_target = 0;
	@else
	var kpi_forecast_vs_target = {{ $account->present()->kpi_forecast_vs_target }}
	@endif

	@if ($account->present()->budget_actual_vs_target == "")
	var budget_actual_vs_target = 0;
	@else
	var budget_actual_vs_target = {{ $account->present()->budget_actual_vs_target }}
	@endif

	@if ($account->present()->budget_forecast_vs_target == "")
	var budget_forecast_vs_target = 0;
	@else
	var budget_forecast_vs_target = {{ $account->present()->budget_forecast_vs_target }}
	@endif

		var graph = {
			'height': 30,
			'line': {
				'width': 3,
			},
			'color': {
				'black' : '#000',
				'grey'  : '#ebeaee',
				'red'   : '#CC1229',
				'amber' : '#E6C747',
				'green' : '#47C765',
				},
				'budget_data': {
				'actual_vs_target': budget_forecast_vs_target,
				'forecast_vs_target' :  budget_forecast_vs_target,
			},
			'kpi_data': {
				'actual_vs_target': kpi_actual_vs_target,
				'forecast_vs_target' : kpi_forecast_vs_target,
			},
			'percent_of_month_gone_by': 66,
		};				
						
		// Set graph width and height
		$('.graph, .bar, .barContent, .line').css({
			'width': '100%',
			'height': '100vh',
			'max-height': graph.height + 'px',
			'background-color' : graph.color.grey,
		});
	
		// Positioning
		$('.graph').css('position', 'relative');
		$('.line, .percentage').css('position', 'absolute');

	
		// Set line width, position and color
		$('.line').css('width', graph.line.width);
		$('.line').css({
			'background-color': graph.color.black,
			// 'left': '0px',                              // Align using the start of the element
			'left': '-' + graph.line.width / 2 + 'px',  // Align using center of the element
			// 'left': '-' + graph.line.width + 'px',      // Align using the end of the element
			'margin-left': graph.percent_of_month_gone_by + '%',
		});
	
		// Set percentage position and enter data
		$('.percentage').css({
			'right': 2 + 'px',
			'top': graph.height / 2 - 9 + 'px',
			})
		
		// Set bar size
		var width = graph.budget_data.actual_vs_target > 100 ? 100 : graph.budget_data.actual_vs_target
		$('#budget .barContent').css({'width': width + '%'});
		var percent = graph.budget_data.forecast_vs_target==0 ? "" : graph.budget_data.forecast_vs_target+ '%'
		$('#budget .percentage').html(percent)
		// Set colors based on data
		//get the colour based on the percentage
		var budget_color = getColour(graph.budget_data)
		$('#budget .barContent').css({'background-color':graph.color[budget_color]});
		

		
		// Set bar size
		var width = graph.kpi_data.actual_vs_target > 100 ? 100 : graph.kpi_data.actual_vs_target
		$('#kpi .barContent').css({'width': width + '%'});
		var percent = graph.kpi_data.forecast_vs_target==0 ? "" : graph.kpi_data.forecast_vs_target+"%"
		$('#kpi .percentage').html(percent)
		// Set colors based on data
		//get the colour based on the percentage
		var kpi_color = getColour(graph.kpi_data)
		$('#kpi .barContent').css({'background-color':graph.color[kpi_color]});
		
		$('#budget .percentage').css({'color':'#000'})
		

		//decide the colour
		// Set colors based on data
		
		function getColour(data){
		var color = ""
		if (data.forecast_vs_target <= 90){
			color = "red"
		} else if (data.forecast_vs_target <= 95){
			color = "amber"
		} else{
			color = "green"
		} 
		return color;
						
		}
}

</script>

@endsection

@section('content')

<div class="row">  
    <div class="col-12">
    	<div class="card">
    	<div class="card-body">

    		@if ($errors->any())
			    <div class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
			@endif
    		{!! Form::model($account, ['action'=> ['User\AccountController@update',$account], 'method'=>'put']) !!}
		

			<table class="table table-hover table-responsive table-fw-widget datatable">
				<thead>
					<tr>
						<th>Account</th>
						<th style="min-width:100px;">Spend ({{ $account->present()->budget }})</th>
						<th style="min-width:100px;">KPI ({{ $account->present()->kpi_value }} {{ $account->present()->kpi_name }})</th>
						<th>Budget</th>
						<th>KPI</th>
						<th>KPI Value</th>
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
					
					
					<tr>
						<td><a href="{{ url('user/feed/' . $account->id) }}">{{ $account->name }}</a></td>
						
						<td>
							<div class="graph" id="budget">
								<div class="bar">
									<span class="line"></span>
									<div class="barContent">
										<span class="percentage"></span>
									</div>
								</div>
							</div>
						</td>
						<td>
								<div class="graph" id="kpi">
										<div class="bar">
											<span class="line"></span>
											<div class="barContent">
												<span class="percentage"></span>
											</div>
										</div>
									</div>
						</td>
						<td>
							{!! Form::text('budget',null, ['min'=>'0']) !!}
						</td>
						<td>
							{!! Form::select('kpi_name',[
								'cpa' => 'CPA',
								'ctr' =>  'CTR',
								'roas'  =>  'ROAS',
								'conversion_rate' =>  'Conv. Rate',
								'clicks'  =>  'Clicks',
								'conversions' =>  'Conversions',
								'conversion_value'  => 'Conv. Value',
								],null, ['class' => '']) !!}


						</td>
						<td>
							{!! Form::text('kpi_value',null, ['min'=>'0']) !!}
						</td>

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
						
					</tr>
					
				</tbody>
      		</table>

      		{!! Form::submit('Submit',['class' => 'btn btn-primary']) !!}
      		{!! Form::close() !!}
      	</div>
      </div>
  </div>
</div>

@endsection