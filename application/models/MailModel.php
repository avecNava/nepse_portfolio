<?php
class MailModel extends CI_Model
{
	private  $SENDER = 'nava.bogatee@gmail.com';
	private  $REPLY_TO = 'nava.bogatee@gmail.com';
	private  $CC = '';
	private  $BCC = 'unnepalese@gmail.com';
	private $app_name;
	
	public function __construct()
    {
        parent::__construct();     
        $this->load->helper('string');   

        $CI =& get_instance();
		$this->app_name = $CI->config->item('app_name');

		$this->load->model('AccountModel','mAccount');
		$this->load->model('Template_model','mTemplate');		

    }   
	
	
	function SendMail($recipient, $subject, $message)
	{
				
		$email_config = Array(
		    'protocol'  => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => '465',
		    'smtp_user' => 'unnepalese@gmail.com',
		    'smtp_pass' => '/Mail:2015',
		    'mailtype'  => 'html',
		    'starttls'  => true,
		    'newline'   => "\r\n"
		);

		$this->load->library('email', $email_config);

		$this->email->from($this->SENDER);		
		$this->email->to($recipient);		
		$this->email->reply_to($this->SENDER);		
		$this->email->cc($this->CC);		
		$this->email->bcc($this->BCC);		
		$this->email->subject($subject);		
		$this->email->message($message);
		
		// Set to, from, message, etc.
		$result = $this->email->send();
		if( $result == false) show_error($this->email->print_debugger());
		return $result;
	}
	
	function mail_user_verification( $email )
	{
		$obj = $this->mAccount->get_user_details_by_email( $email );
		$unique_str = random_string('md5');				//generate an uniquestring and send with the mail. Also save the same in the db
		
		if($obj != null) 
		{	
			//saves the Unique string
			$this->mAccount->update_unique_string($obj->login_id, $unique_str);
			
			//get the mailing template for password recovery			
			$html = $this->mTemplate->account_activation_message($obj->full_name, $obj->login_id, $unique_str);
			
			$subject = $this->app_name . ' : Account activation';
			return $this->SendMail($obj->email, $subject, $html);			
		}
	}	

	function mail_account_recovery ( $obj )
	{
		$unique_str = random_string('md5');
		
		if($obj != null) 
		{	
			$this->mAccount->update_unique_string($obj->login_id, $unique_str);

			//get the mailing template for account recovery			
			$html = $this->mTemplate->account_recovery_message($obj->full_name, $obj->login_id, $unique_str);
			
			$subject = $this->app_name . ' : Account recovery';			
			return $this->SendMail($obj->email, $subject, $html);			
		}
	}


		
}