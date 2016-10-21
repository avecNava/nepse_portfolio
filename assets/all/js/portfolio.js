
//refresh data when filters are applied. filters are nested inside #region_filters
$('#region_filter select').on("change",function(){
    var table = $('#stock_summary_desktop').DataTable();  
    table.ajax.reload( fnPortfolioCallback );    
    fnLoadSummary(); 
});

$('#region_filter input').on('change', function(){
    var table = $('#stock_summary_desktop').DataTable();  
    table.ajax.reload( fnPortfolioCallback );    
    fnLoadSummary(); 
});


  $.fn.serializeObject = function()  {
  var o = {};
  var a = this.serializeArray();
  $.each(a, function() {
      if (o[this.name] !== undefined) {
          if (!o[this.name].push) {
              o[this.name] = [o[this.name]];
          }
          o[this.name].push(this.value || '');
      } else {
          o[this.name] = this.value || '';
      }
  });
  return o;
  }

fnLoadStockType();            //load the types of stock eg, IPO, FPO
fnLoadShareholder();          //load Shareholder's names
fnLoadStockGroup();           //load Share groupings
fnLoadStockOffering();        //load Share offering methods
fnLoadSymbol();               //load the company symbols as drop down
fnLoadPortfolio();
fnLoadSummary();

function GetHtml( id ) {   
return   '<ul class="nav nav-tabs">' + 
  '<li class="active"><a data-toggle="tab" href="#_transDetail"><i class="glyphicon glyphicon-list-alt"></i>Details</a></li>' + 
  '<li><a data-toggle="tab" href="#_transChart"><i class="glyphicon glyphicon-signal"></i>Trends</a></li>' +  
  '</ul>' +
  '<div class="tab-content">' +    
    '<div id="_transDetail" class="tab-pane fade in active">' +      
      '<div class="row">' +    
      '<div class="form-group col-md-12">' +  
      '<table class="table" id="' + id + '">'+
      '<thead><tr>'+
        '<th>Symbol</th>'       +
        '<th>Quantity</th>'       +
        '<th>Rate</th>' +
        '<th>Investment</th>'   +
        '<th>Price</th>'           +                
        '<th>Change</th>'  +                
        '<th>Gain</th>'    +                
        '<th>Market value</th>'    +                
        '<th colspan="2">Last Updated</th>'         +                                         
      '</tr></thead>'               + 
      '</table>' +          
      '</div></div>' +
    '</div>' +
    '<div id="_transChart" class="tab-pane fade" style="position:relative">' +
        '<div style="position:absolute;top:-45px;right:0px"><div id="fltr">Filters </div><button class="btn btn-link" tag="7">7 days</button><button class="btn btn-link" tag="15">15 days</button><button class="btn btn-link" tag="30">30 days</button><button class="btn btn-link" tag="90">90 days</button><button class="btn btn-link" tag="180">180 days</button><button class="btn btn-link" tag="365">365 days</button></div>'+
          '<div id="dashboard">' +
          '<div class="col-sm-12"><div id="lineChart_div">loading...</div></div>' +
          '<div class="col-sm-12"><div id="candleStickChart_div"></div></div>' +
          '<div class="col-sm-12"><div id="filter_div"></div></div>' +
          '</div>' +        
    '</div>' +
   '</div>';
}

