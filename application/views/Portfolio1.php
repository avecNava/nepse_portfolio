<style type="text/css">  
  ._mobile {display: none;}
  @media (max-width:435px) /* The maximum width for the mobile device version. */
  {
   ._desktop {display: none;}
   ._mobile {display: table-cell;}
   #stock_summary_desktop_filter {display: none;}
   #stock_summary_desktop_length {display: none;}
   div#region_filter {display: none}    /*hide filters from mobile*/
   .myclass1 {display: none;}
  }
  
  #stock_summary_mobile tr.odd {  background: #eee;  }
  #stock_summary_mobile tr.even {  background: #fff;  }
  tbody>tr:nth-of-type(odd) {      background-color: #ffffff;  }
  tbody>tr:nth-of-type(even) {      background-color: #f9f9eb;  }  
  div#MyStock {    margin-left: -15px;    margin-right: -15px;  }
  span.glyphicon {
    padding: 5px;
    background-color: #f7f7f7;
    margin: 0px 1px;
    border-radius: 5px;
    border: 1px solid #f8f8f8;
  }
  i {margin-right: 3px }    
  span.glyphicon:hover {background-color: #ddd} 
  #_transChart div#fltr{font-size:12px;  }
  #_transChart button{padding:3px;  font-size: 12px; }
  #_transChart button:hover{  text-decoration: none;  background-color: #f9f9f9;}
  /*#region_summary {margin-bottom: 5px;}*/
  #stock_summary_desktop th {  font-size: small; }
  
  /*css to format region_filter controls */
  #region_filter {
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fffcfc;
    display: table;
    padding: 2px;
    width:100%;  
  }
  #region_filter ol { padding: 0px; margin:0px; list-style: none; }
  #region_filter ol li {
      margin:5px;
      float:left;
  }
  #region_filter li label {
    /*width: auto;
    padding:0px 5px;*/
    font-size: small;
    color: #b1a5a5;
  }
  /*.clear {clear:both;}*/
  .form-group {margin-bottom: 0px;}
  /*#region_filter li div:nth-child(2) {display: inline-block;}  */
  .form-group input, .form-group select {width:90px; }
  

</style>

<div id="region_summary" class="hidden">
   <ul class="list-group">
        <li class="list-group-item col-xs-6  col-sm-6 col-md-6 col-lg-3 style2">
        <span class="figure" id="Worth"></span>            
        <h4 class="text-center">Market Value</h4>
        <img src="../assets/all/images/blue_abstract_background.jpg">
        </li>          

        <li class="list-group-item col-xs-6  col-sm-6 col-md-6 col-lg-3">
        <span class="figure" id="OverallGain"></span>
        <h4 class="text-center">Overall Gain</h4>
        <img src="../assets/all/images/blue_abstract_background.jpg">
        </li>

        <li class="list-group-item col-xs-6  col-sm-6 col-md-6 col-lg-3">
        <span class="figure" id="DayGain"></span>
        <h4 class="text-center">Day Gain</h4>
        <img src="../assets/all/images/blue_abstract_background.jpg">        
        </li>
        
        <li class="list-group-item col-xs-6 col-sm-6 col-md-6 col-lg-3 style1">
        <span class="figure" id="Investment"></span>
        <h4 class="text-center">Investment</h4>
        <img src="../assets/all/images/blue_abstract_background.jpg">
        </li>        
    </ul>
    <span id="trans_date" class="small"> </span>    
</div> <!-- end of region summary -->
   
  <div  id="region_filter"> 
  <form id="MyStock" >
        <ol>
        <li>
          <div class="form-group">
          <label for="Symbol" class="control-label">Company</label>
          <div>
                <input type="text" class="form-control" id="Symbol" name="Symbol" />
                <input type="hidden" class="form-control" id="_Symbol" />   
          </div>
        </div>
 </li>
 <li>
        <div class="form-group">
          <label for="Shareholder" class="control-label">Shareholder</label>
          <div>
                <select class="form-control" id="selShareholder" name="shareholder_id">
                <option value='0'>Any</option>
                </select>   
          </div>
        </div>
</li>
<li>
        <div class="form-group">
          <label for="selStockType" class="control-label">Type</label>
          <div>
                <select class="form-control" id="selStockType" name="stock_type_id">
                <option value='0'>Any</option>
                </select>   
          </div>
        </div>
</li>
<li>
        <div class="form-group">
          <label for="offr_code" class="control-label">Acquired</label>
          <div>
                <select class="form-control" id="selOfferingMethod" name="offr_code">
                <option value='0'>Any</option>
                </select>   
          </div>
        </div>
</li>
<li>
        <div class="form-group">
          <label for="ShareSubCategory" class="control-label">Group</label>
          <div>
                <select class="form-control" id="selShareGroup" name="GroupID">
                <option value='0'>Any</option>
                </select>   
          </div>
        </div>
 </li>
        </ol>

        </form>
    </div>  <!-- end of region_filter -->    
    <br class="clear" />


<div class="table-responsive col-sm-12">
       <div id="MyStock">
        <!-- '','Symbol', 'Qty', 'Rate','Price','Change','Day Gain','Overall Gain' -->
        <table id="stock_summary_desktop" class="table table-striped">
       <!--      <thead>
              <tr> 
                <th></th>                
                <th>Company</th>
                <th>Quantity</th>
                <th>Buy Rate</th>                                
                <th>LTP</th>
                <th>Change</th>                
                <th>Change %</th>  
                <th>Overall gain %</th>  
                <th>High/Low</th>
                <th>Gain</th>                
                <th>52 wks high/low</th>                
                <th>Investment</th>
                <th>Market value</th>
              </tr>                   
            </thead>  -->       
        </table>      
      </div>
</div>
<script src="<?=base_url();?>assets/all/js/portfolio.js"></script> 
<script src="<?=base_url();?>assets/all/js/main.js"></script> 
