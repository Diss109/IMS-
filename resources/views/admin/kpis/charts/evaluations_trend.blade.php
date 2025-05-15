<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // Create the data table
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Mois');
            data.addColumn('number', 'Évaluations');

            data.addRows([
                @foreach($labels as $index => $label)
                ['{{ $label }}', {{ $data[$index] }}],
                @endforeach
            ]);

            // Set chart options
            var options = {
                title: 'Tendance des Évaluations',
                curveType: 'function',
                legend: { position: 'bottom' },
                colors: ['#1cc88a'],
                lineWidth: 3,
                pointSize: 5,
                hAxis: {
                    title: 'Mois'
                },
                vAxis: {
                    title: 'Nombre d\'évaluations',
                    minValue: 0,
                    viewWindow: {
                        min: 0
                    }
                },
                chartArea: {width: '80%', height: '70%'}
            };

            // Instantiate and draw the chart
            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
<div id="curve_chart" style="width: 100%; height: 300px;"></div>
</body>
</html>