function makeChart(dataValues)
{
    //https://developers.google.com/chart/interactive/docs/gallery/candlestickchart
    //candlestick chart documentation
    //Col 0: String (discrete) used as a group label on the X axis
    //Col 1: Number specifying the low/minimum value of this marker
    //Col 2: Number specifying the opening/initial value of this marker. 
    //Col 3: Number specifying the closing/final value of this marker. 
    //Col 4: Number specifying the high/maximum value of this marker.
    
    var data = new google.visualization.DataTable();
    data.addColumn('date', 'Date');
    data.addColumn('number', 'Min');        
    data.addColumn('number', 'Previous Closing Price');
    data.addColumn('number', 'Closing Price');
    data.addColumn('number', 'Max');
    
    var __max_date , __min_date, __average_rate, __symbol, __duration;
    __duration = dataValues[0].duration;
    __symbol = dataValues[0].Symbol;
    __average_rate = dataValues[0].average_rate
    __min_date = dataValues[0].trans_datef;
    __max_date = dataValues[dataValues.length-1].trans_datef;   

    for (var i = 0; i < dataValues.length; i++) 
    {
        data.addRow( [
        new Date(dataValues[i].trans_date), 
        Number(dataValues[i].Min),
        Number(dataValues[i].Prev_closing),
        Number(dataValues[i].LTP),            
        Number(dataValues[i].Max),        
        ]);          
    }    

    var view = new google.visualization.DataView(data);
    view.setColumns([0,3]);
    
    controlWrapper = new google.visualization.ControlWrapper({
      controlType: 'ChartRangeFilter',
      containerId: 'filter_div',
      options: {
        filterColumnIndex: 0,   //Date column
        ui: {
            chartOptions: {
                height: 50,
                width: '90%',
                chartArea: {
                    width: '80%'
                }
            }
        }
      }      
    });

    var lineChart = new google.visualization.ChartWrapper({
      chartType: 'LineChart',
      containerId: 'lineChart_div'
    });
    var _message = '<mark>'  + __duration + ' days rate = <b>NPR ' + __average_rate + '</b></mark> (' + __min_date + ' to ' + __max_date + ')' ;
    $('#_transChart div#fltr').html(_message);    
    setOptions(lineChart, 'Trend chart : ' + __symbol);

    // var candleStickChart = new google.visualization.ChartWrapper({
    //   chartType: 'CandlestickChart',
    //   containerId: 'candleStickChart_div'
    // });
    // setOptions(candleStickChart);

    function setOptions (wrapper,title) {
        wrapper.setOption('width', '90%');
        wrapper.setOption('height', 300);
        wrapper.setOption('pointSize', 5);
        wrapper.setOption('curveType', 'function');
        wrapper.setOption('chartArea.width', '80%');
        wrapper.setOption('animation.duration', 500);
        wrapper.setOption('animation.easing','out');
        wrapper.setOption('title',title);
        wrapper.setOption('legend','top');
    }

    var dashboard = new google.visualization.Dashboard(document.getElementById('dashboard'));
    dashboard
      .bind(controlWrapper, [lineChart])
      .draw(view);      
    
}   //end of makechart


function fnApplyCSS(row){
    
     var table = $('#stock_summary_desktop').DataTable();
     var diff = table.row(row).data().Difference;       //read the difference col
     var td = $(row).find('td:eq(5)');     
     
     if( diff > 0 )
        $(td).addClass('_Success'); 
     else if( diff < 0 )
        $(td).addClass('_NoSuccess');     

    td = $(row).find('td:eq(6)');          
     if( diff > 0 )
        $(td).addClass('_Success') 
     else if( diff < 0 )
        $(td).addClass('_NoSuccess') 
}

function fnFormatNumber(num) {      
    num = (num != null ? num : 0);
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");      
}

//convert days to years, months and days
function humanise (diff) 
{
  var str = '';  
  var values = [[' year', 365], [' month', 30], [' day', 1]];
  // Iterate over the values...
  for (var i=0;i<values.length;i++) 
  {
    var amount = Math.floor(diff / values[i][1]);
    // ... and find the largest time value that fits into the diff
    if (amount >= 1) {
       // If we match, add to the string ('s' is for pluralization)
       str += amount + values[i][0] + (amount > 1 ? 's' : '') + ' ';
       // and subtract from the diff
       diff -= amount * values[i][1];
    }    
  }
  return str;
}

// //table decoration callback
function fnPortfolioCallback() 
{
  var api = $('#stock_summary_desktop').DataTable();          
  api.$('tr').each( function (i) {  fnApplyCSS($(this)); });
}

