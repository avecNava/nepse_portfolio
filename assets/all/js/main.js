
  // on load of the page: switch to the currently selected tab
  // var hash = window.location.hash;
   //$('a[href="' + hash + '"]').tab('show');  
  
   
   $('ul#account a').click(function(){ 
    $('ul#account a').removeClass('selected');
    $(this).addClass('selected');
    var target = $(this).attr('href');    
    $('.tab-pane').hide();
    $(target).fadeIn('slow');
    
    target = target.substring(1);   //strip # sign from the target var
    if(target.toLowerCase()=='tax'){
    fnLoadStockType();
    fnLoadStockOffering();
    //init taxid
    $.post( 
        site_url + '/main/get_unique_id', {prefix:'TAX'}, 
        function (new_id){ 
          $('form#Tax input#_tax_id').val( new_id ); 
          //console.log(new_id);
    });        

    if ( ! $.fn.DataTable.isDataTable( '#tblTax' ) ) 
        fnLoadBuySellRate();
    else{
      tbl = $('#tblTax').DataTable();
      tbl.ajax.reload(callbackBuySellRate);
    }        
    }else if(target.toLowerCase()=='shareholder'){
    if ( ! $.fn.DataTable.isDataTable( '#tblShareholder' ) ) 
        fnLoadShareholder();
    else{
      tbl = $('#tblShareholder').DataTable();
      tbl.ajax.reload( shareholderCallback );
    }
    }
    else if(target.toLowerCase()=='admin'){
    if ( ! $.fn.DataTable.isDataTable( '#tblAdmin' ) ) 
      fnLoadUsers();
    else {
      tbl = $('#tblAdmin').DataTable();       
      tbl.ajax.reload( adminCallback );
    }
    }
    else if(target.toLowerCase()=='user'){
    if ( ! $.fn.DataTable.isDataTable( '#tblUserInfo' ) ) 
      fnLoadUserInfo();
    else {
      tbl = $('#tblUserInfo').DataTable();       
      tbl.ajax.reload(  );
    }
    }
    else if(target.toLowerCase()=='sharegroup'){
    if ( ! $.fn.DataTable.isDataTable( '#tblShareGroup' ) ) 
      fnLoadShareGroup();
    else {
      tbl = $('#tblShareGroup').DataTable();       
      tbl.ajax.reload( sharegroupCallback );
    }
    }
    else if(target.toLowerCase()=='company'){
    if ( ! $.fn.DataTable.isDataTable( '#tblCompany' ) ) 
      fnNewListedCompany();
    else {
      tbl = $('#tblCompany').DataTable();       
      tbl.ajax.reload( newCompanyCallback );
    }
    }

  })

  $('a[data-toggle="tab"]').off('shown.bs.tab').on('shown.bs.tab', function (e) {

      $('#_err_msg').empty();
      var target = $(e.target).attr("href") // activated tab
      window.location.hash = target;        //bookmark the tab
      
      target = target.substring(1);
      var tbl = '';

     if(target.toLowerCase()=='watchlist'){
        if ( ! $.fn.DataTable.isDataTable( '#nepse_watchlist' ) ) 
          fnWatchList();
        else {
          tbl = $('#nepse_watchlist').DataTable();       
          tbl.ajax.reload( watchCallback );
        }
      }
      else if(target.toLowerCase()=='allcompanies'){        
        if ( ! $.fn.DataTable.isDataTable( '#nepse_company' ) ) 
          fnShowCompany();
        else {
          tbl = $('#nepse_company').DataTable();       
          tbl.ajax.reload( companyCallback );
        }
      }      
  });

function fnLoadStockType()
{
  $.post( site_url + '/portfolio/getStockTypes', 
    function(result) {
       $.each(result, function(index, value) 
       {   
          $("#stock_type_id").append('<option value="'+value.stock_type_id+'">'+value.stock_type+'</option>')
       });
     }  ,"json"  //json data
     );
}

function fnLoadStockOffering()
{
  $.post(site_url + '/portfolio/get_commissionable_offering', 
   function(result) {         
     $.each(result,   
          function(index, value) {   
            $("#offr_code").append('<option value="'+ value.offr_code +'">'+value.offr_title +'</option>');
          });
    }  ,"json"  //json data
    );
}

