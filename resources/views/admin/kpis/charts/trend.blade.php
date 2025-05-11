<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Taux de réclamations</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Mois', 'Réclamations'],
                @foreach($labels as $index => $label)
                    ['{{ $label }}', {{ $data[$index] }}],
                @endforeach
            ]);

            var options = {
                title: 'Taux de réclamations',
                curveType: 'function',
                legend: { position: 'bottom' },
                colors: ['#4e73df'],
                backgroundColor: 'transparent',
                chartArea: {width: '85%', height: '70%'},
                hAxis: {
                    textStyle: {fontSize: 10}
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('line_chart'));
            chart.draw(data, options);
        }
    </script>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }
        #line_chart {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div id="line_chart"></div>
</body>
</html>
