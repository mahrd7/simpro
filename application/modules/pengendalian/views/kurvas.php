<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Kurva S Simpro</title>

  <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.9.1.min.js"></script>
  <script type="text/javascript">

        var categories = <?php echo $week ?>;
        var data = <?php echo $chart ?>;

        datas = $.map(data, function(i) { return [[i][0]]; });
        categoriesn = $.map(categories, function(i) { return [[i][0]]; });
        

  $(function () {
    $('#container').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Schedule Kerja Simpro'
        },
        subtitle: {
            text: 'Project1'
        },
        xAxis: {
            categories: categoriesn
        },
        yAxis: {
            title: {
                text: 'Persentasi (%)'
            }
        },
        tooltip: {
            enabled: false,
            formatter: function() {
                return '<b>'+ this.series.name +'</b><br/>'+
                this.x +': '+ this.y +'%';
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [{
            name: 'Schedule Simpro',
            data: datas
        }]
    });
});


  </script>
</head>
<body>
    <script src="<?php echo base_url(); ?>assets/js/line_chart/highcharts.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/line_chart/modules/exporting.js"></script>

    <div id="container" style="min-width: 310px; margin: 0 auto"></div>

</body>
</html>