//when SaveShareholder button is clicked  
$('#btnSaveShareholder').click(function(){
  var obj = $('div#Shareholder').find('input[name="txtShareholderName"]');
  var name = obj.val();
  $('#_err_msg').empty();
  if(name.length >=5){
      $.post( site_url + '/account/SaveShareholder', JSON.stringify({'name' : name}));
      var tbl = $('#tblShareholder').DataTable();
      tbl.ajax.reload(shareholderCallback);
      obj.val('');   //reset the name field
    }
    else { 
      var msg='Please enter name of at least 5 characters';
      $('<div/>',{class:'text-danger',text: msg}).appendTo('#_err_msg').fadeIn('slow') 
    }
});

function shareholderCallback() 
{
    $('#tblShareholder')
    .find('td > span.glyphicon')
    .click(function(){
      var row = $(this).closest('tr');
      var id = row.find('td:eq(0)').text();
      var name = row.find('td:eq(1)').text();        
      if (confirm("Please confirm if you want to delete \"" + name + "\"?")){
          $.post( 
            site_url + '/account/removeShareholder', 
            JSON.stringify({'shareholder_id' : id}), 
            function(data){
              if(data){
                row.fadeOut('slow');
                msg='Deleted shareholder : ' + name;
              }
              else
                msg='Can not delete the record. It might be that this record is being used in your portfolio.';

              $('<div/>',{class:'text-danger',text: msg}).appendTo('#_err_msg').fadeIn('slow');
          })
      }       
    })
} 

function fnLoadShareholder(){

 $('#tblShareholder').DataTable({
      ajax: 
      {
          url: site_url + '/portfolio/getShareholderNames',
          dataSrc : '',                  
          contentType: "application/json",
          type: "POST",          
      },
      pageLength : 50,
      dom: "<'row'>",
      autoWidth : false,      
      initComplete : shareholderCallback,
      columnDefs: [
            //{ 'targets': [0], 'visible': false, 'searchable': false },
            { 'targets': [0,2], 'orderable': false, 'searchable':false },
        ],            
      columns: 
      [
        { sTitle : 'ID', defaultContent:'', data: 'shareholder_id'},
        { sTitle : 'Names', defaultContent:'', data: 'shareholder_name'},        
        { Title : '', data: null,
        render : function (){          
          return '<span class="glyphicon glyphicon-remove"></span>';
        }        
      },
      ]
    });
}

var callbackBuySellRate = function(){
  
  $('#tblTax').find('td > span').click(function()
  {  
      var _table = $('#tblTax').DataTable();
      var _row = $(this).closest('tr');

      var _id = _table.row(_row).data().tax_id;
      
      if (confirm("Please confirm if you want to delete this record?")){
          $.post( 
            site_url + '/main/remove_tax_info', 
            JSON.stringify({'tax_id' : _id}), 
            function(data){
              if(data){
                _row.fadeOut('slow');
                msg='Deleted successfully';
              }
              else
                msg='Oopsy! Can not delete the record. SVP try again later.';

              $('<div/>',{class:'text-danger',text: msg}).appendTo('#_err_msg').fadeIn('slow');
          })
      } 
  })

  $('#tblTax tbody tr').click(function()
  {  
    $('#_err_msg').empty();
    var table = $('#tblTax').DataTable();
    $('#_tax_id').val ( table.row(this).data().tax_id );
    $('select[name="action"]').val( table.row(this).data().action );
    $('select[name="stock_type_id"]').val( table.row(this).data().stock_type_id );
    $('select[name="offr_code"]').val( table.row(this).data().offr_code) ;
    $('input[name="low_range"]').val( table.row(this).data().low_range );
    $('input[name="high_range"]').val( table.row(this).data().high_range );
    $('input[name="tax_per"]').val( table.row(this).data().tax_per );
  })
}

