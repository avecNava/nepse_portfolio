
<h4>Change Password</h4>

<br/>

<form method="POST" action="<?=site_url()?>/account/create_password">
  <div class="row">
  	<div class="col-md-6">          
        <?php 
  			echo validation_errors();
  			echo  $message 
  		?>
    </div>
    <div class="col-md-6">
    </div>
  </div>
	
	<div class="row">    
      <div class="form-group col-md-6">
          <input type="hidden" class="form-control" name="txtLoginId" value="<?=$login_id ?>"></input>   
          <label for="NewPassword" class="control-label">New Password:</label>
          <input type="password" class="form-control" name="NewPassword"></input>   
      </div>
  	</div>

	<div class="row">    
      <div class="form-group col-md-6">
          <label for="ConfirmPassword" class="control-label">Confirm Password:</label>
		  <input type="password" class="form-control" name="ConfirmPassword"></input>       
	  </div>
	</div>

	<div class="row">    
      <div class="form-group col-md-6">
            <button class='btn btn-default' type="Submit" class="form-control" name="Submit">Submit</button>   
      </div>          
  </div>
    
</form>	

</div>		
