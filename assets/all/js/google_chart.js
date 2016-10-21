  
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(fnSharewiseSummaryChart);
  
  function fnSharewiseSummaryChart(){
    var jsonData;
    $.ajax({
        type : 'POST',
        url  :  site_url + '/portfolio/getSharewiseSummary',
        data :  JSON.stringify($('#MyStock').serializeObject()),
        dataType : "json",      //expect back from server  (json, html, text)
        contentType: "application/json",  //data type sending to server
        success : function(result)
        {
            var data = new google.visualization.DataTable(result); 
            var options = {
              title: 'Sharewise Investment',
              sliceVisibilityThreshold: 0.1

            };
            var chart = new google.visualization.PieChart(document.getElementById('pie_investment'));
            chart.draw(data, options);

        }
      });
}