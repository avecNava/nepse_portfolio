<?php
class Portfolio_model extends CI_Model
{
	private $tblLogin		= 'nepse_login';
	private $tblShareholder	= 'nepse_shareholder';
	private $tblPortfolio	= 'nepse_portfolio';
	private $tblNepsedata	= 'nepse_data';	
	private $tblStockGroup 	= 'nepse_share_group';
	private $tblStockType	= 'nepse_stock_type';
	private $tblStockOffering	= 'nepse_share_offering';
	private $tblCompany		= 'nepse_company';
	private $tblCompanyType	= 'nepse_company_type';
	private $tblCommission	= 'nepse_tax';
	private $tblWatch		= 'nepse_watch';

	public function __construt()
	{
		parent::__construct();		
	}
	
	function getCummulativePortfolioData( $arr )
	{
		//loop via array and join the key-value to form parameter for where
		$where_param = '';	
		$where = '';
		
		foreach ($arr as $field => $col) {
			if( strlen(trim($col)) <= 0 ) continue;
		
			if ($field == 'offr_code' || $field == 'Symbol'){
				$where_param .= 'p.'. $field . ' = "' . $col . '" AND ';
				$where .=  $field . ' = "' . $col . '" AND ';
			}
			else{
				$where_param .= 'p.'. $field . ' = ' . $col . ' AND ';						
				$where .=  $field . ' = ' . $col . ' AND ';						
			}
		}
		$where_param .= 1;		
		$where .= 1;		

		// $nested_where = ' AND 1=1';
		// if(array_key_exists('login_id', $arr)){
		// 	$nested_where .= ' AND login_id=' .$arr['login_id'];
		// }
			
		// if(array_key_exists('shareholder_id', $arr)){
		// 	$nested_where .=' AND shareholder_id='.$arr['shareholder_id'];
		// }
		
		$sql = "SELECT p.Symbol, c.Company, p.offr_code, d.LTP, d.Difference, d.trans_date, d.Insert_date, d.Max_price, d.Min_price,
		    @TotalQty:=(SELECT SUM(Quantity) FROM nepse_portfolio WHERE Symbol = p.Symbol AND ". $where .") AS 'TotalQuantity',		    
		    @Investment:=(SELECT SUM(Effective_rate * Quantity) FROM nepse_portfolio WHERE Symbol = p.Symbol AND Effective_rate >= 10 AND ".$where .") AS 'Investment',
		    (SELECT ROUND(SUM(Quantity * Effective_rate) / SUM(Quantity),2) FROM nepse_portfolio WHERE Symbol = p.Symbol AND ".$where." GROUP BY Symbol) AS 'EffectiveRate',
		    (SELECT MAX(ROUND(DATEDIFF(NOW(),trans_date)/7,0)) FROM nepse_data WHERE Symbol=p.Symbol) as weeks,
		    (SELECT ROUND(AVG(Max_price),2) FROM nepse_data WHERE Symbol=p.symbol AND ROUND(DATEDIFF(NOW(),trans_date)/7,0)<=52) AS weeks52_max,
		    (SELECT ROUND(AVG(Min_price),2) FROM nepse_data WHERE Symbol=p.symbol AND ROUND(DATEDIFF(NOW(),trans_date)/7,0)<=52) AS weeks52_min
		FROM
		    nepse_portfolio p
		    LEFT JOIN nepse_data d ON p.Symbol = d.Symbol AND d.flag_new = 1
		    INNER JOIN nepse_company c ON p.Symbol=c.Symbol
		WHERE ".  $where_param ."
		GROUP BY Symbol";

		$query = $this->db->query( $sql );
		if($query->num_rows() > 0)
			return $query->result_object();	
	}	

	function getPortfolioData( $arr )
	{
		//loop via array and join the key-value to form parameter for where
		$where_param = '';
		foreach ($arr as $field => $col) {
		
			if( strlen(trim($col)) <= 0 ) continue;
		
			if ($field == 'offr_code' || $field == 'Symbol')
				$where_param .= 'p.'. $field . ' = "' . $col . '" AND ';
			else
				$where_param .= 'p.'. $field . ' = ' . $col . ' AND ';						
		}
		$where_param .= 1;

		$sql = "SELECT p.Portfolio_id, p.Symbol, p.Quantity, p.Effective_rate, p.offr_code, 
		p.Purchase_date, DATEDIFF(CURDATE(),p.Purchase_date) as NumberDays, 
		p.Transaction_no, p.Remarks, c.Company, d.LTP, d.Difference, DATE_FORMAT(d.trans_date,'%d %b %Y') as trans_datef, s.shareholder_name, 
		g.GroupName, o.offr_title, ct.type_name as CompanyType, t.stock_type
		FROM ". $this->tblPortfolio ." p 
		LEFT JOIN ". $this->tblNepsedata ." d ON p.Symbol = d.Symbol AND d.flag_new = TRUE
		LEFT JOIN ". $this->tblCompany . " c ON p.Symbol = c.Symbol
		LEFT JOIN ". $this->tblCompanyType . " ct ON ct.type_id = c.type_id
		LEFT JOIN ". $this->tblShareholder . " s ON p.shareholder_id = s.shareholder_id
		LEFT JOIN ". $this->tblStockGroup . " g ON p.GroupID = g.GroupID
		LEFT JOIN ". $this->tblStockOffering . " o ON p.offr_code = o.offr_code		
		LEFT JOIN ". $this->tblStockType . " t ON p.stock_type_id = t.stock_type_id
		WHERE ".  $where_param ;
		
		$query = $this->db->query( $sql );
		if($query->num_rows() > 0)
			return $query->result_object();	
	}		

