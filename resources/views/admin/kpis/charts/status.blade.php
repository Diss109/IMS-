<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statut des réclamations</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Statut', 'Nombre'],
                @foreach($statuses as $status => $count)
                    ['{{ $status }}', {{ $count }}],
                @endforeach
            ]);

            var options = {
                title: 'Statut des réclamations',
                colors: ['#f6c23e', '#1cc88a', '#e74a3b'],
                backgroundColor: 'transparent',
                chartArea: {width: '75%', height: '70%'},
                legend: {position: 'bottom'},
                hAxis: {
                    textStyle: {fontSize: 10}
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('bar_chart'));
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
        #bar_chart {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div id="bar_chart"></div>
</body>
</html>
