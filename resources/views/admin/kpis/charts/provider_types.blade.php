<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Type de Prestataire', 'Nombre'],
                @foreach($types as $label => $count)
                ['{{ $label }}', {{ $count }}],
                @endforeach
            ]);

            var options = {
                title: 'Distribution des Prestataires par Type',
                pieHole: 0.4,
                colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796'],
                chartArea: {width: '80%', height: '80%'},
                legend: {position: 'right'}
            };

            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
<div id="donutchart" style="width: 100%; height: 300px;"></div>
</body>
</html>
