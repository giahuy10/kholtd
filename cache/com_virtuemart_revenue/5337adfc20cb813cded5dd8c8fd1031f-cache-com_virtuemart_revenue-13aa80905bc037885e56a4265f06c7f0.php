<?php die("Access Denied"); ?>#x#a:2:{s:6:"result";a:2:{s:6:"report";a:0:{}s:2:"js";s:1415:"
  google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Day', 'Orders', 'Total Items sold', 'Revenue net'], ['2018-03-09', 0,0,0], ['2018-03-10', 0,0,0], ['2018-03-11', 0,0,0], ['2018-03-12', 0,0,0], ['2018-03-13', 0,0,0], ['2018-03-14', 0,0,0], ['2018-03-15', 0,0,0], ['2018-03-16', 0,0,0], ['2018-03-17', 0,0,0], ['2018-03-18', 0,0,0], ['2018-03-19', 0,0,0], ['2018-03-20', 0,0,0], ['2018-03-21', 0,0,0], ['2018-03-22', 0,0,0], ['2018-03-23', 0,0,0], ['2018-03-24', 0,0,0], ['2018-03-25', 0,0,0], ['2018-03-26', 0,0,0], ['2018-03-27', 0,0,0], ['2018-03-28', 0,0,0], ['2018-03-29', 0,0,0], ['2018-03-30', 0,0,0], ['2018-03-31', 0,0,0], ['2018-04-01', 0,0,0], ['2018-04-02', 0,0,0], ['2018-04-03', 0,0,0], ['2018-04-04', 0,0,0], ['2018-04-05', 0,0,0], ['2018-04-06', 0,0,0]  ]);
        var options = {
          title: 'Report for the period from Friday, 09 March 2018 to Saturday, 07 April 2018',
            series: {0: {targetAxisIndex:0},
                   1:{targetAxisIndex:0},
                   2:{targetAxisIndex:1},
                  },
                  colors: ["#00A1DF", "#A4CA37","#E66A0A"],
        };

        var chart = new google.visualization.LineChart(document.getElementById('vm_stats_chart'));

        chart.draw(data, options);
      }
";}s:6:"output";s:0:"";}