<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller 
{	
	private $app_name='';
	
	public function __construct()
	{
		parent::__construct();
			
		$this->load->model('AccountModel', 'mAccount');
		$this->load->model('MailModel','mMail');
		date_default_timezone_set('Asia/Kathmandu');

		$CI =& get_instance();		
		$this->app_name	=	$CI->config->item('app_name');		
	}

	function index()
    {
		if($this->session->login_id == null)
			redirect('account/login');

		echo 'Not implemented';

    }

	function login()
	{
		$data['title'] = $this->app_name;
		$data['inputEmail']='';
		date_default_timezone_set('Asia/Kathmandu');	//SET DEFAULT TIMEZONE
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$email = $this->input->post('inputEmail');
			$password = $this->input->post('inputPassword');
			$data['inputEmail'] = $email;			

			if($this->verify($email, $password))
			{
			     //once logged in, set  last login datetime
			     $this->mAccount->setLastLoginDateTime($this->session->login_id);
			     redirect('/portfolio', 'refresh');
			     return;	
			}
			else
				$data['error'] = 'Invalid email or password. Please try again.';
		}

		$this->load->view('account/login.php', $data);
	}
	

	function register() 
	{
		
		$data['page_title']=$this->app_name.' / Account / User registration';
		$this->load->library('form_validation');
		$this->form_validation->set_rules('FullName','Full name','trim|required|min_length[3]');		
		$this->form_validation->set_rules('Email','Email','trim|required|matches[ReEmail]');
		$this->form_validation->set_rules('ReEmail','Confirm Email','trim|required');
		$this->form_validation->set_rules('Password','Current Password','trim|required|min_length[4]');
		$this->form_validation->set_rules('RePassword','Confrm Password','trim|required|matches[Password]|min_length[5]');		
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">','</div>');

		
		$obj=(object)array(
			'email'=> $this->input->post('Email'),
			'reEmail'=> $this->input->post('ReEmail'),
			'password'=> $this->input->post('Password'),
			'rePassword'=> $this->input->post('RePassword'),			
			'full_name'=>$this->input->post('FullName'),
			'active'=>0	
			);

		$msg='';
		$data['message']=  $msg;
		$data['Email']= $obj->email;
		$data['reEmail']= $obj->reEmail;
		$data['FullName']= $obj->full_name;
		$data['view_name'] ="account/register";
		
		if($this->form_validation->run('account/register') == FALSE) {
			$this->load->view('account/base_public', $data);
			return ;
		}

		$exists=$this->mAccount->is_email_available( $obj->email );
		if($exists)	{
			$message='An account already exists with the email you specified.<br>For account recovery procedures please click <br>';
			$message .= anchor('account/recover','Recover my account');
			$data['message'] = $message;
			$this->load->view('account/base_public', $data);
			return ;
		}
		
		//prepare the object before saving
		unset($obj->reEmail);				//unset re-email from object as it doesn't exist in db
		unset($obj->rePassword);			//unset re-password from object as it doesn't exist in db
		//$obj->unique_string= random_string('md5');
		$obj->password = $this->encrypt->encode($obj->password);	//encrypt the password

		$saved = $this->mAccount->create_login($obj);		
		$saved=true;
	
		if($saved) {
			$this->mMail->mail_user_verification($obj->email);
			$message = '<h3>Registration successful</h3>Please check your email and verify the account.';
			$message .= "<br />Take me to ".anchor('account/login','Login page');
			$data['message']=  $message;
			$data['Email']= "";
			$data['reEmail']= "";
			$data['FullName']= "";			
			$this->load->view('account/base_public', $data);		
		}
	}

	//presents user with a form to change password
	function cp1($login_id, $usr_str){
		$db_str = $this->mAccount->get_unique_string($login_id);

		if (strcmp($db_str, $usr_str) === 0){
			$this->mAccount->set_unique_string_null( $login_id );		//nullify the unique string
			$this->load->library('form_validation');
			//re-direct user to a page where s/he can put new password
			$data['page_title']=$this->app_name.' / Account / Change password';
			$data['message'] = '';
			$data['login_id'] = $login_id;			//pass login_id as reference
			$data['view_name'] = 'account/cp1';			
			$this->load->view( 'base', $data );
		}
		else
		{
			$message = '<h4>Oops!.</h4>The password may already have been changed using this link  or the link have been altered.<hr>Please contact support';
			$data['page_title']=$this->app_name.' / Account / Password change error';
			$data['message'] = $message;
			$data['view_name'] = 'mydefault';			
			$this->load->view( 'base', $data );	
		}
	}


	//creates a new password
	function create_password()
	{	
		
		$data['page_title']=$this->app_name.' / Account / Change password';
		$this->load->library('form_validation');		
		$this->form_validation->set_rules('NewPassword','New Password','trim|required|matches[ConfirmPassword]|min_length[5]');
		$this->form_validation->set_rules('ConfirmPassword','Confirm Password','trim|required|min_length[5]');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">','</div>');
		
		$message='';
		$data['NewPassword']=$this->input->post('NewPassword');
		$data['ConfirmPassword']=$this->input->post('ConfirmPassword');
		
		$login_id = $this->input->post('txtLoginId');
		if($this->form_validation->run()==true)
		{
			$new_password=$this->encrypt->encode($this->input->post('ConfirmPassword'));
			if($this->mAccount->UpdatePassword($login_id, $new_password))
			{
				$message='<div class="alert alert-success">Password has been created. Please try to login.</div>';									
			}				
		}
		
		$data['message'] = $message;
		$data['view_name'] = 'account/cp1';
		$data['login_id'] = $login_id;
		$this->load->view( 'base', $data );		

	}
	//change password when current password is known, ie for logged in users.
	function cp()
	{	
		if($this->session->login_id == null)
			redirect('account/login');

		$data['page_title']=$this->app_name.' / Account / Change password';
		$this->load->library('form_validation');
		$this->form_validation->set_rules('CurrentPassword','Current Password','trim|required|min_length[4]');
		$this->form_validation->set_rules('NewPassword','New Password','trim|required|matches[ConfirmPassword]|min_length[5]');
		$this->form_validation->set_rules('ConfirmPassword','Confirm Password','trim|required|min_length[5]');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">','</div>');
		
		$message='';
		$data['CurrentPassword']=$this->input->post('CurrentPassword');
		$data['NewPassword']=$this->input->post('NewPassword');
		$data['ConfirmPassword']=$this->input->post('ConfirmPassword');
		
		if($this->form_validation->run()==true)
		{
			$login_id = $this->session->login_id;			
			$old_password=$this->input->post('CurrentPassword');
						
			if($this->mAccount->is_password_valid($login_id, $old_password))
			{	
				$new_password=$this->encrypt->encode($this->input->post('ConfirmPassword'));
				if($this->mAccount->UpdatePassword($login_id, $new_password))
				{
					$message='<div class="alert alert-success">Password changed successfully. Logging out in 5 seconds. Please login again.</div>';
					header( "refresh:5;url=./logout" );

				}				
			}
			else 
			{
				$message='<div class="alert alert-danger">Current password that you entered is not correct.</div>';
			}
		}
		//$rs = $this->mAccount->get_user_details($this->session->login_id);		
		$data['message'] = $message;
		$data['view_name'] = 'account/cp';
		//$data['user_data'] = $rs;
		$this->load->view( 'base', $data );		

	}
	
	function  verify($email, $password)
	{
		
	    $db_password =  $this->mAccount->get_decrypted_password_by_email($email);			    	    

	    if(strcmp($db_password, $password) == 0)
	    {
		    $arr =  $this->mAccount->get_user_details_by_email($email);
		    
		    $info = array
		    	(	'login_id' => $arr->login_id,
		    		'email' => $arr->email,
		    		'name'  => $arr->full_name,
		    		'privilege'  => $arr->privilege
		    	);
		    $this->session->set_userdata($info);
		    return true;
		}

		//$this->session->set_flashdata('login-error', 'Invald email or password. Please try again.');
		return false;
	}

	//1.check unique string is valid? 2. activate account 3. get full name of the account and update the shareholder table with the name 4. nullify the unique string so it can't be reused
	function accountVerify($login_id, $usr_str){
		$db_str = $this->mAccount->get_unique_string($login_id);
		if (strcmp($db_str, $usr_str) === 0){
			$this->load->model('Portfolio_model','mPortfolio');
			$activated = $this->mAccount->set_account_active( $login_id );		//activate the account
			$message = 'Can not activate the account. Please contact the administrator ';
			if($activated){
				$this->mAccount->set_unique_string_null( $login_id );
				$full_name  = $this->mPortfolio->get_user_full_name($login_id);				
				//create a new item under table shareholder as the full name of the user
		    	$obj = array('shareholder_name'=> $full_name, 'login_id' => $login_id);
	    		$result = $this->mPortfolio->save_shareholder_name( $obj );
				$message = "Congratulations. Your account has been activated. Click " . anchor('account/login','here to login');
			}
		}
		else		
			$message = '<h4>Can not activate the account.</h4>The account may already have been activated or the URL we sent to you may have been altered.<hr>Please try loggin in and if unsuccessful contact support';
			
		$data['page_title']=$this->app_name.' / Account / Activation error';
		$data['message'] = $message;
		$data['view_name'] = 'mydefault';			
		$this->load->view( 'base', $data );	
	}

	function logout(){

		$sessionData = $this->session->all_userdata();
	 	foreach($sessionData as $key =>$val){
	    	$this->session->unset_userdata($key);	    
	  	}

	  	redirect('account/login');
	}	