function fnLoadBuySellRate(){
 $('#tblTax').DataTable({
      ajax: 
      {
          url: site_url + '/main/get_tax_info',
          dataSrc : '',                  
          contentType: "application/json",
          type: "POST",          
      },
      pageLength : 50,
      dom: "<'row'>",
      autoWidth : false,      
      initComplete : callbackBuySellRate,
      columnDefs: [            
            { 'targets': [0,7], 'orderable': false, 'searchable':false, 'visible':false },
            { 'targets': [8], 'orderable': false, 'searchable':false  },
          ],            
      columns: 
      [
        { sTitle : 'ID', data: 'tax_id'},
        { sTitle : 'Action', data: 'action'},        
        { sTitle : 'Stock', data: 'stock_type'},        
        { sTitle : 'Offering', data: 'offr_code'},
        { sTitle : 'Low', data: 'low_range',
          render: function(data,type,row){
            return fnFormatNumber(data)
          }
        },
        { sTitle : 'High', data: 'high_range',
          render: function(data,type,row){
            return fnFormatNumber(data)
          }
        },        
        { sTitle : 'Tax %', data: 'tax_per',render:function(data,type,row){return data + '%'}},        
        { sTitle : 'Last updated on', data: 'last_update_date'},        
        { sTitle : '', data: null,
        render : function (){          
          return '<span class="glyphicon glyphicon-remove"></span>';
        }        
      },
      ]
    });
}

//when SaveTax button is clicked  
$('#btnSaveTax').click(function(){
    
    var DTO = $('form#Tax').serializeObject();
    
    $.ajax({
            url: site_url + '/main/save_tax_info',                   
            contentType: "application/json",
            type: "POST",
            dataType : "JSON",                  
            data : JSON.stringify( DTO ) ,
            success: function(result)
            {  
              //reset input fields, also get a new id for the form
              $.post( 
                site_url + '/main/get_unique_id', {prefix:'TAX'}, 
                function (new_id){ 
                $('form#Tax input#_tax_id').val( new_id );
              });

              $('#action option:first').attr('selected','selected');
              $('#stock_type_id option:first').attr('selected','selected');
              $('#offr_code option:first').attr('selected','selected');
              $('form#Tax [name=low_range]').val('');
              $('form#Tax [name=high_range]').val('');
              $('form#Tax [name=tax_per]').val('');        

              var tbl = $('#tblTax').DataTable();
              tbl.ajax.reload(callbackBuySellRate);
          }
    }); 
  });

var sharegroupCallback = function () {

  $('div#ShareGroup').find('td>span.glyphicon')      
  .click(function(){
    var row = $(this).closest('tr');
    var id = row.find('td:eq(0)').text();
    var name = row.find('td:eq(1)').text();
    if (confirm("Please confirm if you want to delete \"" + name + "\"?")){
        $.post( site_url + '/account/removeShareGroup', 
          JSON.stringify({'GroupID' : id}),
            function(data){
              if(data){
                row.fadeOut('slow');
                msg='Deleted shareholder : ' + name;
              }
              else
                msg='Can not delete the record. It might be that this record is being used in your portfolio.';

              $('<div/>',{class:'text-danger',text: msg}).appendTo('#_err_msg').fadeIn('slow');
            }
        );    //post
      }   //cnfimo
  });
}; 

//when SaveSharegroup button is clicked
$('#btnSaveShareGroup').click(function(){
    var obj = $('div#ShareGroup').find('input[name="txtGroupName"]');
    var name = obj.val();
    $('#_err_msg').empty();
    if(name.length >=5){
        $.post( site_url + '/account/SaveShareGroup', JSON.stringify({'name' : name}));
        var tbl = $('#tblShareGroup').DataTable();
        tbl.ajax.reload(sharegroupCallback);
        obj.val('');   //reset the name field
      }
      else { 
        var msg='Please enter name of at least 5 characters';
        $('<div/>',{class:'text-danger',text: msg}).appendTo('#_err_msg').fadeIn('slow') 
      }
});  

