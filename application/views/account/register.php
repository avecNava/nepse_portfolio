	<h4>User Registration</h4>
	
	<form method="POST" action="./register">

	<div class="row alert">
		<span class="alert col-md-6">			
         <?php echo validation_errors();
     	 if (strlen( trim($message) ) > 0)
     	 	echo '<span class="alert alert-warning">' . $message . '</span>';
     	 ?>
         </span>
	 </div>     		

	<div class="row"></div>

	 	<div class="row">    
	      <div class="form-group col-md-6">
	          <label for="FullName" class="control-label">Full  Name :</label>
	          <!-- <input type="text" class="form-control" name="FullName"></select>    -->
	          <?php echo form_input( array('name' =>"FullName", 'value'=> $FullName,'class'=>"form-control" )); ?>
		  </div>
		</div>

		<div class="row">    
	      <div class="form-group col-md-6">
	          <label for="Email" class="control-label">Email :</label>
			  <input type="email" class="form-control" name="Email" value="<?=$Email ?>"></select>       
		  </div>
		</div>

		<div class="row">    
	      <div class="form-group col-md-6">
	          <label for="ReEmail" class="control-label">Confirm Email :</label>
			  <input type="email" class="form-control" name="ReEmail"  value="<?=$reEmail ?>"></select>       
		  </div>
		</div>	

		<div class="row">    
	      <div class="form-group col-md-6">
	          <label for="Password" class="control-label">password:</label>
	          <input type="password" class="form-control" name="Password"></select>   
		  </div>
		</div>

		<div class="row">    
	      <div class="form-group col-md-6">
	          <label for="RePassword" class="control-label">Confirm Password:</label>
	          <input type="password" class="form-control" name="RePassword"></select>   
	      </div>
	  	</div>


		<div class="row">    
	      <div class="form-group col-md-6">
	            <button class='btn btn-default' type="Submit" class="form-control" name="Submit">Submit</button>   
	      </div>          
	  </div>
	    
	</form>	