//load portfolio Cummulative data
function fnLoadPortfolio(){   
    //set header tooltip
    var x= $('#stock_summary_desktop').find('th:eq(1)').attr("title","NEPSE symbol for companies").end()
    .find('th:eq(2)').attr("title","Quantity").end()
    .find('th:eq(3)').attr("title","Purchase rate. Weighted average if bought on different rates").end()
    .find('th:eq(4)').attr("title","Last transaction price. The price of the traded day.").end()
    .find('th:eq(5)').attr("title","change in price since last transaction. Yesterday or last Thursday").end();
    
    $('#stock_summary_desktop').DataTable({
      ajax: 
      {
          url: site_url + '/portfolio/getCummulativePortfolioData',
          dataSrc : '',  
          // "processing": true,
          // "serverSide": true,                
          contentType: "application/json",
          type: "POST",
          data : function(d)
          {
            return JSON.stringify( $('#MyStock').serializeObject() );
          },
      },
      dom :   "'<'col-sm-10'fl><'col-sm-2 myclass1'B>" +
               "'<tr>" +
               "'<'col-sm-3'i><'col-sm-9'p>",
      buttons: [ 'copy', 'excel', 'pdf' ],  
      autoWidth : false,  
      order:[],            
      lengthMenu: [[15, 30, 50, -1], [15, 30, 50, "All"]],
      pageLength:30,
      initComplete: fnPortfolioCallback,          
      columnDefs : [{ 'targets': [0,8,9,10], 'orderable': false} ],
      columns: 
      [
        {
         orderable : false, className : 'details-control', data : null, defaultContent:'',
        },
        { sTitle : 'Symbol', 
        render : function(data, type, row){                      
              return '<div title="' + row.Company + '">' + row.Symbol + '</div>';
            }
        }, 
        { sTitle : 'Qty', data: 'TotalQuantity'},
        { sTitle: 'Buy', data : 'EffectiveRate',
        'render':function (data,type,row) { 
          if(row.offr_code=='DIVIDEND') 
            return 'DIVIDEND';
          else
            return  fnFormatNumber(parseFloat(row.EffectiveRate).toFixed(2)); 

        }
        },        
        { sTitle : 'LTP', 
        render : function (data, type, row) { return fnFormatNumber( parseFloat(row.LTP).toFixed(2)); }
        },
        { sTitle: "Change", 
        render : function(data, type, row){
              diff =  parseFloat(row.Difference).toFixed(2);
              return diff;
            }
        },     
        { sTitle: "Change %", 
        render : function(data, type, row){           
          if(row.LTP>0) {
              diff_per = parseFloat((row.Difference / row.LTP) * 100).toFixed(2);
              return diff_per + '%';
            }
            else return '';
            }
        }, 
        { sTitle: "Gain %", 
        render : function(data, type, row){
            if(row.offr_code=='DIVIDEND') return '';
            else{
              overall_gain = parseFloat(row.TotalQuantity *  (row.LTP - row.EffectiveRate)).toFixed(2);
              gain_class = overall_gain > 0 ? '_Success' : '_NoSuccess';
              overall_gain_pc = parseFloat((overall_gain / row.Investment) * 100).toFixed(2);
              return "<span class='"+ gain_class +"''>" + overall_gain_pc + "%</span>";
            }
          }
        }, 
        
        {sTitle : 'Gain (Day/Overall)', 
        render : function(data, type, row){              
            var day_gain_pc='', overall_gain_pc='',day_gain_class='',overall_gain_class='';
            day_gain = parseFloat(row.TotalQuantity * row.Difference).toFixed(2);                                        
            if (row.offr_code!='DIVIDEND'){
              day_gain_pc = parseFloat((day_gain / row.Investment) * 100).toFixed(2);
              day_gain_class = day_gain_pc > 0 ? '_Success' : '_NoSuccess';
            }
            overall_gain = parseFloat(row.TotalQuantity *  (row.LTP - row.EffectiveRate)).toFixed(2);
            if (row.offr_code!='DIVIDEND'){
              overall_gain_pc = parseFloat((overall_gain / row.Investment) * 100).toFixed(2);
              overall_gain_class = overall_gain_pc > 0 ? '_Success' : '_NoSuccess';
            }
            var day_val, overall_val;
            if (row.offr_code=='DIVIDEND'){
              day_val = day_gain;
              overall_val = overall_gain;
            }
            else{
             day_val = day_gain + ' (' + day_gain_pc + '%)';
              overall_val = overall_gain + ' (' + overall_gain_pc + '%)'; 
            }
            return "<div title='Day gain'><span class='"+ day_gain_class +"'><span class='x'>Today</span> "+fnFormatNumber(day_val) + "</span></div><div title='Overall gain'><span class='"+ overall_gain_class +"'><span class='x'>Overall</span> " + fnFormatNumber(overall_val)+"</span></div>";          
            },
        },               

        { sTitle: "High/Low", 
        render : function(data, type, row){                                    
              return "<div title='High-Low Today'><span class='x'>Today</span> " + parseInt(row.Max_price) + " - " + parseInt(row.Min_price) +  "</div><div title='High-Low 52 weeks'><span class='x'><b>"+row.weeks+"</b> wks</span> " + parseInt(row.weeks52_max) + " - " + parseInt(row.weeks52_min) +  "</div>";
            }
        }, 

        { sTitle: 'Investment/<br>Market value', 
        'render':function (data,type,row) { 
          return '<div title="Investment">' + ((row.offr_code=='DIVIDEND') ?'': fnFormatNumber(row.Investment))  +  '</div><div title="Market Value">' + fnFormatNumber(parseFloat(row.LTP *  row.TotalQuantity).toFixed(2)) + '</div>';
        }
        },

      ]        
    });   
} //end of function

