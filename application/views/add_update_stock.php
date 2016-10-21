  <style>
    td.button_add:before {
      font-family: 'Glyphicons Halflings'; 
      content:"\e081";
      cursor: pointer;              
    }
    td.button_remove:before {
        font-family: 'Glyphicons Halflings'; 
        content:"\2212";
        cursor: pointer;
    }
    /*#_err_msg div {font-weight: bold; display: table-cell; padding-right:10px;}*/
    
    #region_filter {margin-left: -25px}
    #region_entry {margin-left: -15px}
    .small-box {width: 75px}
    .smaller-box {width: 55px}
    tr {margin:0px;padding: 0px;}
    tbody td {vertical-align: bottom;}

    p.small {padding-top:5px}
    td.comm span {
      border-radius: 4px;
      padding: 5px 10px;
      background-color: #f0eefc;
      margin-right: 5px;
      font-size:12px;
    }
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

<!--[if !IE]><!-->
<style>
   /* table-related media query stuff only */

table { 
width: 100%; 
border-collapse: collapse; 
}
/* Zebra striping */
/*tr:nth-of-type(odd) { 
  background: #eee; 
}*/
th { 
  background: #c6c6c6;  
  font-weight: bold; 
}
td, th { 
  padding: 6px 4px; 
  border: 0px solid #ccc; 
  text-align: left; 
}

/* 
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media 
only screen and (max-width: 463px),
(max-device-width: 463px)  {

  /* Force table to not be like tables anymore */
  table, thead, tbody, th, td, tr { 
    display: block; 
  }
  
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
  
  /*
  Label the data
  */
  td:nth-of-type(1):before { content: "Company"; }
  td:nth-of-type(2):before { content: "Quantity"; }
  td:nth-of-type(3):before { content: "Amount"; }
  td:nth-of-type(4):before { content: "Purchase date"; }
  td:nth-of-type(5):before { content: "Ownership date"; }
  td:nth-of-type(6):before { content: "Transaction No"; }
  td:nth-of-type(7):before { content: "Remarks"; }    
}
</style>

<!--<![endif]-->

  </style>

<?php

echo '<input type=hidden id=_shareholder_id value=' . (isset($obj) ? $obj->shareholder_id:0) . ' />';
echo '<input type=hidden id=_stocktype_id value=' . (isset($obj) ? $obj->stock_type_id:0) . ' />';
echo '<input type=hidden id=_group_id value=' . (isset($obj) ? $obj->GroupID:0) . ' />';
echo '<input type=hidden id=_offr_code value=' . (isset($obj) ? $obj->offr_code:0) . ' />';
?>

  <form id="MyStock">

   <div id="region_filter">
      
      <div class="col-sm-2">
          <div class="form-group">
          <label for="shareholder_id" class="control-label sr-only">Shareholder</label>
            <select class="form-control" id="selShareholder" name="shareholder_id">
            <option value='0'>- Shareholder -</option>
            </select>   
          </div>
      </div>
       <div class="col-sm-2">
          <div class="form-group">
            <label for="GroupID" class="control-label sr-only">- Group as -</label>          
            <select class="form-control" id="selShareGroup" name="GroupID" title="Grouping as">              
            </select>               
          </div>
      </div>
      <div class="col-sm-2">
          <div class="form-group">
          <label for="stock_type_id" class="control-label sr-only">Stock Type</label>
            <select class="form-control" id="selStockType" name="stock_type_id">
            <option value='0'>- Stock Type -</option>
            </select>               
          </div>
      </div>

      <div class="col-sm-2">
          <div class="form-group">
          <label for="offr_code" class="control-label sr-only">Acquired as</label>
            <select class="form-control" id="selOfferingMethod" name="offr_code">
            <option value='0'>- Acquired as -</option>
            </select>   
          </div>
      </div>    
    <div class="col-sm-1">
        <div class="form-group">  
          <label for="BrokerNo" class="control-label sr-only">Broker no</label>
          <input type="number" class="form-control" style="width:90px" name="BrokerNo" placeholder="Broker #"  value="<?=isset($obj) ? ($obj->Broker_no==0?'':$obj->Broker_no):'' ?>"></input>
        </div>
    </div>      
    <div class="col-sm-3">
      <div class="form-group col-sm-6">                  
        <button type="submit" class="form-control btn-primary" id="btnSave">Save</button>          
      </div>
      <div class="form-group col-sm-6">        
        <button type="button" class="form-control" id="btnReset">Reset</button>
      </div>
    </div>  

    </div>  <!-- end of region_filter -->

  <div class="row">
    <div id="_err_msg"></div>
  </div>

  <div id="region_entry">    
  <!-- <?php //echo isset($obj) ? '' : '<h4 class="text-danger">Can not load data. Please try again or contact the system administrator </h4>'  ?> -->
    <table>       
      <thead>
        <tr>            
            <th>Company</th>            
            <th>Quantity</th><th>Amount</th>
            <th>Purchase date</th>            
            <th  colspan="2" title="Transfer date into your DEMAT account">Owned / Date to own</th>
            <th>Trans No</th>
            <th>Remarks</th>
            <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <tr>                    
          <td>    
            <label for="Symbol" class="control-label sr-only">Company</label>
            <input type="text" class="form-control" name="Symbol" placeholder="Company" value="<?=isset($obj) ? $obj->Symbol:'' ?>"></input>            
            <input type="hidden" name="Portfolio_id" value="<?=isset($obj) ? $obj->Portfolio_id:'-1' ?>"></input>
          </td>
          <td>
              <label for="Qty" class="control-label sr-only">Quantity</label>              
              <input type="number" class="form-control" style="width:90px" name="Qty" min="1" placeholder="Qty" value="<?=isset($obj) ? $obj->Quantity:'' ?>"></input>
          </td>          
          <td>
              <label for="Amount" class="control-label sr-only">Amount</label>
              <input type="number" class="form-control" name="Amount" min="1" placeholder="Amount" value="<?=isset($obj) ? $obj->Amount:'' ?>"></input>
              <input type="hidden" readonly name="effRate"></input>
              <input type="hidden" readonly name="BROKER_comm"></input>
              <input type="hidden" readonly name="SEBON_comm"></input>
          </td>                                  
          <td>            
              <label for="Purchase_date" class="control-label sr-only">Purchased on</label>
              <input type="Date" class="form-control" name="Purchase_date"  value="<?=isset($obj) ? $obj->Purchase_date:'' ?>"></input>
          </td>
          <td>  <?php
                $checked='';
                if( isset($obj) ){
                    $checked = ($obj->Owned==1 ? 'checked' :'');                    
                }
                ?>
                <input type="checkbox" <?=$checked ?> title="The share is actualized"></input>
                <input type="hidden" name="owned"  value="<?=isset($obj) ? $obj->Owned:0 ?>"></input>
          </td>
          <td>
                <label for="Ownership_date" class="control-label sr-only">Ownership date</label>
                <input readonly="true" type="Date" class="form-control" name="Ownership_date" title="enter date when this share will be actualized"></input>              
          </td>
          <td>
            <label for="TransNo" class="control-label sr-only">Trans #</label>
            <input type="text" class="form-control" name="TransNo" placeholder="Transaction #" value="<?=isset($obj) ? $obj->Transaction_no:'' ?>"></input>
          </td>
          <td>
            <label for="Remarks" class="control-label sr-only">Remarks</label>
            <input type="text" class="form-control" name="Remarks" placeholder="Remarks" value="<?=isset($obj) ? $obj->Remarks:'' ?>"></input>            
          </td>
          <td class="button_add" style="width:25px;">&nbsp;</td>
      </tr>

      </tbody>
    </table>

