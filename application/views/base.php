<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">		
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/t/bs-3.3.6/jq-2.2.0,jszip-2.5.0,dt-1.10.11,b-1.1.2,b-html5-1.1.2,b-print-1.1.2/datatables.min.css"/>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/smoothness/jquery-ui.css" /> 
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <!--    
    <link href="<?php echo base_url(); ?>assets/bs/css/bootstrap.min.css" rel="stylesheet">  
    <link href="<?php echo base_url(); ?>assets/all/jquery-ui.css" rel="stylesheet">  
    <link href="<?php echo base_url(); ?>assets/all/jquery.dataTables.min.css" rel="stylesheet">
	-->
	
	<title><?=$page_title ?></title>
	<style type="text/css">
	  body { padding-top: 2px; font-size: 16px; font-family: 'Open Sans', sans-serif;}
	  .smaller {font-size:10px; font-weight: normal;}
	  .small {  font-size: 12px; }  /* 75% of the baseline */
	  .medium {  font-size: 16px; }  /* 100% of the baseline */
	  .large {  font-size: 20px; }  /* 125% of the baseline */
	  .navbar-default {background-color: #607D8B; border-color: #f9f7f8;}
      .navbar-default .navbar-brand { color: #ffffff }
      .navbar-default .navbar-nav>li>a { color: #f5f5f5 }
	</style>
	<script>
	    var site_url = "<?php echo site_url()?>"; //Don't forget the extra semicolon! Used for $.ajax / $.post
	</script>
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
	
 <!--
<script src="<?=base_url();?>assets/all/jquery-1.11.1.min.js"></script>
<script src="<?=base_url();?>assets/all/jquery.dataTables.min.js"></script>
<script src="<?=base_url();?>assets/all/js/jquery-ui.min.js"></script>
<script src="<?=base_url();?>assets/all/js/jquery.validate.js"></script>
-->

<script src="https://cdn.datatables.net/t/bs-3.3.6/jq-2.2.0,jszip-2.5.0,dt-1.10.11,b-1.1.2,b-html5-1.1.2,b-print-1.1.2/datatables.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"   integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="   crossorigin="anonymous"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>  

<?php $this->load->view($view_name) ?>

</div>  <!-- end div container -->
<script src="<?=base_url();?>assets/all/js/main.js"></script> 
</body>
</html>

</script>