//nested table for detail of transactions
// Add event listener for opening and closing details
$('#stock_summary_desktop tbody').on('click', 'td.details-control', function () {

    var table = $('#stock_summary_desktop').DataTable();
    var tr = $(this).closest('tr');
    var row = table.row( tr );
    var _symbol = $(this).next('td').text();           //eg, _symbol = CHCL 
    $('#_Symbol').val(_symbol);            //set the current symbol as hidden input        

    if ( row.child.isShown() ) {    // This row is already open - close it            
        row.child.hide();
        tr.removeClass('shown');
    }
    else{
        
        // create a new nested table with the given ID and show it
        row.child( GetHtml( _symbol ) ).show(); 
        $('#_transChart button').click(function(){var days = $(this).attr('tag'); drawChart(days)});   //event binding for newly created buttons 
        tr.addClass('shown');         //replace plus icon with minus via css 'shown'

        var dtlTable = 'table#' + _symbol;      //initialize datatable if not already done                
        var table1 = $(dtlTable).DataTable({
          ajax: 
          {
              url: site_url + '/portfolio/getPortfolioData',
              dataSrc : '',                  
              contentType: "application/json",
              type: "POST",
              dataType : "JSON",                  
              data : function()
              { 
                arr = $('#MyStock').serializeObject();
                arr['Symbol'] = _symbol;               //append the Symbol from the selected row in the Portfolio Summary table

                return JSON.stringify( arr );
              },
          },
          dom: '<"top"><"bottom">',              
          ordering: false,
          autoWidth:false,
          pageLength:30,
          initComplete: fnPopCallback,
          columns: 
          [ 
              { sTitle : 'Symbol', 
                render : function(data,type, row)
                {
                  var Purchase_date_label = row.Purchase_date + ' (' +  humanise(row.NumberDays) + 'ago)';
                  var merolagani = 'http://merolagani.com/CompanyDetail.aspx?symbol=' +row.Symbol;
                  return '<a href="#" class="popper" class="popper" data-toggle="popover">' + row.Symbol + '</a>' +                      
                  '<div class="pop_content hide">' +
                  '<div class="glyphicon  glyphicon-user" data-toggle="tooltip" title="Shareholder: '+ row.shareholder_name +'"><span>Shareholder : ' + row.shareholder_name +'</span></div>'+ 
                  '<div class="glyphicon  glyphicon-compressed" data-toggle="tooltip" title="Group: '+ row.GroupName +'"><span>Grouped as : ' + row.GroupName  +'</span></div>'+                      
                  '<div class="glyphicon  glyphicon-subtitles" data-toggle="tooltip" title="Acquisition: '+ row.offr_title +'"><span>Offering type : ' + row.offr_title  +'</span></div>'+ 
                  '<div class="glyphicon  glyphicon-calendar" data-toggle="tooltip" title="Purchased on: '+ Purchase_date_label +'"><span>Purchased on : ' + Purchase_date_label  +'</span></div>'+  
                  '<div class="glyphicon  glyphicon-floppy-saved" data-toggle="tooltip" title="Transaction no: '+ row.Transaction_no +'" /><span>Transaction no : ' + row.Transaction_no  +'</span></div>'+
                  '<div class="glyphicon  glyphicon-new-window" data-toggle="tooltip" title="This will open in a new window" /><span><a target="_blank" href='+merolagani+'>View '+row.Symbol+' profile in merolagani</a></div>'+
                  '<div>'+ (row.Remarks !=='' ? 'Remarks:<hr/> '+ row.Remarks:'') + '</div></div>';
                }
              },
            { sTitle : 'Qty', data: 'Quantity'},        
            { sTitle: 'Rate', data : 'Effective_rate',
            'render':function (data,type,row) { return fnFormatNumber(parseFloat(row.Effective_rate).toFixed(2)); }
            },
            { sTitle: 'Investment', 
            'render':function (data,type,row) { return fnFormatNumber(parseFloat(row.Effective_rate *  row.Quantity).toFixed(2)); }
            },
            { sTitle : 'LTP', 
            render : function (data, type, row) { return fnFormatNumber( parseFloat(row.LTP).toFixed(2)); }
            },
            { sTitle: "Change", 
            render : function(data, type, row){
                  diff =  parseFloat(row.Difference).toFixed(2);
                  diff_per = parseFloat((row.Difference / row.LTP) * 100).toFixed(2);
                  result =  diff  +  ' (' + diff_per + '%)';                    
                  return result;
                }
            },        
            {sTitle : 'Gain/Loss', 'class' : 'gainloss', class:'_desktop',
            render : function(data, type, row){              

            var day_gain_pc='', overall_gain_pc='',day_gain_class='',overall_gain_class='';
            day_gain = parseFloat(row.Quantity * row.Difference).toFixed(2);                                        
            var investment = row.Quantity * row.Effective_rate;             

            if (row.offr_code!='DIVIDEND'){
              day_gain_pc = parseFloat((day_gain / investment) * 100).toFixed(2);
              day_gain_class = day_gain_pc > 0 ? '_Success' : '_NoSuccess';
            }
            overall_gain = parseFloat(row.Quantity *  (row.LTP - row.Effective_rate)).toFixed(2);
            if (row.offr_code!='DIVIDEND'){
              overall_gain_pc = parseFloat((overall_gain / investment) * 100).toFixed(2);
              overall_gain_class = overall_gain_pc > 0 ? '_Success' : '_NoSuccess';
            }
            var day_val, overall_val;
            if (row.offr_code=='DIVIDEND'){
              day_val = day_gain;
              overall_val = overall_gain;
            }
            else{
             day_val = day_gain + ' (' + day_gain_pc + '%)';
              overall_val = overall_gain + ' (' + overall_gain_pc + '%)'; 
            }
            return "<div title='Day gain'><span class='"+ day_gain_class +"'><span class='x'>Today</span> "+fnFormatNumber(day_val) + "</span></div><div title='Overall gain'><span class='"+ overall_gain_class +"'><span class='x'>Overall</span> " + fnFormatNumber(overall_val)+"</span></div>";          
            

                // day_gain = parseFloat(row.Quantity * row.Difference).toFixed(2);                            
                // var investment = row.Quantity * row.Effective_rate;             
                // day_gain_pc = parseFloat((day_gain / investment) * 100).toFixed(2);
                // day_gain_class = day_gain_pc > 0 ? '_Success' : '_NoSuccess';

                // overall_gain = parseFloat(row.Quantity *  (row.LTP - row.Effective_rate)).toFixed(2);
                // overall_gain_pc = parseFloat((overall_gain / investment) * 100).toFixed(2);
                // overall_gain_class = overall_gain_pc > 0 ? '_Success' : '_NoSuccess';

                // var day_val = day_gain + ' (' + day_gain_pc + '%)';
                // var overall_val = overall_gain + ' (' + overall_gain_pc + '%)';
                // //return "Day: <span class='"+ day_gain_class +"'>"+day_val + "</span><br>Overall: <span class='"+ overall_gain_class +"'>" + overall_val+"</span>";
                // return "<div title='Day'>D : <span class='"+ day_gain_class +"'>"+day_val + "</span></div><div title='Overall'>O : <span class='"+ overall_gain_class +"'>" + overall_val+"</span></div>";

                },
            },
            { sTitle: 'Market value', class:'text-right',
            'render':function (data,type,row) { return fnFormatNumber(parseFloat(row.LTP *  row.Quantity).toFixed(2)); }
            },

            { sTitle: 'Last updated', data:'trans_datef' },                
            {
              sTitle:'', data: null, orderable:false,
              render : function(data, type, row)
              {
                return '<span title="Update" class="glyphicon glyphicon-pencil update"></span><span title="Sell" class="glyphicon glyphicon-shopping-cart sell"></span><span title="Delete" class="glyphicon glyphicon-remove delete"></span>';
              }
            },
          ]                
    })   //DataTable()
} //end if

//tab navigation inside sub table 
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  var target = $(e.target).attr("href");  // activated tab  
  target = target.substring(2);  
  
  if(target == 'transChart') 
  { 
      google.charts.setOnLoadCallback(drawChart);         // Set a callback to run when the Google Visualization API is loaded.
      
  } //target == 'transChart'
});   //a[data-toggle="tab"]

