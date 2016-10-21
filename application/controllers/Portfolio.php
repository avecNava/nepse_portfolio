<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Portfolio extends CI_Controller 
{
	private $app_name;
	public function __construct()
	{
		parent::__construct();				
		$this->load->model('Portfolio_model', 'mPortfolio');
		$CI =& get_instance();		
		$this->app_name	=	$CI->config->item('app_name');	
		date_default_timezone_set('Asia/Kathmandu');
		if($this->session->userdata('email')==null)
			redirect('account/login');
	}

	function index()
    {		
		$this->login_id = $this->session->login_id;		
        $data['page_title'] =  $this->app_name . ' / Portfolio';        
		$data['view_name'] = 'Portfolio1';		
		$this->load->view( 'base_p', $data );	
    }
 
    //function to handle callbacks
    //https://github.com/IgnitedDatatables/Ignited-Datatables/wiki/Function-Reference
	// $this->datatables->add_column($column, $content, [$match_replacement]);
	// Sets additional column variables to facilitate custom columns. You can also make your own custom column definitions via the following syntax: *match_replacement is optional only needed if you have $1 to $n matches in your content pattern
	// $this->datatables->add_column('edit', '<a href="profiles/edit/$1">EDIT</a>', 'id');
	//this function passes the json data to the ajax function calling via js file

	//get share data
    function getPortfolioData()
    {
		$i = 0;		$arr = array();

		//get the json POST stream from header
		$obj = json_decode(file_get_contents('php://input'), true);

		//loop through the json obj and parse the first 5 select lists whose value > 0 . (only they are used for filter)
		foreach ($obj as $key => $value) {						
			if($value != "0")
				$arr[$key] = $value;			//push all enumerated items in the array $arr
			if( ++$i >= 6 ) 					//only take params for the first five form items (ie, the select list)
				break;
		}
		//add the current login id in the list of parameters
		$arr['login_id'] = $this->session->login_id;

		
		$arr = $this->mPortfolio->getPortfolioData( $arr );	
	 	header('Content-Type: application/json');
	    //echo empty brackets when array is null, ie, there is no data fetched from the server otherwise datatable shall throw error
	    echo  (is_null($arr) ? '[]' : json_encode( $arr ));    
    }

    //cummulative data of shares
    function getCummulativePortfolioData()
    {
		$i = 0;		$arr = array();

		//get the json POST stream from header
		$obj = json_decode(file_get_contents('php://input'), true);

		//loop through the json obj and parse the first 5 select lists whose value > 0 . (only they are used for filter)
		foreach ($obj as $key => $value) {						
			if($value != "0")
				$arr[$key] = $value;			//push all enumerated items in the array $arr
			if( ++$i >= 6 ) 					//only take params for the first five form items (ie, the select list)
				break;
		}
		//add the current login id in the list of parameters
		$arr['login_id'] = $this->session->login_id;
		//print_r($arr);
	    $arr = $this->mPortfolio->getCummulativePortfolioData( $arr );	

	    //process the 

	 	header('Content-Type: application/json');
	    //echo empty brackets when array is null, ie, there is no data fetched from the server
	    //otherwise datatable shall throw error
	    echo  (is_null($arr) ? '[]' : json_encode( $arr ));    

    }    

    function getPortfolioSummary()
    {
		$i = 0;		$arr = array();

		//get the json POST stream from header
		$obj = json_decode(file_get_contents('php://input'), true);

		//loop through the json obj and parse the first 5 select lists whose value > 0 . (only they are used for filter)
		foreach ($obj as $key => $value) {						
			if($value != "0")
				$arr[$key] = $value;			//push all enumerated items in the array $arr
			if( ++$i >= 6 ) 					//only take params for the first five form items (ie, the select list)
				break;
		}
		//add the current login id in the list of parameters
		$arr['login_id'] = $this->session->login_id;

		$arr = $this->mPortfolio->get_portfolio_summary( $arr );	
		
	 	//add the header here
	     header('Content-Type: application/json');
	     //echo  (is_null($arr) ? '[]' : json_encode( $arr ));    
	     echo json_encode( $arr );
    }

    function getSharewiseSummary()
    {
		$i = 0;		$arr = array();

		//get the json POST stream from header
		$obj = json_decode(file_get_contents('php://input'), true);

		//loop through the json obj and parse the first 5 select lists whose value > 0 . (only they are used for filter)
		foreach ($obj as $key => $value) {						
			if($value != "0")
				$arr[$key] = $value;			//push all enumerated items in the array $arr
			if( ++$i >= 5 ) 					//only take params for the first five form items (ie, the select list)
				break;
		}
		//add the current login id in the list of parameters
		$arr['login_id'] = $this->session->login_id;

		$arr = $this->mPortfolio->get_portfolio_summary( $arr, true );	

		//prepare the resultset as requierd by datatable for google chart
		$table = array();
		//column titles
		$table['cols'] = array(
			array('label' => 'Company','type' => 'string'),
			array('label' => 'Investment','type' => 'number'),
			);
		$rows = array();
		foreach ($arr as $key => $value) {
			$temp = array();
			$temp[] = array('v' => (string) $value->Symbol);			//col that will slice the pie chart
			$temp[] = array('v' => (int) $value->Investment);
			$rows[] = array('c' => $temp);
		}
		$table['rows'] = $rows;		
		$jsonTable = json_encode($table);		
	 	//add the header here
	     header('Content-Type: application/json');
	     echo $jsonTable;
	     //echo  (is_null($arr) ? '[]' : json_encode( $arr ));    
    }    

    //get trade history for the selected symbol
    function get_trade_history(){
    	//get the json POST stream from header
		$obj = json_decode(file_get_contents('php://input'), true);    			
		$result = $this->mPortfolio->get_trade_history( $obj['symbol'], $obj['limit'] );
		 header('Content-Type: application/json');
    	echo json_encode($result); 
    }

    function account(){
    	$data['page_title'] = $this->app_name . ' / My account';
    	$data['view_name'] = 'my_account.php';
    	$this->load->view('base', $data);
    }

    function saveShareholderName(){
    	$obj = array(
    			'shareholder_name'=> $this->input->post('name'), 
    			'login_id' => $this->session->login_id
    			);
    	$result = $this->mPortfolio->save_shareholder_name( $obj );
    	echo ($result > 0) ?  'Saved' : 'Can not save';
    }

    function remove_shareholder_name(){
    	$id = $this->input->post('id');
    	$this->mPortfolio->remove_shareholder_name( $id );
    }

    function saveGroupName(){
		$obj = array(
    			'GroupName'=> $this->input->post('name'), 
    			'Shareholder_id' => $this->session->login_id,
    			'Scope'=>'USER'
    			);
		$result = $this->mPortfolio->save_group_name( $obj );
    	echo ($result > 0) ?  'Saved' : 'Can not save';    	
    }

    function remove_group_name(){
    	$id = $this->input->post('id');
    	$this->mPortfolio->remove_group_name( $id );
    }
  
    function getShareholderNames()
    {
    	$this->load->model('Portfolio_model','mPortfolio');    	
    	$data = $this->mPortfolio->getShareholderNames( $this->session->userdata('login_id') );
        $this->output->set_content_type('application/json');
        echo  (is_null($data) ? '[]' : json_encode( $data ));    
    }

    function getStockGroups()
    {
    	$data = $this->mPortfolio->getStockGroups( $this->session->userdata('login_id') );
        $this->output->set_content_type('application/json');
        echo  (is_null($data) ? '[]' : json_encode( $data ));    
    }

  	function getStockTypes()
    {
    	$data = $this->mPortfolio->getStockTypes();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function getStockOffering()
    {
    	$data = $this->mPortfolio->getStockOfferingMethod();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function get_commissionable_offering(){
    	$data = $this->mPortfolio->get_commissionable_offering_methods();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));	
    }

    
    //get the company symbols
    function getSymbols()
    {
    	$data = $this->mPortfolio->getSymbols();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }	

    //delete a particular portfolio
    function removePortfolio(){
    	
    	$id = $this->input->post('id');		//get the portfolio id as posted value
    	$result = $this->mPortfolio->removePortfolio( $id );
    	echo $result;    	

    }

	function save_data(){
		
		$arr = array();
		$this->load->library('Stock');			//the class can now be accesed by Stock object

		//php://input is a read-only stream that allows you to read raw data from the request body.
		//order of post vars //{"frmSymbol":"ACEDBL","frmQty":"","frmRate":"","frmEffRate":"","frmTransDate":"2016-05-06","frmTransNo":"","frmBrokerNo":"","frmRemarks":""}
		$obj = json_decode(file_get_contents('php://input'), true);
		foreach ($obj as $key => $value) {
			$arr[$key] = $value;			//push all enumerated items in the array $arr
		}		
		$arr['login_id'] = $this->session->login_id;		//also pass the login_id
		$local_date = date('Y/m/d h:i:s', time());					
		$arr['Create_date'] = $local_date;
		$data = new Stock();
		$data->initObject( $arr );

		$val = $this->mPortfolio->save_data( $data );
		echo $val;		
	}

	function addStock($portfolio_id=null){
		$obj = array('portfolio_id' => $portfolio_id );	
		$data['page_title'] =  $this->app_name . ' / Add new stock';        
		$data['view_name'] = 'add-stock';
		if(isset($portfolio_id))
			$data['obj'] = $this->mPortfolio->get_portfolio_detail($portfolio_id);
		else{
			$data['obj'] = null;
		}
		$this->load->view( 'base_p', $data );	
	}

	function save_data_arr(){

		$arr = array();		
		$obj = json_decode(file_get_contents('php://input'), true);		
		//take out first 3 items and merge with login_id info, hence creating two arrays.
		//$obj1 = 1 dimensional 		//$obj = 2 dimensional
		$tmp 	= 	array_splice($obj,0,4);		//get 4 items from array $obj starting from 0
		$new	=	array('login_id'=>$this->session->login_id);		
		$new['DP_amount']= $this->config->item('DP_amount_rs');
		$new['Name_transfer']= $this->config->item('name_transfer_rs');

		$obj1 = array_merge($tmp,$new);		
		$ret = $this->mPortfolio->save_portfolio_arr( $obj1, $obj );		
		//$ret = 1;
		echo ( $ret );
	}

	//calculates various rates on stock bought. eg, broker commission, sebon commission etc
    function get_effective_rate()
    {    	
    	$obj = json_decode(file_get_contents('php://input'), true);
    	$eff_broker_per = $this->mPortfolio->get_broker_commission( $obj );
    	
    	if($eff_broker_per != null){
	    	$comm_rs = round(($eff_broker_per/100) * $obj['amount'], 2);
	    	$SEBON_comm_pc = $this->config->item('SEBON_comm_pc');
	    	$SEBON_comm_rs = round(($SEBON_comm_pc/100) * $obj['amount'], 2);
	    	$DP_amount_rs = $this->config->item('DP_amount_rs');
	    	$name_transfer_rs = $this->config->item('name_transfer_rs');    	
	    	$total_payable = round(($comm_rs + $name_transfer_rs + $SEBON_comm_rs + $obj['amount']),2);
	    	$eff_rate_rs = round(($total_payable / $obj['qty']),2);

	    	$arr = array();
	    	$arr['BROKER_comm_rs'] = $comm_rs;
	    	$arr['BROKER_comm_per'] = $eff_broker_per;
	    	$arr['SEBON_comm_rs'] = $SEBON_comm_rs;    	
	    	$arr['EFF_RATE_rs'] = $eff_rate_rs;    	
	    	$arr['TOTAL_PAYABLE_rs'] = $total_payable;
	    	// print_r($arr);
	    	$this->output->set_content_type('application/json')->set_output(json_encode($arr));    	
   		}
   		else
   			$this->output->set_content_type('application/json')->set_output('{}');
    }

	function watchlist(){
		$data['page_title'] = $this->app_name . ' / My watchlist';
		$data['view_name']	= 'watchlist';
		$this->load->view('base', $data);
	}

	function get_watch_list(){
		$this->load->model('Nepse_model','mNepse');
		$login_id = $this->session->login_id;
		$arr = $this->mNepse->get_watch_lists($login_id);		
		header('Content-Type: application/json');	    
	    echo  (is_null($arr) ? '[]' : json_encode( $arr ));	
	}	

	function addtowatch(){
		$action = $_POST['val'];
		$symbol = $_POST['syb'];
		$local_date = $local_date = date('Y/m/d h:i:s', time());
		$arr = array(
			'action' => $action,
			'symbol' => $symbol,
			'login_id' => $this->session->login_id,
			'shareholder_id'=>$this->session->login_id,
			'insert_date' => $local_date
		);		
		$ret = $this->mPortfolio->add_to_watch( $arr );
		echo $ret;
	}

}