</div> <!-- end region_entry -->    
</form>

<script type="text/javascript">

$(document).ready(function(){
  //check the _portfolio_id hidden input to check if the form is in edit mode or add mode.
  //In edit mode, hide the plus button. Only single record to be edited at a time.
  var $mode = $('input[name="Portfolio_id"]').val();
  if ( $mode != '-1' ) //ie, in edit mode (asssumption -1 for add mode, portfolio id in edit mode)
    $('#region_entry table').find('tbody td:nth-child(9)').removeClass();
})

$('#btnReset').click(function(){
  $('#_err_msg').empty();
  $('#region_entry tbody tr').each(function(i,row){
    if (i > 0)
      $(row).remove();
  })
  $('input').val('');
  $('textarea').val('');
})

$tmp = $('input[name=Purchase_date]').val();
if($tmp=='') $('input[name=Purchase_date]').val( getDate() );
$tmp = $('input[name=Ownership_date]').val();
if($tmp=='') $('input[name=Ownership_date]').val( getDate() );

function getDate(delay_days=0){
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

$("form").validate({
    errorLabelContainer : '#_err_msg',
    wrapper: "li",    
    submitHandler : function(form) { fnSaveData() },
    rules: {
        Symbol: { required: true},
        Qty: { required: true, number:true, min: 1  },
        Amount : { required: true, number: true, min: 1 },
        BrokerNo : { number: true, min: 1 },
        shareholder_id : {SelectShareholder:true},
        stock_type_id : {SelectStockType:true},
        offr_code : {SelectAcquired:true},
        Purchase_date : {date:true}
    },
    messages : {
      Symbol : {required: "Choose a <mark>Company</mark>"},
      Qty : {required: "Enter <mark>Quanity</mark>", number: "Enter Quantity as number ", min : "Quantity should be greater than zero "},
      Amount : {required: "Enter <mark>Amount</mark>", number: "Enter Amount as number ", min : "Amount should be greater than zero "},      
      BrokerNo : {number: "Enter Broker No as number ", min : "Broker No should be greater than zero "},
      Purchase_date : {date: "Enter valid Purchase date"}
    }
}); 

jQuery.validator.addMethod('SelectShareholder', function (value) {
    return (value != '0');
}, "<mark>Shareholder name</mark> is required"); 
jQuery.validator.addMethod('SelectStockType', function (value) {
    return (value != '0');
}, "<mark>Stock type</mark> is required"); 
jQuery.validator.addMethod('SelectAcquired', function (value) {
    return (value != '0');
}, "<mark>Acquired method</mark> is required"); 

</script>
<script type="text/javascript" src="<?=base_url();?>assets/all/js/new_stock.js"></script>