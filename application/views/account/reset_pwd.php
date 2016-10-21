<script type="text/javascript">

$(document).ready(function(){

	$('.rounded').corners("20px");

	$('#btnUser_name').click(function()
	{		
			$ptrUsername 	=	$("#login_name");
			$ptrLoading  	=	$('div#loading');
			$ptrMessage		= 	$('#username_label');
			
			$user_name	=	$ptrUsername.val();
			$staff_guid	=	$ptrUsername.attr('guid');
			
			if($user_name == '') return false;
			
			$ptrLoading.show();
			$ptrMessage.show().hide();
			
			$.post($base_url + "/account/checkusername",
					{"user_name" : $user_name, "staff_guid" : $staff_guid},
					function(val )
					{
						$ptrLoading.hide();	
						if (val == 'yes')
							$ptrMessage.removeClass().addClass('success').html('The username is available').show();
						else 
							$ptrMessage.removeClass().addClass('error').html('The username is not available').show();
					}
				);
	});
});	
		
</script>

<style type="text/css">
	.list {list-style : none; display : table}
	.list li{font-size : small ; float : left ; margin : 5px 0px ; padding : 3px;}
	input.submit {font-size:small; font-weight:bold ; padding : 3px 7px;}
	div.error {font-size : small; margin : 2px}	
</style>

	<?php
		echo form_open('account/reset');
		$star = '<span class="star">*</star>';
	?>

	<div id="ChangePassword" class="rounded">
		
			
		<ul class="list">
			<li>
				<h2 style="margin:0px">Account Recovery</h2><br />
				<?php if(strlen(validation_errors()) > 0) echo validation_errors(); 
					  if(strlen($form_message) > 0 ) echo '<span class="error">' . $form_message .'</span>';
				?>
			</li>
			<li>
				<div class="grid_2">
				<?php 
					echo form_label('Login Name','login_name'); 
					echo strlen(form_error('login_name')) > 0 ? $star : '';					
				?>
				</div>
				<div class="grid_4">
				<?php
					$val=array('name'=>'login_name','id'=>'login_name','class'=>'input','value'=>$login_name,'guid'=>$staff_guid);
					echo form_input($val). '&nbsp;&nbsp;';
					$val=array('name'=>'btnUser_name','id'=>'btnUser_name','class'=>'input');
					echo form_button($val,'Check Availability');
					echo '<div id="loading"></div>';
					echo '<div id="username_label"></div>';
					echo form_hidden('login_guid',$login_guid);
					echo form_hidden('staff_guid',$staff_guid);
					echo form_hidden('unique_str',$unique_str);
				?>					
				</div>
			</li>
			<li>
				<div class="grid_2">
				<?php 
				
					echo form_label('Password','Password');
					echo strlen(form_error('Password')) > 0 ? $star : '';
				?>
				</div>
				<div class="grid_4">
				<?php
					$val=array('name'=>'Password','id'=>'Password','class'=>'input password');
					echo form_password($val);					
				?>					
				</div>
			</li>
			<li>
				<div class="grid_2">
					<?php 
						echo form_label('Retype password','RePassword');
						echo strlen(form_error('RePassword')) > 0 ? $star : '';
					?>
				</div>
				<div class="grid_4">
				<?php
					$val=array('name'=>'RePassword','id'=>'RePassword','class'=>'input password');
					echo form_password($val);
				?>					
				</div>
			</li>
			<li>
				<div class="grid_2">&nbsp;</div>
				<div class="grid_4">
				<?php
					$val=array('name'=>'btnSubmit','id'=>'btnSubmit','value'=>'Submit','class'=>'submit');
					echo form_submit($val);
				?>					
				</div>
			</li>
		</ul>
		
	</div>		
	<?php echo form_close();?>
