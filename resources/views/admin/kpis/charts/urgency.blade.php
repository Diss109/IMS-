<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Niveau d'urgence</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Urgence', 'Nombre'],
                @foreach($urgencies as $urgency => $count)
                    ['{{ $urgency }}', {{ $count }}],
                @endforeach
            ]);

            var options = {
                title: 'Niveau d\'urgence',
                pieHole: 0.4,
                colors: ['#e74a3b', '#f6c23e', '#4e73df', '#1cc88a'],
                backgroundColor: 'transparent',
                chartArea: {width: '85%', height: '70%'},
                legend: {position: 'bottom', textStyle: {fontSize: 10}}
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_chart'));
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
        #donut_chart {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div id="donut_chart"></div>
</body>
</html>
