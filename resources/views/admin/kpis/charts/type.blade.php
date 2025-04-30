<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Types de réclamations</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Type', 'Nombre'],
                @foreach($types as $type => $count)
                    ['{{ $type }}', {{ $count }}],
                @endforeach
            ]);

            var options = {
                title: 'Types de réclamations',
                pieHole: 0,
                colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                backgroundColor: 'transparent',
                chartArea: {width: '85%', height: '70%'},
                legend: {position: 'bottom', textStyle: {fontSize: 10}}
            };

            var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
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
        #pie_chart {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div id="pie_chart"></div>
</body>
</html>