	//summary on top
	function get_portfolio_summary( $arr, $grp=false)
	{
		//loop via array and join the key-value to form parameter for where
		$where_param = '';
		foreach ($arr as $field => $col) {		
			if( strlen(trim($col)) <= 0 ) continue;		
			if ($field == 'offr_code' || $field == 'Symbol')
				$where_param .= 'p.'. $field . ' = "' . $col . '" AND ';
			else
				$where_param .= 'p.'. $field . ' = ' . $col . ' AND ';						
		}
		$where_param .= 1;

		$sql = "SELECT d.source, ROUND(SUM(p.Quantity*p.Effective_rate), 2) AS 'Investment',
			    ROUND(SUM(p.Quantity*d.LTP), 2) AS 'Worth',
			    ROUND(SUM(p.Quantity*(d.LTP-d.Prev_closing)), 2) AS 'DayGain',
			    ROUND(SUM(p.Quantity*(d.LTP-p.Effective_rate)), 2) AS 'OverallGain',
			    (SELECT 
			        CASE d.source
			        	WHEN 'todaysprice' THEN DATE_FORMAT(MAX(trans_date), '%d %b %Y')
			        	ELSE MAX(trans_date)
			        END
			     FROM nepse_data
				) AS trans_date
				FROM    nepse_portfolio p
				LEFT JOIN nepse_data d ON p.Symbol = d.Symbol    
				WHERE  ".$where_param."
			    AND d.flag_new = TRUE";

			if($grp)	$sql .= " GROUP BY p.Symbol";
			$query = $this->db->query($sql);
			if($query->num_rows() > 0)
				return $query->result_object();	
	}

	function get_portfolio_detail($portfolio_id){
		$sql = 'SELECT * FROM ' . $this->tblPortfolio. ' WHERE Portfolio_id=?';
		$result = $this->db->query($sql, $portfolio_id);
		if($result->num_rows()>0)
			return $result->row();			//include only the first row
	}

	function get_trade_history($symbol, $limit){
		
	$sql="SELECT DISTINCT DATE(d.trans_date) AS trans_date,
	DATE_FORMAT(d.trans_date, '%d %b %Y') AS trans_datef,
	DATEDIFF(NOW(), d.trans_date) AS duration,
    @avg_rate:=(SELECT 
            ROUND(AVG(LTP),2)
        FROM
            nepse_data
        WHERE
            Symbol = d.Symbol
                AND DATEDIFF(NOW(), trans_date) <= ".$limit.") AS average_rate,
    d.Symbol,
    d.Max_price AS Max,
    d.Min_price AS Min,
    d.LTP,
    d.Prev_closing
	FROM
	    `nepse_data` d	      
	WHERE
	    d.`Symbol` = ?
	        AND DATEDIFF(NOW(), d.trans_date) <= ". $limit ."
	ORDER BY d.trans_date ASC";

		$query = $this->db->query($sql, $symbol);
		return $query->result_object();	
	}

	function get_portfolio_count($login_id)
	{
		$total = 0;
		$sql = "SELECT count(*) as total_record FROM " . $this->tblPortfolio . " WHERE login_id =? GROUP BY Symbol ";
		$query = $this->db->query($sql, $login_id);
		if($query->num_rows() >0 )
		{
			$row = $query->row();
			$total = $row->total_record;
		}
		return $total;
	}


	function getShareholderNames( $login_id )
	{
		if(isset($login_id))
		{
		$sql = "SELECT shareholder_name, shareholder_id FROM ". $this->tblShareholder ." WHERE login_id = ? ORDER BY shareholder_name";
		$query = $this->db->query($sql, $login_id);
		if($query->num_rows() > 0)
			return $query->result_object();	
		}
	}