function fnLoadShareGroup(){
$('#tblShareGroup').DataTable({
    ajax: 
    {
        url: site_url + '/portfolio/getStockGroups',
        dataSrc : '',                  
        contentType: "application/json",
        type: "POST",          
    },
    dom: "<'row'>",
    pageLength : 50,
    initComplete: sharegroupCallback,      
    autoWidth : false,      
    columnDefs: [
          //{ 'targets': [0], 'visible': false, 'searchable': false },
          { 'targets': [0,2], 'orderable': false, 'searchable':false },
      ],      
    columns: 
    [
      { sTitle : 'ID', defaultContent:'', data: 'GroupID'},
      { sTitle : 'Names', defaultContent:'', data: 'GroupName'},        
      { data: null,
      render : function (){        
        return '<span class="glyphicon glyphicon-remove"></span>';
      }        
    },
    ]
  });
}

//table decoration callback
var adminCallback = function () {

  $('input:checkbox').click(function(){
    var checkBox = $(this);
    var id  = checkBox.closest('tr').find('td:eq(0)').text();
    var state = checkBox.is(':checked');
    var target = checkBox.attr('tag');

    if (confirm("Please confirm if you want to do this ?")){
          $.post( site_url + '/account/update_user_roles', JSON.stringify({'id' : id, 'target' : target, 'state':state}));
          //reload datatable
          var tbl = $('#tblAdmin').DataTable();
          tbl.ajax.reload();
        }
     else
       checkBox.prop('checked',!state);        //reflect new status      
  }); 

};

function fnLoadUsers(){
 $('#tblAdmin').DataTable({
      ajax: 
      {
          url: site_url + '/account/getUserRoles',
          dataSrc : '',                  
          contentType: "application/json",
          type: "POST",          
      },
      dom: "<'row'>",
      pageLength : 50,
      autoWidth : false,           
      initComplete: adminCallback, 
      columnDefs:
      [//{targets:[0], visible:false},
      {targets:[3,4], searchable:false,orderable:false}
      ],
      columns: 
      [
        {data: 'login_id'},
        {data: 'full_name'},        
        {data: 'email'},
        {data: 'privilege',
          render : function(data,type,row){
            var disabled = (row.myself==1) ? 'DISABLED':'';
            var chk = '<input '+ disabled +' tag="privilege" type="checkbox" ' + ( data==1 ? "checked" : "" ) + '/>';
            return chk;
          }
        },        
        { data: 'active',
          render : function(data,type,row){
            var disabled = (row.myself==1) ? 'DISABLED':'';
            var chk = '<input '+ disabled +' tag="active" type="checkbox" ' + ( data==1 ? "checked" : "" ) + '/>';
            return chk;
          }
        },
      ]
    });
}
  //all user roles
  function fnLoadUserInfo(){
  $('#tblUserInfo').DataTable({
      ajax: 
      {
          url: site_url + '/account/getAllUserStatus',
          dataSrc : '',                  
          contentType: "application/json",
          type: "POST",          
      },
      //dom: "<'row'>",      
      autoWidth : false,    
      pageLength : 50,  
      // columnDefs: [
      //       { 'targets': [0,2], 'orderable': false, 'searchable':false },
      //   ],      
      columns: 
      [
        {data: 'login_id'},
        {data: 'full_name'},
        {data: 'email'},
        {data: 'last_login_date', 
          render : function(data,type,row){
            if(data!=null)
                return data + '<br/>' + humanise(row.login_days) + ' ago';
              else
                return   '<mark>Never logged in</mark>';
          }
        },
        {data: 'create_date',
           render : function(data,type,row){
            return data + '<br/>' + humanise(row.create_days) + ' ago';
          }
        },
        {data: 'privilege',
          render : function(data,type,row){
            var chk = '<input disabled type="checkbox" ' + ( data==1 ? "checked" : "" ) + '/>';
            return chk;
          }
        },        
        { data: 'active',
          render : function(data,type,row){
            var chk = '<input disabled type="checkbox" ' + ( data==1 ? "checked" : "" ) + '/>';
            return chk;
          }
        },
      ]
    });
}