function drawChart(limit) 
{
  limit = limit || 30;
  var jsonData = $.ajax({
      url: site_url + '/portfolio/get_trade_history',                  
      contentType: "application/json",
      type: "POST",
      dataType : "JSON",                  
      data : JSON.stringify({symbol : $('#_Symbol').val(), 'limit': limit}) ,
      success: function(result){        
        makeChart(result);
      }
    });      
} //drawChart

// //listen to event click for delete/edit/sell
var dtlTable ='#' + _symbol;
$(dtlTable).on('click','span.glyphicon',function()         //eg, id=CHCL (table id)
{    
    var row_id = table1.row( $(this).closest('tr') ).data().Portfolio_id;            
    if( $(this).hasClass('delete') ){
        if (confirm("Are you sure to delete this record ?")) {              
          var tr = $(this).closest('tr');
          $.post(
            site_url + '/portfolio/removePortfolio', {id : row_id} , 
            function(result){
              if(result == 1 ) {                  
                $(tr).hide('slow');                  
              }
              else 
                console.log(result);
            }
          );    //end post
        }// delete alert
    }   // delete check    
    else if( $(this).hasClass('update') ){
      var Portfolio_id = row_id;
      window.location = './portfolio/addstock/' + Portfolio_id;
    }
    else if( $(this).hasClass('sell') ){
      console.log("SELL feature not yet implemented");
    }
});    //dtlTable.click()

}); //stock_summary_desktop tbody click