// 	//used to recover the password when the user forgets his/her login details
// 	//will send a link to the user's inbox for account activation
	
	function recover(){	
		
		$data['title']=$this->app_name.' / Recover account';
		$this->load->library('form_validation');
		$this->form_validation->set_rules('Email','Email Address','required|valid_email|trim');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">','</div>');

		$email 	= $this->input->post('Email');
		$data['message']	=	'';
		$data['page_title']	=	 $this->app_name . ' / Account recovery';
		$data['email']	=	 $email;
		$data['view_name']='account/recover_account';
		
		if($this->form_validation->run()==FALSE)
		{			
			$this->load->view('account/base_public',$data);
			return;
		}		

		//check if email exists
		$result = $this->mAccount->get_user_details_by_email( $email );

		if( $result ){						
			$what = $this->mMail->mail_account_recovery( $result );	
		}
		else{
			$message = 'Looks like you have not been registered with us yet.';
			$message .= '<br>If you want to register ' . anchor('account/register','click here');
			$data['message']= $message;		
			$this->load->view('account/base_public',$data);				
			return;
		}
		
		$data['message']="An email has been sent to you with instructions to reset your new password."
			."<br /><br />".anchor('account/login','Login page');			
		$this->load->view('account/base_public',$data);			
	}

	function getAllUserStatus(){
		$data = $this->mAccount->get_all_user_status();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));		
	}

	function getUserRoles(){
		$data = $this->mAccount->get_user_roles($this->session->email);
        $this->output->set_content_type('application/json');
        echo  (is_null($data) ? '[]' : json_encode( $data ));    
	}

	//change user privilige or active status based on target
	function update_user_roles(){		
		$obj = json_decode(file_get_contents('php://input'), true);			//get the json POST stream from header
		$status = $this->mAccount->update_user_roles($obj);
        $this->output->set_content_type('application/json')->set_output(json_encode($status));
	}

	//save shareholder names
	function SaveShareholder(){
		$obj = json_decode(file_get_contents('php://input'), true);	
		$this->mAccount->save_shareholder_name( $obj['name'], $this->session->login_id );
	}

	//save sharegroup names
	function SaveShareGroup(){
		$obj = json_decode(file_get_contents('php://input'), true);	
		$this->mAccount->save_sharegroup_name( $obj['name'], $this->session->login_id );
	}	

	//remove shareholder
	function removeShareholder(){
		$obj = json_decode(file_get_contents('php://input'), true);			
		$obj['login_id'] = $this->session->login_id; 						
		$num_rows = $this->mAccount->remove_shareholder($obj);
		$this->output->set_content_type('application/json')->set_output(json_encode($num_rows));
	}

	//remove shareholder group
	function removeShareGroup(){
		$obj = json_decode(file_get_contents('php://input'), true);	
		$obj['login_id'] = $this->session->login_id; 		
		$num_rows = $this->mAccount->remove_shareholder_group($obj);
		$this->output->set_content_type('application/json')->set_output(json_encode($num_rows));
	}

}