var newCompanyCallback= function(){
  $('#tblCompany').find('td:nth-child(5) a')
  .click(function(){
    var action = $(this).attr('action');
    
    var td = $(this).closest('tr').find('td:eq(0)');      

    if(action=='remove'){
      var symbol = td.text();     //get the id from the first col of the row
      console.log(symbol);
      if(confirm("Are you sure to remove this company?")){
      $.post( site_url + '/main/remove_company', {'symbol' : symbol},
        function(result){
          if(result==1){
              //hide the row
              td.closest('tr').fadeOut('slow');
              $('#_err_msg').html('<strong>'+td.closest('tr').find('td:eq(1)').text()+'</strong> has been removed');
          }          
        });
      }
    }
    else if (action == 'edit'){
      $(this).text('Update').attr('action','update');              
      console.log('editing ' + td.text());
      td.html('<input type="text" class="form-control" old_val="' + td.text() + '" value="' + td.text() +'" />');        
    }
    else {
      var old_val = td.find('input[type="text"]').attr('old_val');
      var new_val = td.find('input[type="text"]').val();
      if (new_val.length > 2)   //save only if something has been entered for SYMBOL
      {
        $.post( site_url + '/main/update_symbol', JSON.stringify({'symbol' : old_val, 'new_symbol' : new_val}),
          function(result){
            console.log(result);
            $('#_err_msg').html(result);
          });

        console.log('updated to '  + new_val);
        var tbl = $('#tblCompany').DataTable();
        tbl.ajax.reload(newCompanyCallback);
      }
    }
    
  });    
}

function fnNewListedCompany(){
 $('#tblCompany').DataTable({
      ajax: 
      {
          url: site_url + '/main/get_new_company',
          dataSrc : '',                  
          contentType: "application/json",
          type: "POST",          
      },
      dom: "<'row'>",
      pageLength : 50,
      autoWidth : false,      
      initComplete : newCompanyCallback,
      columns: 
      [
        { sTitle : 'Symbol', data: 'symbol'},
        { sTitle : 'Company', data: 'company'},        
        { sTitle : 'Similiar', data: 'similiar_company'},        
        { sTitle : 'Added on', data: 'create_date'},        
        { Title : 'Action', data: null,
        render : function (){          
          return '<i class="glyphicon glyphicon-pencil"></i> <a action="edit" href="#">Edit</a>&nbsp;<i class="glyphicon glyphicon-remove"></i><a action="remove" href="#">Delete</a>';;
        }        
      },
      ]

    });
}

function fnShowMarketRate(){
    //set header tooltip
    $('#market_rate')
    .find('th:eq(0)').attr("title","Transaction date").end()
    .find('th:eq(1)').attr("title","Company Symbol").end()    
    .find('th:eq(2)').attr("title","Maximum price").end()
    .find('th:eq(3)').attr("title","Minimum price").end()
    .find('th:eq(4)').attr("title","Closing price").end()
    .find('th:eq(5)').attr("title","Previous price").end()
    .find('th:eq(6)').attr("title","Difference between max and min price");

    $('#market_rate').DataTable({
      ajax: 
      {
          url: site_url + '/main/get_market_rate',
          dataSrc : '',                  
          contentType: "application/json",
          type: "POST",
          // data : function(d)
          // {
          //   return JSON.stringify( $('#MyStock').serializeObject() );
          // },
      },      
     pageLength : 100,     
     lengthMenu : [[25,50,100,200,-1],[25,50,100,200,'All']],    
     dom :   "<'row'<'col-sm-10'fl><'col-sm-2 myclass1'B>>" +
       "<'row'<'col-sm-12'tr>>" +
       "'<row'<'col-sm-5'i><'col-sm-7'p>>",
      buttons: [ 'copy', 'excel', 'pdf' ],  
      autoWidth : false,      
      // columnDefs: [
      //       //{ 'targets': [0], 'visible': false, 'searchable': false },
      //       { 'targets': [2], 'orderable': false, 'searchable':false },
      //   ],      
      columns: 
      [
        { 
          sTitle : 'Symbol', defaultContent:'', data: 'Symbol',
         'render' : function(data, type, row){
           return '<a target="_blank" href="http://www.merolagani.com/CompanyDetail.aspx?symbol=' + row.Symbol + '">'+row.Symbol+'</a>';
         }
        },
        
      { sTitle: 'LTP', data : 'LTP'},      
      { sTitle: 'LTV', data : 'LTV'},      
      { sTitle: 'Change', data : 'Difference'},
      { sTitle: 'Change %', data : 'Difference_per'},      
      { sTitle: 'Open', data : 'Open_price'},      
      { sTitle: 'High', data : 'Max_price'},
      { sTitle: 'Low', data : 'Min_price'},
      { sTitle: 'Volume', data : 'volume'},
      { sTitle: 'Previous Closing', data : 'Prev_closing'},      
      { sTitle : 'Transaction date', data: 'trans_date'}, 

        ],
        "order": [[ 0, "desc" ],[1,"asc"]]
    });  
}