//fnPopCallback
function fnPopCallback()
{
  //http://stackoverflow.com/questions/8875376/is-it-possible-to-use-a-div-as-content-for-twitters-popover
  //http://jsfiddle.net/isherwood/E5Ly5/
  $('.popper')
  .click(function(e){e.preventDefault();e.stopPropagation();})
  .popover({ 
    html : true,
    title:'More Information'+ 
     '<button type="button" id="close" class="close" onclick="$(&quot;.popper&quot;).popover(&quot;hide&quot;);">&times;</button>',
    //container:'body',
    content:function(){
      return $(this).next('.pop_content').html();
    }
  });
}



function fnLoadSummary()
{
  $.ajax({
    url : site_url + '/portfolio/getPortfolioSummary', 
    type : 'POST',
    contentType: "application/json",
    dataType : 'JSON',
    data : JSON.stringify( $('#MyStock').serializeObject() ),
    success: function(result) 
    {       
        $.each(result, function(index, value) 
        { 
          if(value.Worth != null){
            var _gain_pc = ((value.OverallGain/value.Worth)*100).toFixed(2);
            var gain_text = fnFormatNumber(Math.round(value.OverallGain)) + ' (' + _gain_pc + '%)';
            var _daygain_pc = ((value.DayGain/value.Worth)*100).toFixed(2);
            var daygain_text = fnFormatNumber(Math.round(value.DayGain)) + ' (' + _daygain_pc + '%)';
            $('#region_summary').find('span#Investment').text( fnFormatNumber( Math.round(value.Investment))).closest('ul')
            .find('span#Worth').text( fnFormatNumber( Math.round(value.Worth))).closest('ul')
            .find('span#DayGain').text(  daygain_text ).closest('ul') 
            .find('span#OverallGain').text( gain_text );
            }
            else
            {
              $('#region_summary').find('span#Investment').text( '' ).closest('ul')
              .find('span#Worth').text('' ).closest('ul')
              .find('span#DayGain').text( '' ).closest('ul')  
              .find('span#OverallGain').text('');
            }
          //css decorations
           var li = $('span#DayGain').closest('li'); 
           $(li).removeClass('_Success').removeClass('_NoSuccess');
           if(value.DayGain != null){                   
               value.DayGain >0 ? $(li).addClass('_Success') : $(li).addClass('_NoSuccess');
            }
           
           li = $('span#OverallGain').closest('li');                
           $(li).removeClass('_Success').removeClass('_NoSuccess');
           if(value.OverallGain != null ){                                   
             value.OverallGain >0 ? $(li).addClass('_Success') : $(li).addClass('_NoSuccess');
           }

           li = $('span#worth').closest('li');                
           $(li).removeClass('_Success').removeClass('_NoSuccess');
           if(value.worth != null ){                                   
             value.worth >0 ? $(li).addClass('_Success') : $(li).addClass('_NoSuccess');
           }
           $('#trans_date').html('<span title="last synchronization with NEPSE">Transaction rates based on scraped data from <em>nepalstock.com</em> as of <mark>' + value.trans_date + '</mark></span>' );
        })  //each loop
        $('#region_summary').removeClass('hidden');
    } //success 
  })  //ajax
} //function

