<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">		
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/smoothness/jquery-ui.css" />    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />        
	<title><?=$page_title ?></title>
	<style>
		body {margin: 1px }
		h4 {margin:5px 0px;}
		.navbar {margin: 1px -15px; margin-bottom: 15px}
		.alert {
		    padding: 5px;
		    margin-bottom: 10px;	   
		    display:block; 
		}
	</style>

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
	      <div class="navbar-brand">NEPSE Portfolio</div>
	      <ul class="nav navbar-nav navbar-right">
        	<li><a href="./login">Login</a></li>
        	<li><a href="./register">Register</a></li>
        	<li><a href="./recover">Account recovery</a></li>
          </ul>
	    </div>
	</nav>
	
	<?php $this->load->view($view_name) ?>

</div>  <!-- end div container -->

</body>
</html>
<script src="http://code.jquery.com/jquery-1.12.4.min.js"   integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="   crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"   integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="   crossorigin="anonymous"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> 

</script>