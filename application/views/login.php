<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="My Portfolio : Login">
    <meta name="author" content="Nava Bogatee">
    <link rel="icon" href="<?php echo base_url(); ?>favicon.ico">

    <title>Signin</title>

    <!-- Custom styles for this template -->
    <!-- <link href="<?php echo base_url(); ?>assets/all/css/signin.css" rel="stylesheet"> -->
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">      
     <form method="post" class="form-signin" action="<?=current_url();?>"
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" name="inputEmail" id="inputEmail" value="<?=$inputEmail ?>"  class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="inputPassword" id="inputPassword" class="form-control" placeholder="Password" required>
        <?php 
          if(isset($error)) echo '<label>'. $error . '</label>';
        ?>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-primary" type="submit">Sign in</button>
        <button class="btn">Reset Password</button>        
      </form>

    </div> <!-- /container -->

  </body>
</html>
