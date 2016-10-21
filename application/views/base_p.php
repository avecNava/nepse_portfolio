<html>
<head>
	  <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">		
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>assets/all/css/signin.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <!--<link href="<?php echo base_url(); ?>assets/bs/css/bootstrap.min.css" rel="stylesheet">      
    <link href="<?php echo base_url(); ?>assets/all/jquery-ui.css" rel="stylesheet">  
    <link href="<?php echo base_url(); ?>assets/all/jquery.dataTables.min.css" rel="stylesheet">
    -->
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/t/bs-3.3.6/jq-2.2.0,jszip-2.5.0,dt-1.10.11,b-1.1.2,b-html5-1.1.2,b-print-1.1.2/datatables.min.css"/>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/smoothness/jquery-ui.css" />    
    

    <style>

      body { padding-top: 2px; font-size: 16px; font-family: 'Open Sans', sans-serif;}
      .smaller {font-size:10px; font-weight: normal;}
      .small {  font-size: 12px; }  /* 75% of the baseline */
      .medium {  font-size: 16px; }  /* 100% of the baseline */
      .large {  font-size: 20px; }  /* 125% of the baseline */
      .navbar-default {background-color: #607D8B; border-color: #f9f7f8;}
      .navbar-default .navbar-brand { color: #ffffff }
      .navbar-default .navbar-nav>li>a { color: #f5f5f5 }
      .navbar-nav .open .dropdown-menu {
        background-color: #fff;
      }
      .figure {
        font-family: 'Open Sans', sans-serif;
        color:#fff;
        font-size: 25px;
        padding:5px;  
        text-align: center;    
        display: block;
      }
      div#region_summary { margin-top: -15px;   }

      #region_summary h4 {
        font-family: 'Open Sans', sans-serif;
        color:#fff;
        font-size: 20px;
        z-index: 2;        
        position: relative;
    
      }
      
      #region_summary li 
      { 
        border-radius: 5px; 
        position: relative;;
        overflow: hidden;        
      }

      #region_summary li img
      {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: auto;
        opacity: 0.5; 
      }

      @media (max-width:435px) /* The maximum width for the mobile device version. */
      {
        #region_summary li { display:table-cell; }
        #region_summary li h4 {font-size:14px;}
        #region_summary li .figure {font-size:14px; font-weight: bold;}
      }

      .table > tbody > tr > td {vertical-align: middle; text-align: right;}
      .table > thead > tr > th {vertical-align: middle; text-align: right;}      
      
      #stock_summary_desktop td div { list-style-type: none; padding: 0px; margin:0px;}            
      #stock_summary_desktop td:nth-child(1), td:nth-child(2) {text-align: left !important}      
      #stock_summary_desktop td:nth-child(2) {max-width:25px;}
      #stock_summary_desktop td:nth-child(9) div {min-width: 200px}   /*width of Overall Gain*/      
      #stock_summary_desktop td:nth-child(10) div {min-width: 150px}   /*width of High Low*/

      td span.x {  display: none;  font-size: x-small;    color: #f9f9f7;    padding: 0px 2px;    background-color: #b3b3b3;}
      li._Success {background-color: #3F943F; font-weight: normal; border-right:1px solid #fff; color:#fff;}
	    li._NoSuccess {background-color: #FF3333; color:#fff; font-weight: normal; border-right:1px solid #fff }
	    ._Success { color: #03B703; font-weight: normal; border-right:1px solid #fff;}
	    ._NoSuccess { color:#FF3333; font-weight: normal; border-right:1px solid #fff }      
      .style1 { background-color: #E89B27; color: #fff; }
      .style2 { background-color: #885246; color: #fff; }      
	    .up {color:#03B703; }
	    .down{ color:#FF3333; }
	    td.details-control:before {
        font-family: 'Glyphicons Halflings'; 
        content:"\e081";
	      cursor: pointer;
        min-width:15px;
        color:#bd5b59;
	    }
	    tr.shown td.details-control:before {
          font-family: 'Glyphicons Halflings'; 
          content:"\2212";
          min-width:15px;
          color:#bd5b59;
	    }
	    div#notify {margin:5px 15px; padding:2px 5px;}	    
	    .ui-helper-hidden-accessible { display:none; }    /*hide jquery ui text-autocompletion notification messages*/   
      #stock_summary th {vertical-align: bottom }
      .tab-content > div {margin:1px}      
      #stock_summary_paginate {float:left; }
      
      div.dataTables_length {display: inline-block;}
      div.dataTables_length select {
        width: 75px;
        display: inline-block;
        margin-left: 15px;        
      }      
      /*search text box in the datatable*/
      div.dataTables_filter {
        display: inline-table;        
        margin-right: 15px;
        margin-left: -15px;
      }      
      .myclass1 { float: right; width: 120; padding: 0px; }      
      
      span.dx {padding: 10px; display:block; margin:-7px;}
      hr {margin:5px 0px; border-top: 1px ridge;}
      td,th {font-family: 'Open Sans', sans-serif;}
      div.glyphicon > span {padding: 5px; font-family: 'Open Sans', sans-serif;}
      span.glyphicon {cursor: pointer;}
      .popover-content div {margin:10px 15px}
/*      .clear {clear:both;}
      .form-group div {
    position: absolute;
    top: 25px;    
    left: 0px;
}
.form-group {    
    position: relative;
    min-width: 100;
    border: 1px solid red;
    margin-right: 10px;
}*/
      .ui-menu-item {padding:5px; font-family: 'Open Sans', sans-serif;}
    </style>

	<script>
	    var site_url = "<?php echo site_url()?>"; //Don't forget the extra semicolon! Used for $.ajax / $.post
	</script>

	<title><?=$page_title ?></title>
  
</head>

<body>    

<div class="container">

	<nav class="navbar  navbar-default">

	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="<?=site_url()?>/portfolio">NEPSE Portfolio</a>	      
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">      
<!-- 	      <form class="navbar-form navbar-left" role="search">
	        <div class="form-group">
	          <input type="text" class="form-control" placeholder="Search">
	        </div>
	        <button type="submit" class="btn btn-default">Submit</button>
	      </form> -->
        
	      <ul class="nav navbar-nav navbar-right">        

          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Portfolio<span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="<?=site_url()?>/portfolio"><strong>Dashboard</strong></a></li>
              <li><a href="<?=site_url()?>/portfolio/addstock">Add new</a></li>
              <li><a href="<?=site_url()?>/portfolio/watchlist">Watchlist</a></li>
              <li><a id="btnSellStock" href="<?=site_url()?>/main/sale">Sell</a></li>
              <li><a id="btnSellStock" href="<?=site_url()?>/main/pl">P/L account</a></li>
            </ul>
          </li>

          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">NEPSE<span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="<?=site_url()?>/main/rate">Market rate</a></li>              
              <li><a href="<?=site_url()?>/main/company">Listed companies</a></li>
            </ul>
          </li>

	        <li class="dropdown">
	          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Account<span class="caret"></span></a>
	          <ul class="dropdown-menu">
	            <li>
                  <a href="<?=site_url()?>/portfolio/account">My account
                  <div><?=$this->session->userdata('email');?></div>
                  </a>
              </li>
              <!-- <li><a href="#">Stock groups</a></li> -->
	            <li role="separator" class="divider"></li>	            
              <li><a href="<?=site_url()?>/account/cp">Change password</a></li>
	            <li><a  href="<?=site_url()?>/account/logout">Go out</a></li>
	          </ul>
	        </li>
	      </ul>
	    </div> <!-- /.navbar-collapse -->  
	</nav>

<script src="https://cdn.datatables.net/t/bs-3.3.6/jq-2.2.0,jszip-2.5.0,dt-1.10.11,b-1.1.2,b-html5-1.1.2,b-print-1.1.2/datatables.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"   integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="   crossorigin="anonymous"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>  

<?php $this->load->view($view_name) ?>

</div>  <!-- end div container -->

</body>
</html>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


<!-- <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>  -->
<!--<script src="<?=base_url();?>assets/all/jquery-1.11.1.min.js"></script>
<script src="<?=base_url();?>assets/all/jquery.dataTables.min.js"></script>
<script src="<?=base_url();?>assets/all/js/jquery-ui.min.js"></script> -->


<!-- <script src="<?=base_url();?>assets/all/js/portfolio.js"></script> 
<script src="<?=base_url();?>assets/all/js/main.js"></script>  -->

<script type="text/javascript">
// Load the Visualization API and the piechart package.
google.charts.load('current', {'packages':['corechart','table','controls']});

</script>