	function get_user_full_name( $login_id )
	{
		if(isset( $login_id )){
			$sql = 'SELECT full_name FROM ' . $this->tblLogin . ' WHERE login_id=?';
			$row = $this->db->query($sql, $login_id)->row();
			if($row != null)
				return $row->full_name;
		}
	}	

	function removePortfolio( $id )
	{
		if( ! is_null( $id ) )
		{
			$this->db->where('Portfolio_id', $id);
			return $this->db->delete($this->tblPortfolio);
		}	
		else
			return "Portfolio ID can not be null  or empty";
	}

	function getStockGroups( $login_id )
	{
		if(isset($login_id))
		{
			$sql = "SELECT GroupID,GroupName FROM ". $this->tblStockGroup . " WHERE scope= 'ALL' OR Shareholder_id =? ORDER BY GroupName" ;
			$query = $this->db->query( $sql, $login_id );
			if($query->num_rows() > 0)
				return $query->result_object();	
		}
	}

	function getStockTypes()
	{
		$sql = "SELECT stock_type_id, stock_type FROM ". $this->tblStockType . " ORDER BY stock_type" ;
		$query = $this->db->query( $sql );
		if($query->num_rows() > 0)
			return $query->result_object();	
	}

	function getStockOfferingMethod()
	{
		$sql = "SELECT offr_code, offr_title FROM ". $this->tblStockOffering . " ORDER BY offr_code" ;
		$query = $this->db->query( $sql );
		if($query->num_rows() > 0)
			return $query->result_object();	
	}	

	function get_commissionable_offering_methods()
	{
		$sql = "SELECT offr_code, offr_title FROM ". $this->tblStockOffering . " WHERE `commissionable`=1 ORDER BY offr_code" ;
		$query = $this->db->query( $sql );
		if($query->num_rows() > 0)
			return $query->result_object();	
	}	

	function getSymbols( )
	{

		$sql = "SELECT Symbol, CONCAT(Company, ' (' ,Symbol, ')') AS Company   FROM ". $this->tblCompany ." ORDER BY Symbol";
		$query = $this->db->query( $sql );
		if($query->num_rows() > 0)
			return $query->result_object();	
	}	

	//save_portfolio_arr obj format
	//Array
	// (
	//     [shareholder_id] => 38
	//     [stock_type_id] => 6
	//     [offr_code] => DIVIDEND
	//     [Symbol] => Array
	//         (
	//             [0] => DDBL
	//             [1] => ALICL
	//         )