//table decoration callback
var companyCallback = function () {
  
var api = $('#nepse_company').DataTable();          
  api.$('tr').each( function (i) {
     fnApplyCSS($(this), 3);              
});

$('td').click(function(){
  $('#_err_msg_company').empty();
  $(this).find('span').toggleClass('star').toggleClass('starred');
  tmp = $(this).find('span').attr('class');
  $val = (tmp == 'star') ? 0 : 1;
  
  var tbl = api;
  var r = $(this).closest('tr');
  var $symbol = tbl.row(r).data().Symbol;
  $.post(site_url + '/portfolio/addtowatch',{syb: $symbol, val:$val }, 
    function(){
      var $msg = ($val)?" added to ":" removed from ";
      $msg = '<strong>' + $symbol + '</strong>' + $msg + ' watchlist';
      $('<span>',{class:"text-success",html:$msg}).appendTo('#_err_msg_company');      
  });
})

};

function fnShowCompany() 
{      
  $('#nepse_company').DataTable({
    ajax: 
    {
        url: site_url + '/main/get_company_details',
        dataSrc : '',                  
        contentType: "application/json",
        type: "POST",
    },      
    initComplete: companyCallback, 
   dom :   "<'row'<'col-sm-10'l><'col-sm-2 myclass1'>f>" +
     "<'row'<'col-sm-12'tr>>" +
     "'<row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [ 'copy', 'excel', 'pdf' ],  
    autoWidth : false,   
    pageLength : 100,     
    lengthMenu : [[25,50,100,200,-1],[25,50,100,200,'All']],    
    columns: 
    [
      { 
        sTitle : 'Symbol', defaultContent:'', data: 'Symbol',
           'render' : function(data, type, row){
             return '<a target="_blank" href="http://www.merolagani.com/CompanyDetail.aspx?symbol=' 
             + row.Symbol + '">'+row.Symbol+'</a>';
           }
      },

      { sTitle : 'Company', data: 'Company'},        
      { sTitle: 'LTP', data : 'LTP'},      
      { sTitle: 'LTV', data : 'LTV'},      
      { sTitle: "point", 
      render : function(data, type, row){
            diff =  parseFloat(row.Difference).toFixed(2);
            return diff;
          }
      },     
      { sTitle: "%", 
      render : function(data, type, row){                      
            diff_per = parseFloat((row.Difference / row.LTP) * 100).toFixed(2);
            return diff_per + '%';
          }
      }, 
      { sTitle: 'Open', data : 'Open_price'},      
      { sTitle: 'High', data : 'Max_price'},
      { sTitle: 'Low', data : 'Min_price'},
      { sTitle: 'Volume', data : 'volume'},
      { sTitle: 'Prev Closing', data : 'Prev_closing'},      
      { sTitle : 'Updated', data: 'trans_date'}, 
      {
        data:null, class:'watch',
        render: function(data,type, row){        
          var $css = (row.Symbol == row.watch)?'starred':'star';
          return '<span class="'+$css+'"></span>';
        }
      },
      ],
      "order": [[ 0, "asc" ]]        
  });    
}

function watchCallback(){
  $('td').click(function(){
      var $row = $(this).closest('tr');
      $('#_err_msg').empty();
      $(this).find('span').toggleClass('star').toggleClass('starred');
      tmp = $(this).find('span').attr('class');
      $val = (tmp == 'star') ? 0 : 1;
      var tbl = $('#nepse_watchlist').DataTable();
      var r = $(this).closest('tr');
      var $symbol = tbl.row(r).data().Symbol;
      $.post(site_url + '/portfolio/addtowatch',{syb: $symbol, val:$val }, 
        function(){
          var $msg = ($val)?" added to ":" removed from ";
          $msg = '<strong>' + $symbol + '</strong>' + $msg + ' watchlist';
          $('<span>',{class:"text-success",html:$msg}).appendTo('#_err_msg');
          if($val==0) $row.slideUp();
      });
  })
}

