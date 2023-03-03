@extends('layouts.app')

@section('pageTitle')
N-Gram Report
@endsection


@section('breadcrumbs')

@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div id="myDiv"><!-- Plotly chart will be drawn inside this DIV --></div>
				<script>
					createNGramGraph()
					function createNGramGraph(){
					@if ($graphValues == "")
					{{ "return;" }}
					@endif
					var values = {!! $graphValues !!}
					for(var n_gram in values){
						values[n_gram] = JSON.parse(values[n_gram])
					}
					for(var n_gram in values){
			
						for(var metric in values[n_gram]){
							values[n_gram][metric] = parseFloat(values[n_gram][metric])
						}
					}
					
					//populated from the db END
					var words = Object.keys(values)
					var metrics = Object.keys(values[words[0]])
					var colours = ['#369196','#47B9BF', '#55DEE5']

					var data = []
					for(var w in words){
					var numbers = []
					for(var m in metrics){
					numbers.push(values[words[w]][metrics[m]])
					}
					var trace = {
						x: metrics,
						y: numbers, 
						name: words[w], 
						type: 'bar',
						marker: {
							color: colours[w]
						}
					};

					data.push(trace)

					}

					var layout = {barmode: 'group'};

					Plotly.newPlot('myDiv', data, layout, {displayModeBar: false});

					}
				</script>
			</div>
		</div>
	</div>
</div>

<div class="row">  
    <div class="col-12">
      <div class="card">
      	<div class="card-body">
	      	<table class="table table-hover table-responsive table-fw-widget datatable">
				<thead>
					<tr>
				        <th>N-Gram</th>
				        <th>N-Gram Count</th>
				        <th>CTR</th>
				        <th>CTR Significance</th>
				        <th>Impressions</th>
				        <th>Clicks</th>
				        <th>Cost</th>
				        <th>Avg. CPC</th>
				        <th>Conversions</th>
				        <th>Conv. Value</th>
				        <th>CPA</th>
				        <th>ROAS</th>
				        <th>Conv. Rate</th>
					</tr>
				</thead>
				<tbody>

					@foreach($nGramPerformance as $record)
						<tr>
							<td>{{ $record->n_gram }}</td>
							<td class="text-right">{{ $record->n_gram_count }}</td>
							<td class="text-right">{{ $record->ctr }}</td>
							<td class="text-right">{{ $record->impressions }}</td>
							<td class="text-right">{{ $record->clicks }}</td>
							<td class="text-right">{{ $record->cost }}</td>
							<td class="text-right">{{ $record->average_cpc }}</td>
							<td class="text-right">{{ $record->conversions }}</td>
							<td class="text-right">{{ $record->conversion_value }}</td>
							<td class="text-right">{{ $record->cpa }}</td>
							<td class="text-right">{{ $record->roas }}</td>
							<td class="text-right">{{ $record->conversion_rate }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
      </div>
  </div>
</div>

@endsection