function fnLoadStockType()
{
  $.post( site_url + '/portfolio/getStockTypes', 
    function(result) {
       $.each(result, function(index, value) 
       {   
          $("#selStockType").append('<option value="'+value.stock_type_id+'">'+value.stock_type+'</option>')
       });
     }  ,"json"  //json data
     );
}

function fnLoadShareholder()
{
  $.post(site_url + '/portfolio/getShareholderNames', 
   function(result) {         
     $.each(result, 
          function(index, value) {   
            $("#selShareholder").append('<option value="'+value.shareholder_id +'">'+value.shareholder_name+'</option>');
          });
    }  ,"json"  //json data
    );
}

function fnLoadStockGroup()
{
  $.post(site_url + '/portfolio/getStockGroups',     
   function(result) {         
     $.each(result,   
          function(index, value) {   
            $("#selShareGroup").append('<option value="'+ value.GroupID +'">'+value.GroupName  +'</option>');
          });
    }  ,"json"  //json data
    );
}

function fnLoadStockOffering()
{
  $.post(site_url + '/portfolio/getStockOffering', 
   function(result) {         
     $.each(result,   
          function(index, value) {   
            $("#selOfferingMethod").append('<option value="'+ value.offr_code +'">'+value.offr_title +'</option>');
          });
    }  ,"json"  //json data
    );
}

