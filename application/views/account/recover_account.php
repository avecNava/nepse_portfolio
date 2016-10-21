
	<h4>Account recovery</h4>
	
	<form method="POST" action="./recover">

	<div class="row alert">
	<span class="alert col-md-6">			
	 <?php echo validation_errors();
		 if (strlen( trim($message) ) > 0)
		 	echo '<span class="alert alert-warning">' . $message . '</span>';
		 ?>
	 </span>
	</div>     		

	<div class="row">  </div>

	<div class="row">    
	  <div class="form-group col-md-6">
	      <label for="Email" class="control-label">Email :</label>
	      <input type="email" class="form-control" name="Email" value="<?=$email ?>"></select>   
	  </div>
	</div>

	<div class="row">    
	  <div class="form-group col-md-6">
	        <button class='btn btn-default' type="Submit" class="form-control" name="Submit">Submit</button>   
	  </div>          
	</div>
    
</form>	
