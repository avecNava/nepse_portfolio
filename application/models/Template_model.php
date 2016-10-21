<?php

class Template_model extends CI_Model

{	
	private $HTML_OPEN = '<HTML><HEAD></HEAD><BODY>';
	private $HTML_CLOSE = '</BODY></HTML>';
	private $site_logo;
	private $domain_name;
	private $domain_url;
	private $SUPPORT = 'nava.bogatee@gmail.com';
	
	public function __construct()
    {
        parent::__construct();

		$CI =& get_instance();
		$this->domain_name = $CI->config->item('app_name');
		$this->domain_url = $CI->config->item('site_url');;
		
		// $logo = array(
	 //          'src' =>  $this->domain_url . '/images/site_logo_sm.gif',
	 //          'alt' => '',
	 //          'class' => 'site_logo',
	 //          'width' => '150',
	 //          'height' => '30',
		// );
		
		// $this->site_logo = '<div style="margin-left:-5px">' . img($logo) . '</div>';
		
	}

	function account_activation_message($customer_name, $login_id, $unique_str)
	{			

		$msg = '<p>Dear <strong>' . $customer_name . '</strong></p>';
		$msg .= '<p>Welcome to '. $this->domain_name.'. </p><p>A huge thanks for signing up with us. We are hopeful that you will have a wonderful portfolio management experience through our platform.</p>';
		
		$msg .= '<p>We are sending this mail to verify your email address. In order to do so please click ' . anchor('/account/accountVerify/' . $login_id . '/' . $unique_str, "here" ) . '.</p>';
		
		$msg .= '<p>If you have difficulties opening the above link, please copy and paste the following address to any browser of your choice (eg, mozilla firefox, google chrome, brave)</p>';

		$msg .= '<p>Address : <em><b>'. $this->domain_url .'/account/accountVerify/' . $login_id . '/' . $unique_str .'</b></em></p>';

		$msg.= '<p>Should you have any issues in completing this step, please let us know at <em><u>'. $this->SUPPORT.'</u></em>. </p>';
		
		$msg .= '<p>Thanking You</p>';
		$msg .= '<p><strong>'.$this->domain_name.'</strong></p>';
				
		return $this->HTML_OPEN . $msg . $this->HTML_CLOSE;
		
	}

	
	function account_recovery_message( $customer_name, $login_id, $unique_str )
	{
		$msg = '<p>Dear <strong>' . $customer_name . '</strong></p>';
				
		$msg .= '<p>This mail will assist you to recover your '. $this->domain_name.' account. Please click ' . anchor('/account/cp1/' . $login_id . '/' . $unique_str, "here" ) . ' to reset your password.</p>';
		
		$msg .= '<p>If you have difficulties opening the above link, please copy and paste the following address to any browser of your choice (eg, mozilla firefox, google chrome, brave)';

		$msg .= '<br>Address : <em><b>'. $this->domain_url .'/account/cp1/' . $login_id . '/' . $unique_str .'</b></em></p>';

		$msg.= '<p>Should you have any issues in completing this step, please let us know at <em><u>'. $this->SUPPORT.'</u></em>. </p>';

		$msg.= '<p><h4>NOTE : If you did not initiate this request please do not click on the above link and delete this mail.</h4></p>';
		
		$msg .= '<p>Thanking You</p>';
		$msg .= '<p><strong>'.$this->domain_name.'</strong></p>';
				
		return $this->HTML_OPEN . $msg . $this->HTML_CLOSE;

	}
	
}