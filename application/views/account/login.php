<!DOCTYPE html>
<html lang="en">
  <head>
    
    <title>NEPSE Portfolio / Authentication </title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url(); ?>assets/bs/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>assets/all/css/signin.css" rel="stylesheet">
    
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
          if(isset($error)) echo '<label class="alert alert-danger">'. $error . '</label>';
        ?>
        <div class="checkbox">
     <!--      <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label> -->
        </div>
        <button class="btn btn-primary" type="submit">Sign in</button>
        <a href="./recover" class="btn btn-link" role="button">Reset Password</a>        
      </form>

    </div> <!-- /container -->

  </body>
</html>
