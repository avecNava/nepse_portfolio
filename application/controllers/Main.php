<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller 
{
	private $tblShareholder	= 'nepse_shareholder';
	private $tblPortfolio	= 'nepse_portfolio';
	private $tblNepsedata	= 'nepse_data';
	private $tblCategory 	= 'nepse_category';
	private $tblLogin 		= 'nepse_login';
	private $tblStockType	= 'nepse_stock_type';
	private $app_name;

	public function __construct()
	{
		parent::__construct();	
		$CI =& get_instance();		
		$this->app_name	=	$CI->config->item('app_name');				
		//date_default_timezone_set('Asia/Kathmandu');
	}

	function rate(){
		$data['page_title'] = $this->app_name . ' / Market rate';
		$data['view_name']	= 'nepse_rate';
		$this->load->view('base', $data);
	}
	
	function company(){
		$data['page_title'] = $this->app_name . ' / Company details';
		$data['view_name']	= 'nepse_company';
		$this->load->view('base', $data);
	}

	function sale(){
		echo 'Not implemented';
	}

	function pl(){
		echo 'Not implemented';
	}

	//parses the sharesansar.com website and extracts data
	public function fetchStock($mode = 'todaysprice')
	{
		$var = '';
		$days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];			
		$timezone = date_default_timezone_get();
		
		echo "Site hosted in : " . $timezone;		
		$server_date = date('m/d/Y h:i:s a', time());
		$dayofweek = date('w', strtotime($server_date)); 		
		echo  '<h4>Current time ' . $days[$dayofweek] .' '. $server_date . '</h4>';
		
		echo('Changing timezone to Asia/Kathmandu<br/>');
		date_default_timezone_set('Asia/Kathmandu');
		$local_date = date('m/d/Y h:i:s a', time());
		$dayofweek = date('w', strtotime($local_date)); 		
		echo  '<h4>Local time ' . $days[$dayofweek] .' '. $local_date. '</h4>';
		
		$server_date = new DateTime($server_date);
		$local_date = new DateTime($local_date);
		$interval = $local_date->diff($server_date);
		
		echo '<h4>Time difference is ' . $interval->format("%H:%I:%S");
		
		//don't run if its friday/saturday ..
		if( $dayofweek > 4){
			echo '<h4>Ouch! It can run only Sunday to Thursday. And yes, not holidays too. <br>See you on Sunday</h4>';
			echo '<hr>';
			return;	
		} 
		
		$this->load->model('Nepse_model');
		if ($mode == 'todaysprice')
			$var=$this->Nepse_model->fetchStock();
		elseif($mode == 'live')
			$var=$this->Nepse_model->fetchLIVEStock();

		echo $var;
	}

	function stocktype()
	{
		$crud = new grocery_CRUD();
		$crud->set_table($this->tblStockType);
		$crud->unset_read()->unset_edit()->unset_delete()->unset_add();
		$crud->display_as('broker_commission_pc','Broker Commission %')->display_as('sebon_commission_pc','SEBON Commission %')->display_as('name_transfer_npr','Name transfer (NPR)');
		$output = $crud->render();
		$this->load->view('stocktype.php', $output);
	}

	public function Index()
	{
		
		echo 'Not implemented';
	}


	// function _callback_closing_price($value,$row)
	// {
	// 	$this->load->model('Nepse_model');
	// 	$Company = strip_tags($row->Symbol);
	// 	return round($this->Nepse_model->getClosingPrice($Company));
	// }
	// function _callback_prev_closing_price($value,$row)
	// {
	// 	$this->load->model('Nepse_model');
	// 	$Company = strip_tags($row->Symbol);
	// 	return round($this->Nepse_model->getPreviousClosingPrice($Company));
	// }
	// function _callback_get_difference($value, $row)
	// {
	// 	if($row->Price > 0 )
	// 	{
	// 	$diff = round($row->Price - $row->Prev_closing,2);		
	// 	$per_change = round(($diff / $row->Price)*100 , 2);
	// 	return $diff . ' (' . $per_change . '%)';
	// 	}
	// }
	// function _callback_remarks($value, $row)
	// {
	// 	$output = ($row->Remarks === NULL) ? '' : $row->Remarks . '<hr>';
	// 	//$output .= 'create date<br>' . $row->Create_date . '<br>';
	// 	//$output .= is_null($row->Update_date) ? '' : 'Update date<br>'. $row->Update_date;
	// 	return $output;
	// }
