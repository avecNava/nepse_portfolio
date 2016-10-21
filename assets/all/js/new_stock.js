  fnLoadStockType();
  fnLoadShareholder();
  fnLoadStockGroup();
  fnLoadStockOffering();

   //loads the company symbols (this appears as drop down when adding new stocks)
  $.post(site_url + '/portfolio/getSymbols',     
   function(result) 
   {         
      var arr_of_companies=[];
      $.each(result, function(index, value) {  
        arr_of_companies[value.Symbol] =  value.Company;
        arr_of_companies.push({label : value.Company, value : value.Symbol});        
      });   

      $('#region_entry > table').on('click','tr input[name=Symbol]',function(){
        
          var row = $(this).closest('tr');  
          $(this).autocomplete({ 
              source : arr_of_companies,
              select: function(event, ui) {
                $(row).find('input[name=Symbol]').val(ui.item.value);             
            },
            focus: function(event, ui) {
              $(row).find('input[name=Symbol] ').val(ui.item.value);          
            }
          })   //autocomplete
      })  //onChange
      
    }  
    ,"json"  //json data
    );

  //when amount textbox looses focus, calculate the effective rate , sebon and broker commission.
  $('#region_entry > table').on('blur','input[name=Rate]', function()
    {   
        var row = $(this).closest('tr');
        $('#_err_msg').empty();
        $(row).find('input[name=effective_rate]').val('');    
        $(row).find('input[id=_total_payable]').val('');  
        
        //only get commission if acquired method is SECONDARY(SCD_MARKET) 
        var $commission_applicable =  ['SCD_MARKET'];        
        var $acquired_as = $('#selOfferingMethod').val();     //offering method choosen
        //if not matched to $commission applicable array, no need to get commission
        if ( $commission_applicable.indexOf($acquired_as) == -1 ){   //returns -1 if not matched
          var qty = $(row).find('input[name=Qty]').val();
          var rate = $(row).find('input[name=Rate]').val();          
          $(row).find('input[name=effective_rate]').val( rate );    
          $(row).find('input[id=_total_payable]').val( rate * qty );          
          var data = row.next().find('td.td_notify').text(); //if commission is already displayed, empty it 
          if ( data.length > 0 ) row.next().empty();
          return false;
        }

        //only calculate effective rate and commission when offering method= SECONDARY_MARKET  
        var qty = $(row).find('input[name=Qty]').val();
        var rate = $(row).find('input[name=Rate]').val();
        var amount = $(row).find('input[name=Amount]').val();  
        var stock_type_id = $('#selStockType').val();
        var offr_code = $('#selOfferingMethod').val();    
        var action = 'BUY';    
        var _total = (qty * rate).toFixed(2);

        var DTO = {qty:qty, amount : _total, stock_type_id : stock_type_id, action : action, offr_code : offr_code};

        var rate = $('input[name=Rate]');

        $.ajax({
          url: site_url + '/portfolio/get_effective_rate',
          type: 'POST',
          contentType: 'application/json',
          dataType: 'json',
          data: JSON.stringify(DTO),
          success : 
          function( result ) 
          {
            if( JSON.stringify(result) != '{}' )   //if result is not null
            {
              //var _total_payable = _total + result['BROKER_comm_rs'] + result['SEBON_comm_rs'];     //net payable including commission              
              //$(row).find('#_effective_rate').val( result['EFF_RATE_rs'] );
              $(row).find('#_total_payable').val( result['TOTAL_PAYABLE_rs'] );
              $(row).find('input[name=effective_rate]').val( result['EFF_RATE_rs'] );
              $(row).find('input[name=BROKER_comm]').val( result['BROKER_comm_rs'] ).attr('title','Broker commission @ ' + result['BROKER_comm_per'] + '%');
              $(row).find('input[name=SEBON_comm]').val( result['SEBON_comm_rs'] );

              var temp = 
                "<div><span title='Effective rate'>Effective rate: Rs " + result['EFF_RATE_rs'] + "</span></div>" +
                "<div><span class='Broker commission'>Broker : Rs " + result['BROKER_comm_rs'] + " @ <mark> " + result['BROKER_comm_per'] + "% </mark>"+ "</span></div>" +
                "<div><span class='SEBON commission'>SEBON : Rs " + result['SEBON_comm_rs'] + "</span></div>";
           } else 
              temp = '<div>Commissions not defined for this category of share</div>';
            
          var data = row.next().find('td.td_notify').text(); //if commission is already displayed, empty it 
          if ( data.length > 0 ) row.next().empty();
          _new_row = row.after('<tr><td colspan=7 class="td_notify"><b>***</b>' + temp + '</td></tr>').slideDown();

          }   //result
          })  //ajax
    
    }) //onBlur

  //$('button#btnSave').submit(function(){
  function fnSaveData(){
    
    var fields = {};
    fields.shareholder_id = 'Shareholder';
    fields.stock_type_id ='Stock Type';
    fields.offr_code = 'Acquired type';
    fields.GroupID ='Group Name';

    var DTO = JSON.stringify( $('#MyStock').serializeObject() );         

    $.ajax({
      type : 'POST',
      url  :  site_url + '/portfolio/save_data_arr',
      data :  DTO ,
      dataType : "text",      //expect back from server  (json, html, text)
      contentType: "application/json",  //data type sending to server
      success : 
              function(rows)
              {
                
                $('#_err_msg').empty();
                if( rows > 0 ){
                    $('#btnReset').trigger('click');        //reset input
                    $("<div/>",{class:'text-success',text:'Data saved successfully. ' + rows +' records added.'}).appendTo('#_err_msg');
                    $('input[name="Portfolio_id"]').val(-1);
                }
                else
                  $("<div/>",{class:'text-danger',text:'Could not save data this time.'}).appendTo('#_err_msg');
                  
                $('#_err_msg').slideDown();
              },
      error : 
            function(requestObject, error, errorThrown){          
              $("<div/>,{class='text-danger',text:'" + errorThrown +"'}").appendTo('#_err_msg');
            }        
    });     
}  //fnSave Data

function fnLoadStockType()
{
  $.post( site_url + '/portfolio/getStockTypes', 
    function(result) {
       $.each(result, function(index, value) 
       {   
          $("#selStockType").append('<option value="'+value.stock_type_id+'">'+value.stock_type+'</option>')
       });
       $('#selStockType').val( $('#_stocktype_id').val() ).prop('selected',true);
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
          $('#selShareholder').val( $('#_shareholder_id').val() ).prop('selected',true);
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
            $('#selShareGroup').val( $('#_group_id').val() ).prop('selected',true);
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
            $('#selOfferingMethod').val( $('#_offr_code').val() ).prop('selected',true);
          });
    }  ,"json"  //json data
    );
}

  //when offering method is Dividend, disable amount input
  $('#selOfferingMethod').change(function(){
    var offr_code = $(this).val();    //get the selected value
    if(offr_code=='DIVIDEND')
      $('input[name=Rate]').attr('disabled','disabled');
    else
      $('input[name=Rate]').removeAttr('disabled');
  });

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
  };