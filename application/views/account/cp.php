
<h4>Change Password</h4>

<br/>

<form method="POST" action="./cp">
  <div class="row">
	<div class="col-md-6">
          <!-- <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> -->
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
          <label for="CurrentPassword" class="control-label">Current password:</label>
          <input type="password" class="form-control" name="CurrentPassword"></select>   
	  </div>
	</div>

	<div class="row">    
      <div class="form-group col-md-6">
          <label for="NewPassword" class="control-label">New Password:</label>
          <input type="password" class="form-control" name="NewPassword"></select>   
      </div>
  	</div>

	<div class="row">    
      <div class="form-group col-md-6">
          <label for="ConfirmPassword" class="control-label">Confirm Password:</label>
		  <input type="password" class="form-control" name="ConfirmPassword"></select>       
	  </div>
	</div>

	<div class="row">    
      <div class="form-group col-md-6">
            <button class='btn btn-default' type="Submit" class="form-control" name="Submit">Submit</button>   
      </div>          
  </div>
    
</form>	

</div>		