//loads the company symbols (this appears as drop down when adding new stocks)
function fnLoadSymbol()
{
  $.post(site_url + '/portfolio/getSymbols',     
   function(result) {         
      var arr_of_companies=[];
      $.each(result,   function(index, value) {  
        arr_of_companies[value.Symbol] =  value.Company;
        arr_of_companies.push({label : value.Company, value : value.Symbol});        
      }); 
      
      //console.table(arr_of_companies);

      $("#Symbol").autocomplete({ 
          source : arr_of_companies,
          select: function(event, ui) {
            $('#Symbol').val(ui.item.value);            
            $('#_Symbol').val(ui.item.value);             //hidden textbox (needed for validation purpose only)
            console.log('selected '+ ui.item.value);
            //when a value is selected, refresh the screen values too
            var table = $('#stock_summary_desktop').DataTable();  
            table.ajax.reload( fnPortfolioCallback );    
            fnLoadSummary(); 
        },
        focus: function(event, ui) {
          $("#Symbol").val(ui.item.value);          
        },
        // change : function(e, ui){
        //     console.log('select '+ ui.item.label);
        // }
       });
      
    }  ,"json"  //json data
    );
 }   //end function

 //my_account/potfolio
 //save
 $('#ShareGroup button[name="btnSaveGroup"]').click(function(){
    var GroupName = $('#ShareGroup input[name="txtGroupName"]').val();
    $.post(site_url + '/portfolio/saveGroupName', 
    {'name' : GroupName},
    function(result) {         
        var msg = '<span class=text-info>' + result + '</span>';         
        $('#ShareGroup input[name="txtGroupName"]').val('').after(msg);

        //refresh the datatable
        var tbl = $('#tblShareGroup').DataTable();
        tbl.ajax.reload(); 
    } 
    );
 });
//save
 $('#ShareholderGroup button[name="btnSaveShareholder"]').click(function(){
    var ShareholderName = $('#ShareholderGroup input[name="txtShareholderName"]').val();
    $.post(site_url + '/portfolio/saveShareholderName', 
      {'name' : ShareholderName},
    function(result) {
      var msg = '<span class=text-info>' + result + '</span>';
      $('#ShareholderGroup input[name=txtShareholderName]').val('').after( msg );
      
      //refresh the datatable
      var tbl = $('#tblShareholder').DataTable();
      tbl.ajax.reload(); 
    } 
    );
 });
  
  //delete
 $('#tblShareholder tbody').on('click','button',function(){
  var id = $(this).closest('tr').children('td:eq(0)').text();
  if (confirm("Are you sure to delete this record ?")) {
    $.post( site_url + '/portfolio/remove_shareholder_name', {'id' : id});
    //refresh the datatable
    var tbl = $('#tblShareholder').DataTable();
    tbl.ajax.reload(); 
  }
 });

 $('#tblShareGroup tbody').on('click','button',function(){
  var id = $(this).closest('tr').children('td:eq(0)').text();
  if (confirm("Are you sure to delete this record ?")) {
    $.post( site_url + '/portfolio/remove_group_name', {'id' : id});
    //refresh the datatable
    var tbl = $('#tblShareGroup').DataTable();
    tbl.ajax.reload(); 
  }
 });