	//     [Qty] => Array
	//         (
	//             [0] => 10
	//             [1] => 28
	//         )	
	//saves the portfolio data / form as array
	//$o = single dimensional array . contains (login_id,shareholder_id,offering_mode...)
	//$obj = 2 dimensional arrray. contains portfolio details
	function save_portfolio_arr ( $o, $obj )
	{
		$count =0;
		$commission_applicable = $this->config->item('commission_applicable');

		//if array is 1D save it one way, if 2D loop the array and save it
		if(count($obj) == count($obj,COUNT_RECURSIVE))			//if count=recursive count, then the array is single dimensional
		{
			$data = 
			array(				
				'login_id'		=> $o['login_id'],
				'shareholder_id'=> $o['shareholder_id'],
				'GroupID'		=> $o["GroupID"],
				'Symbol'		=> $obj['Symbol'],
				'Quantity'		=> $obj['Qty'],
				'Rate'			=> isset( $obj['Rate'] ) ? $obj['Rate'] : 0.000001,
				'Effective_rate'=> $obj['effective_rate'],				
				'stock_type_id'	=> $o['stock_type_id'],
				'offr_code'		=> $o['offr_code'],
				'Purchase_date'	=> $obj['Purchase_date'],
				'Broker_no'		=> isset( $o['BrokerNo']) ? $o['BrokerNo'] : null,
				'Transaction_no'=> isset( $obj['TransNo']) ? $obj['TransNo'] : null,
				'Remarks'		=> isset( $obj['Remarks']) ? $obj['Remarks'] : null				
				// 'Ownership_date'=> $obj['Ownership_date'],
				// 'Ownership_date'=> ( $obj['owned'] == 0 ) ? $obj['Ownership_date'] : null,
				// 'Owned'=> $obj['owned']
			);
			//set commission values if applicable
			if( in_array($o['offr_code'], $commission_applicable )){
				$data['BROKER_comm']	= isset($obj['BROKER_comm']) ? $obj['BROKER_comm'] : 0.00;
				$data['SEBON_comm']		= isset($obj['SEBON_comm']) ? $obj['SEBON_comm'] : 0.00;
				$data['DP_amount']		= isset($obj['BROKER_comm']) ? $o['DP_amount'] : 0.00;		//if broker comm is charged, then DP is charged
				$data['Name_transfer']	= isset($obj['BROKER_comm']) ? $o['Name_transfer'] : 0.00;
			}
			//print_r($data);
			if($obj['Portfolio_id']=='-1')
				$this->db->insert($this->tblPortfolio, $data);
			else{
				$this->db->where('Portfolio_id',$obj['Portfolio_id']);
				$this->db->update($this->tblPortfolio, $data);
				}
			$count = $this->db->affected_rows();
			//return ($this->db->last_query());
		}
		else
		{
			$len = count($obj['Symbol']);
			for ($i=0; $i < $len; $i++) 
			{
				$data = 
				array(
					'login_id'		=> $o['login_id'],
					'shareholder_id'=> $o['shareholder_id'],
					'GroupID'		=> $o["GroupID"],
					'Symbol'		=> $obj['Symbol'][$i],
					'Quantity'		=> $obj['Qty'][$i],
					'Rate'			=> isset( $obj['Rate'] ) ? $obj['Rate'][$i] : 0.000001,
					'Effective_rate'=> $obj['effective_rate'][$i],
					'stock_type_id'	=> $o['stock_type_id'],
					'offr_code'		=> $o['offr_code'],
					'Purchase_date'	=> $obj['Purchase_date'][$i],
					'Broker_no'		=> isset( $o['BrokerNo'][$i] )? $o['BrokerNo'][$i] : null,
					'Transaction_no'=> isset( $obj['TransNo'][$i] )? $obj['TransNo'][$i] : null,
					'Remarks'		=> isset( $obj['Remarks'][$i] )? $obj['Remarks'][$i] : null,
					// 'Ownership_date'=> ( $obj['owned'][$i] == 0 ) ? $obj['Ownership_date'][$i] : null,
					// 'Owned'=> $obj['owned'][$i]
				);
				//set commission values if applicable
				if( in_array($o['offr_code'], $commission_applicable )){
					$data['BROKER_comm']	= isset($obj['BROKER_comm'][$i]) ? $obj['BROKER_comm'][$i] : 0.00;
					$data['SEBON_comm']		= isset($obj['SEBON_comm'][$i]) ? $obj['SEBON_comm'][$i] : 0.00;
					$data['DP_amount']		= isset($obj['BROKER_comm'][$i]) ? $o['DP_amount'] : 0.00;		//if broker comm is charged, then DP is charged
					$data['Name_transfer']	= isset( $obj['BROKER_comm'][$i]) ? $o['Name_transfer'] : 0.00;
				}
				//print_r($data);						
				$sql = $this->db->insert_string( $this->tblPortfolio, $data);			
				//echo $sql;
				$result = $this->db->query($sql);
				$count += $this->db->affected_rows();
			}	//for
		}	//end if	
	//return $this->db->last_query();
	return $count;
	}

	function get_broker_commission( $obj )
	{
		$sql = "SELECT tax_per FROM ". $this->tblCommission 
			." WHERE stock_type_id = ? AND action = ? AND offr_code= ? AND ? BETWEEN low_range AND high_range";
		$result = $this->db->query($sql, array($obj['stock_type_id'], $obj['action'], $obj['offr_code'], $obj['amount']));
		return $result->row('tax_per');
	}

	function save_data( $obj ){
			
		$this->db->insert($this->tblPortfolio, $obj);
		return $this->db->affected_rows();

	}

	function save_shareholder_name( $obj )
	{	
		$this->db->insert($this->tblShareholder, $obj);
		return $this->db->affected_rows();
	}

	function save_group_name( $obj )
	{	
		$this->db->insert($this->tblStockGroup, $obj);
		return $this->db->affected_rows();
	}

	function remove_shareholder_name( $id )
	{	//todo : only delete records if there wont' be any orphan records in the portfolio table
		$this->db->where('shareholder_id', $id);
		$this->db->delete($this->tblShareholder);	
	}

	function remove_group_name( $id )
	{
		//todo : only delete records if there wont' be any orphan records in the portfolio table
		$this->db->where('GroupID', $id);
		$this->db->delete($this->tblStockGroup);	
	}

	function add_to_watch( $obj ) {
		if( $obj['action'] == 1 ){			//insert symbol for watch
			$arr = array_slice($obj, 1);	//remove the first element of the array that contains "action=0/1"
			$this->db->insert($this->tblWatch, $arr);
			//echo $this->db->insert_string($this->tblWatch, $arr);
		}
		else
		{
			$where = array('symbol'=>$obj['symbol'],'login_id'=>$obj['login_id']);
			$this->db->delete($this->tblWatch, $where);
		}		
	}
}