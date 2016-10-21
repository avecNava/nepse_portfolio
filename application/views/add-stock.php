  <style>
    
    td.button_add:before {
      font-family: 'Glyphicons Halflings'; 
      content:"\e081";
      cursor: pointer;              
    }

    td.button_remove:before {
        font-family: 'Glyphicons Halflings';         
        cursor: pointer;
        content:"\e014";
    }

    div#region_filter { display: table; margin-bottom: 15px}
    div#region_filter div {
    width: auto;
    float: left;
    margin: 2px 5px;
    }

    #region_entry table {width:100%;}
    tr {margin:0px;padding: 0px;}
    tbody td {vertical-align: bottom;}

    p.small {padding-top:5px}

    input[type=checkbox]
    {
        /* Double-sized Checkboxes */
        -ms-transform: scale(1.5); /* IE */
        -moz-transform: scale(1.5); /* FF */
        -webkit-transform: scale(1.5); /* Safari and Chrome */
        -o-transform: scale(2); /* Opera */
        padding: 10px;
        margin:10px 0px;
    }    

  #_err_msg { color: #c75f5f;    font-size: small;  }
  #_err_msg span{ padding:5px;  margin-right: 5px;  }  
  .td_notify div {padding:3px; margin: 1px 3px; display: inline-block; font-size: small;}

  <!--[if !IE]><!-->
  /* table-related media query stuff only */
  table { width: 100%; border-collapse: collapse; }
  /* Zebra striping */
  /*tr:nth-of-type(odd) {   background: #eee; }*/
  th { background: #eee;  font-weight: bold;  color: #592626; font-size: small; }
  td, th { padding: 6px 4px; border: 0px solid #ccc; text-align: left; }

  /* Max width before this PARTICULAR table gets nasty. This query will take effect for any screen smaller than 760px and also iPads specifically.*/
  @media only screen and (max-width: 463px), (max-device-width: 463px)  
  {

    /* Force table to not be like tables anymore */
    table, thead, tbody, th, td, tr { display: block;   }
    
    /* Hide table headers (but not display: none;, for accessibility) */
    thead tr { 
      position: absolute;
      top: -9999px;
      left: -9999px;
    }
    
    tr { border: 1px solid #ccc; }
    
    td { 
      /* Behave  like a "row" */
      border: none;
      border-bottom: 1px solid #eee; 
      position: relative;
      padding-left: 50%; 
    }

    td.td_notify {padding-left: 0%}
    
    td:before { 
      /* Now like a table header */
      position: absolute;
      /* Top/left values mimic padding */
      top: 6px;
      left: 6px;
      width: 45%; 
      padding-right: 10px; 
      white-space: nowrap;
    }

    /*  Label the data  */
    td:nth-of-type(1):before { content: ""; }
    td:nth-of-type(2):before { content: "Transaction no"; }
    td:nth-of-type(3):before { content: "Company"; }
    td:nth-of-type(4):before { content: "Quantity"; }
    td:nth-of-type(5):before { content: "Rate"; }    
    td:nth-of-type(6):before { content: "Eff. rate"; }
    td:nth-of-type(7):before { content: "Total payable"; }
    td:nth-of-type(8):before { content: "Purchase date"; }
    td:nth-of-type(9):before { content: "Remarks"; }    
}

<!--<![endif]-->

</style>

<?php

echo '<input type=hidden id=_shareholder_id value=' . (isset($obj) ? $obj->shareholder_id:0) . ' />';
echo '<input type=hidden id=_stocktype_id value=' . (isset($obj) ? $obj->stock_type_id:0) . ' />';
echo '<input type=hidden id=_group_id value=' . (isset($obj) ? $obj->GroupID:0) . ' />';
echo '<input type=hidden id=_offr_code value=' . (isset($obj) ? $obj->offr_code:0) . ' />';
?>

  <form id="MyStock" method="POST">

   <div id="region_filter">
      
          <div class="form-group">
          <label for="shareholder_id" class="control-label sr-only">Shareholder</label>
            <select class="form-control" id="selShareholder" name="shareholder_id">
            <option value='0'>- Shareholder -</option>
            </select>   
          </div>
          <div class="form-group">
            <label for="GroupID" class="control-label sr-only">Group as</label>          
            <select class="form-control" id="selShareGroup" name="GroupID" title="Grouping as">              
            <option value='0'>- Group as -</option>
            </select>               
          </div>
          <div class="form-group">
          <label for="stock_type_id" class="control-label sr-only">Stock Type</label>
            <select class="form-control" id="selStockType" name="stock_type_id">
            <option value='0'>- Stock Type -</option>
            </select>               
          </div>
          <div class="form-group">
          <label for="offr_code" class="control-label sr-only">Acquired as</label>
            <select class="form-control" id="selOfferingMethod" name="offr_code">
            <option value='0'>- Acquired as -</option>
            </select>   
        </div>
      
      <div class="form-group">  
          <label for="BrokerNo" class="control-label sr-only">Broker no</label>
          <input type="number" class="form-control" name="BrokerNo" placeholder="Broker #"  value="<?=isset($obj) ? ($obj->Broker_no==0?'':$obj->Broker_no):'' ?>"></input>
      </div>
      <div class="form-group">                  
        <button type="submit" class="form-control btn-primary" id="btnSave">Save</button>          
      </div>
      <div class="form-group">        
        <button type="button" class="form-control" id="btnReset">Reset</button>
      </div>

    </div>  <!-- end of region_filter -->

  
  <div id="_err_msg"></div>  

  <div id="region_entry">    
  <!-- <?php //echo isset($obj) ? '' : '<h4 class="text-danger">Can not load data. Please try again or contact the system administrator </h4>'  ?> -->
    <table>       
      <thead>
        <tr>            
            <th style="display:none"></th>
            <th title="Transaction number">Trans. no</th>
            <th title="Please select a company">Company</th>            
            <th title="Total shares purchased">Quantity</th>
            <th title="Rate per share">Rate</th>
            <th title="Effective rate calculated">Eff. Rate</th>
            <th title="Total payable (includes Broker and SEBON commission)">Total payable</th>            
            <th>Purchase date</th>                        
            <th>Remarks</th>
            <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>      
          <td style="display:none"></td>      <!-- donot delete this line. Used to provide fix for mobile view-->
          <td>
            <label for="TransNo" class="control-label sr-only">Trans #</label>
            <input type="text" class="form-control" name="TransNo" placeholder="Transaction #" value="<?=isset($obj) ? $obj->Transaction_no:'' ?>"></input>
          </td>             
          <td>    
            <label for="Symbol" class="control-label sr-only">Company</label>
            <input type="text" class="form-control" name="Symbol" placeholder="Company" value="<?=isset($obj) ? $obj->Symbol:'' ?>"></input>
            <input type="hidden" name="Portfolio_id" value="<?=isset($obj) ? $obj->Portfolio_id:'-1' ?>"></input>
          </td>
          <td>
              <label for="Qty" class="control-label sr-only">Quantity</label>              
              <input type="number" class="form-control" name="Qty" min="1" placeholder="Quantity" value="<?=isset($obj) ? $obj->Quantity:'' ?>"></input>
          </td>          
          <td>
              <label for="Rate" class="control-label sr-only">Rate</label>
              <input type="number" class="form-control" name="Rate" placeholder="Rate" value="<?=isset($obj) ? $obj->Rate:'' ?>"></input>
              <input type="hidden" readonly name="BROKER_comm"></input>
              <input type="hidden" readonly name="SEBON_comm"></input>
          </td>                                  
          <td>
            <input readonly class="form-control" type="number" name="effective_rate" placeholder="Eff. rate"></input>
          </td>
          <td>
            <input readonly class="form-control" type="number" id="_total_payable" placeholder="Total Payable"></input>
          </td>
          <td>            
              <label for="Purchase_date" class="control-label sr-only">Purchased on</label>
              <input type="Date" class="form-control" name="Purchase_date"  value="<?=isset($obj) ? $obj->Purchase_date:'' ?>"></input>
          </td>
          
          <td>
            <label for="Remarks" class="control-label sr-only">Remarks</label>
            <input type="text" class="form-control" name="Remarks" placeholder="Remarks" value="<?=isset($obj) ? $obj->Remarks:'' ?>"></input>            
          </td>
          <td class="button_add">&nbsp;</td>
      </tr>

      </tbody>
    </table>

</div> <!-- end region_entry -->    
</form>

<script type="text/javascript" src="<?=base_url();?>assets/all/js/new_stock.js"></script>
<script type="text/javascript">

$(document).ready(function(){
  //check the _portfolio_id hidden input to check if the form is in edit mode or add mode.
  //In edit mode, hide the plus button. Only single record to be edited at a time.
  var $mode = $('input[name="Portfolio_id"]').val();
  if ( $mode != '-1' ) //ie, in edit mode (asssumption -1 for add mode, portfolio id in edit mode)
    $('#region_entry table').find('tbody td:nth-child(9)').removeClass();
})

//handle null dates
$tmp = $('input[name=Purchase_date]').val();
if($tmp=='') $('input[name=Purchase_date]').val( getDate() );
$tmp = $('input[name=Ownership_date]').val();
if($tmp=='') $('input[name=Ownership_date]').val( getDate() );

function getDate(delay_days){
  delay_days = delay_days || 0 ;
  var now = new Date();
  day = now.getDate() + Number(delay_days);
  var day = ("0" + day).slice(-2);  
  var month = ("0" + (now.getMonth() + 1)).slice(-2);   //Extract the last 2 character:
  var today = now.getFullYear()+"-"+(month)+"-"+(day) ;
  return today;
}

//when plus icon is created to create new row
$('#region_entry > table tbody')
  .on('click','td.button_add', function(){      
      var tr = $(this).closest('tr');
      var new_row = $(tr).html();
      $('<tr>',{ html : new_row, class:'row' + $('#region_entry tr').length }).appendTo( 'table > tbody' );
      $('tr:last-child').find('td:last-child').removeClass('button_add').addClass('button_remove');
      $('tr').find('input[name=Purchase_date]').val( getDate() );
      $('input[name=BrokerNo]').val ( $('input[name=BrokerNo]:eq(0)').val() );

  })
 
 $('#region_entry > table tbody')
  .on('click','td.button_remove',function(){      
    if(confirm('Are you sure to remove this row?')) 
    {
      var _row = $(this).closest('tr');
      $(_row).remove();
    }
  })

$('#btnReset').click(function(){  
  $('#_err_msg').empty();
  $('#region_entry tbody tr').each(function(i,row){
    if (i > 0)
      $(row).remove();
  }) //END each
  $('input').val('');
  $('textarea').val('');
  $('input[name=Purchase_date]').val(getDate());
  $('input[name="Portfolio_id"]').val(-1);
})

//when save ie, when the form is submitted 1. Validate form 2. Save form data
$("form").validate({
    errorLabelContainer : '#_err_msg',
    wrapper: "span",    
    submitHandler : function(form) { fnSaveData() },
    rules: {
        Symbol: { required: true},
        Qty: { number:true },
        Amount : { required: true, number: true, min: 1 },
        BrokerNo : { number: true, min: 1 },
        shareholder_id : {SelectShareholder:true},
        stock_type_id : {SelectStockType:true},
        offr_code : {SelectAcquired:true},
        Purchase_date : {date:true}
    },
    messages : {
      Symbol : {required: "Company is required"},
      Qty : {number: "Enter Quantity as number"},
      Amount : {required: "Amount is required", number: "Enter Amount as number", min : "Amount should be greater than zero "},      
      BrokerNo : {number: "Broker no should be number", min : "Broker no should be greater than zero "},
      Purchase_date : {date: "Enter a valid date for Purchase date"}
    }
}); 

jQuery.validator.addMethod('SelectShareholder', function (value) {
    return (value != '0');
}, "Shareholder name is required"); 
jQuery.validator.addMethod('SelectStockType', function (value) {
    return (value != '0');
}, "Stock type is required"); 
jQuery.validator.addMethod('SelectAcquired', function (value) {
    return (value != '0');
}, "Acquired method is required"); 

</script>