//calculates various commissions and returns an array
	function do_calculation($share_qty, $rate)
	{
		 //SEBON commission		 
		 $_sebon_comm = $this->config->item('Sebon_comm');
		 $_commission = $this->config->item('Broker_comm');	 

		$net_amount = $share_qty * $rate;
		$prev_amount=0; 
		$commission=0;

		 foreach ($_commission as $amount=> $per) 
		 {
		 	if($net_amount > $prev_amount && $net_amount <= $amount)
		 		$commission = $per;			 		
		 	elseif($net_amount >= 1000001) 
	 			$commission = 0.7;
	 			
		 	$prev_amount = $amount;		 			 	
		 }

		 $comm_per =  $commission * 100;
		 $comm_amount = round($net_amount * $commission,2);
		 $sebon_comm = round($net_amount * $_sebon_comm,2);
		 $gross_total = round($net_amount + $comm_amount + $sebon_comm,2);
		 $effective_rate = round(($gross_total / $share_qty), 2);		 
		 $result = (object)array
				 	('comm_per' => $comm_per,				 	
				 	'comm_amount'=> $comm_amount,
				 	'sebon_comm' => $sebon_comm,
				 	'effective_rate' => $effective_rate,
				 	'net_amount' => $net_amount,				 	
				 	'gross_total' => $gross_total				 	
				 	);					 	 
		 return $result; 
	}

	// function _callback_Investment($value, $row)
	// {
	// 	$result = $this->do_calculation($row->Quantity, $row->Rate);
	// 	$investment = $result->gross_total;
	// 	return  round($investment,2);
	// }

	// function _callback_Market_value($value, $row)
	// {
	// 	//$result = $this->do_calculation($row->Quantity, $row->Rate);
	// 	$earning = $row->Quantity * $row->Price;		
	// 	return  round($earning,2);
	// }
	// function _callback_Gain_loss($value, $row)
	// {
	// 	$day_gain = ($row->Price - $row->Prev_closing) * $row->Quantity;
	// 	$overall_gain = ($row->Quantity * $row->Price) - $row->Investment;
		
	// 	$day_gain_pc = round(($day_gain / $row->Investment) * 100, 2);
	// 	$overall_gain_pc = round(($overall_gain / $row->Investment) * 100, 2);
		
	// 	return 'Gain: ' . $day_gain . ' ('. $day_gain_pc . '%)<br>Overall Gain: '. $overall_gain . ' (' . $overall_gain_pc . '%)';
	// }

	// //The parameters that callback takes are : 1 - the primary key value of the row and 2 - the row as an object.
	// public function _callback_date($val,$row)
	// {
	// 	return 	date('Y-m-d');
	// }

	// public function _callback_webpage_url($value, $row)
	// {
	//   return "<a target='_blank' href='http://www.merolagani.com/CompanyDetail.aspx?symbol=".$row->Symbol."'>$value</a>";
	// }

	// function _update_effective_rate($post_array, $primary_key)
	// {
	// 	if(!empty($post_array['Rate']))
	// 	{	$result	= $this->do_calculation($post_array['Quantity'],$post_array['Rate']);
	// 		$post_array['Effective_rate'] = $result->effective_rate + 5;			
	// 		$post_array['Update_date'] = date('Y-m-d H:i:s');
	// 		return $post_array;
	// 	}
	// }

	function encrypt_password_callback($post_array, $primary_key = null)
	{
	    $post_array['password'] = $this->encrypt->encode($post_array['password']);
	    $post_array['re-password'] = $this->encrypt->encode($post_array['re-password']);
	    return $post_array;
	}
	 
	function decrypt_password_callback($value)
	{
	    $decrypted_password = $this->encrypt->decode($value);
	    return "<input type='password' name='password' value='$decrypted_password' />";
	}

	//called by ajax function .. (market_rate page)
	function get_market_rate()
	{
		$this->load->model('Nepse_model','mNepse');
		$arr = $this->mNepse->get_market_rate();		
		//header('Content-Type: application/json');
	    //echo empty brackets when array is null, ie, there is no data fetched from the server otherwise datatable shall throw error
	    echo  (is_null($arr) ? '[]' : json_encode( $arr ));

	}
	//output JSON to datatable
	function get_company_details(){
		$this->load->model('Nepse_model','mNepse');
		$arr = $this->mNepse->get_company_details();		
		header('Content-Type: application/json');	    
	    echo  (is_null($arr) ? '[]' : json_encode( $arr ));	
	}

	//newly listed companies 
	function get_new_company(){
		$this->load->model('Nepse_model','mNepse');
		$arr = $this->mNepse->get_new_company();		
		header('Content-Type: application/json');	    
	    echo  (is_null($arr) ? '[]' : json_encode( $arr ));	
	}

	function update_symbol(){
		$obj = json_decode(file_get_contents('php://input'), true);			//get the json POST stream from header
		$this->load->model('Nepse_model','mNepse');
		$status = $this->mNepse->update_symbol($obj);
        $this->output->set_content_type('application/json')->set_output(json_encode($status));
	}

	//get tax info
	function get_tax_info(){
		$this->load->model('Nepse_model','mNepse');
		$arr = $this->mNepse->get_tax_info();		
		header('Content-Type: application/json');	    
	    echo  (is_null($arr) ? '[]' : json_encode( $arr ));	
	}

	//save tax info
	function save_tax_info(){
		$this->load->model('Nepse_model','mNepse');
		$obj = json_decode(file_get_contents('php://input'), true);

		$retVal = $this->mNepse->save_tax_info( $obj );		
		echo $retVal;
		// header('Content-Type: application/json');	    
	 //    echo  (is_null($arr) ? '[]' : json_encode( $arr ));	
	}	

	function remove_tax_info( )
	{
		$this->load->model('Nepse_model','mNepse');
		$obj = json_decode(file_get_contents('php://input'), true);
		
		$retVal = $this->mNepse->remove_tax_info( $obj['tax_id'] );		
		header('Content-Type: application/json');	    
		echo json_encode($retVal);
	}

	function get_unique_id(){
		$prefix = $_POST['prefix'];
		$prefix = empty($prefix) ? 'NEPSE' : strtoupper($prefix);		
		// header('Content-Type: application/json');	    
		// echo json_encode(uniqid($prefix));
		echo uniqid($prefix);
	}

	function remove_company(){
		$symbol = $_POST['symbol'];
		$this->load->model('Nepse_model','mNepse');
		$ret = $this->mNepse->remove_company($symbol);
		echo $ret;
	}
}