function fnWatchList() 
{      
  $('#nepse_watchlist').DataTable({
    ajax: 
    {
        url: site_url + '/portfolio/get_watch_list',
        dataSrc : '',                  
        contentType: "application/json",
        type: "POST",
    },      
    initComplete: watchCallback, 
    dom:"",
   // dom : "<'row'<'col-sm-12'fl>>" +
   //   "<'row'<'col-sm-12'tr>>" +
   //   "'<row'<'col-sm-5'i><'col-sm-7'p>>",
    autoWidth : false,   
    pageLength : 100,     
    lengthMenu : [[25,50,100,200,-1],[25,50,100,200,'All']],    
    columnDefs:
      [{targets:[4,5,6], searchable:false,orderable:false}],
    columns: 
    [
      { 
        sTitle : 'Symbol', defaultContent:'', data: 'Symbol',
           'render' : function(data, type, row){
             return '<a target="_blank" href="http://www.merolagani.com/CompanyDetail.aspx?symbol=' 
             + row.Symbol + '">'+row.Symbol+'</a>';
           }
      },

      { sTitle : 'Company', data: 'Company'},        
      { sTitle: 'LTP', data : 'LTP'},      
      // {
      //   sTitle: 'LTP', render:function(data,type,row){
      //   return row.LTP + '<br><small>(' + row.trans_datef + ')</small>';
      //   }
      // },      
       { sTitle: "point", 
        render : function(data, type, row){
              diff =  parseFloat(row.Difference).toFixed(2);
              return diff;
            }
        },     
        { sTitle: "%", 
        render : function(data, type, row){                      
              diff_per = parseFloat((row.Difference / row.LTP) * 100).toFixed(2);
              return diff_per + '%';
            }
        }, 
        { sTitle: "High/Low", 
        render : function(data, type, row){                                    
              return 'High : ' + row.Max_price + '<br/>Low : ' + row.Min_price;
            }
        },
       { sTitle: 'Updated', data : 'trans_datef'},      
      // {render: function(data,type,row){
      //   return '<input id="' + row.Symbol + '" type="text" class="form-control"></input>';
      // }},
      {
        data:null, class:'watch',
        render: function(data,type, row){
          //return '<input type="checkbox" class="form-control" name="chkWatch"></input>'; 
          return '<span class="starred"></span>';
        }
      },
      ],

  });    
}

function fnApplyCSS(obj, $col)
{    
 var txt = $(obj).children().eq($col).text();     
 var num = parseInt(num);     
 if( num > 0 )
    $(obj).children().eq($col).addClass('_Success') 
 else if( num < 0 )
    $(obj).children().eq($col).addClass('_NoSuccess');
}

  //convert days to years, months and days
  function humanise (no_days) 
  {  
    var str = '';  
    var values = [[' year', 365], [' month', 30], [' day', 1]];

    // Iterate over the values...
    for (var i=0;i<values.length;i++) {
    var amount = Math.floor(no_days / values[i][1]);

    // ... and find the largest time value that fits into the diff
    if (amount >= 1) {
       // If we match, add to the string ('s' is for pluralization)
       str += amount + values[i][0] + (amount > 1 ? 's' : '') + ' ';

       // and subtract from the diff
       no_days -= amount * values[i][1];
    }    
  }
  return (str == '' ? 'a while' : str);
  }

  //difference between dates in days
  function daydiff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
  }

  $.fn.serializeObject = function()
  {
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

  function getRandomID(pre){
    pre = pre || 'nepse';
    //var _dt = new Date();
    //return pre + _dt.getMonth() + _dt.getDay() + _dt.getMilliseconds();
    return pre + microtime();
    //return uniqid(pre);
  }


  function fnFormatNumber(num) {      
      num = (num != null ? num : 0);
      return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");      
  }