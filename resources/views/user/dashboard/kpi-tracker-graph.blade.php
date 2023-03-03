<div class="card">
  <div class="card-body">

	<div id="myDiv"><!-- Plotly chart will be drawn inside this DIV --></div>
<script>

var daysArray = getDaysInMonth();
//target

var target_y = {{ $account->kpi_target_graph_data }}

  target_y = target_y.map(function (i){return Math.round(i)})
      var trace1 = {
    x: daysArray,
    y: target_y,
    mode: 'lines',
    name: 'Target',
    line: {
      dash: 'solid',
      width: 4
    }
  };
  
//actual
var actual_y = {{ $account->kpi_actual_graph_data }}

  actual_y = actual_y.map(function (i){return Math.round(i)})
  var trace2 = {
    x: daysArray,
    y: actual_y,
    mode: 'lines',
    name: 'Actual/Forecast',
    line: {
      dash: 'dashdot',
      width: 4
    }
  };
  
  var data = [trace1, trace2];

  //lowest and highest numbers to show on the graph
  var yRange = [Math.min(...target_y.concat(actual_y))*.5, Math.max(...actual_y.concat(target_y))*1.5]
		
		var shapes = [{
				type: 'line',
				x0: daysArray[getTodaysDate()-1],
				y0:yRange[0],
				x1: daysArray[getTodaysDate()-1],
				y1:yRange[1],		
				line: {
					color: 'grey', 
					width: 1.5,
					dash:'dot'
				}
			}]


  var metricName = convertMetricToTitleCase("{{ $account->kpi_name }}")
  function convertMetricToTitleCase(str){
     str = str.replace("_", " ")
      str = str.toLowerCase().split(' ');
    for (var i = 0; i < str.length; i++) {
      str[i] = str[i].charAt(0).toUpperCase() + str[i].slice(1); 
    }
    return str.join(' ');
  }
  var layout = {
    title: metricName+' Vs Target',
    xaxis: {
      range: [daysArray[0], daysArray[daysArray.length-1]],
      autorange: false,
      tickformat: '.1f'
    },
    yaxis: {
				range: yRange,
        		autorange: false
			},
			legend: {
				traceorder: 'reversed',
				font: {
					size: 14
				},
				yref: 'paper',
				"orientation": "h"
			},
			shapes:shapes 
  };
  
  Plotly.newPlot('myDiv', data, layout, {displayModeBar: false});


     function getDaysInMonth() {
         // Since no month has fewer than 28 days
         var date = new Date();
         var daysGone = date.getDate()-1
         date = new Date(date.setDate(date.getDate()-daysGone))
         var days = [];
         var month = date.getMonth();
         while (date.getMonth() === month) {
          days.push(date.getDate())
          date.setDate(date.getDate() + 1);
         }
         return days;
    }

      function getTodaysDate(){
        var d = new Date()
        return d.getDate()
      }
    
    </script>

	</div>
</div>
