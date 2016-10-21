<?php
class Nepse_model extends CI_Model
{
	private $tblNepsedata = 'nepse_data';
	private $tblNepseLive = 'nepse_live';
	private $tblNepseCompany = 'nepse_company';
	private $tblNepseTax = 'nepse_tax';
	private $tblNepseStockType = 'nepse_stock_type';
	private $tblNepseShareOffer = 'nepse_share_offering';
	private $tblNepseWatch = 'nepse_watch';
	public function __construt()
	{
		parent::__construct();
		$this->load->database();				
	}
	
	//fetches data from http://www.nepalstock.com.np/stocklive
	//S.N.	Symbol	LTP	LTV	Point Change	%Change	Open	High	Low	Volume	Previous Closing
	public function fetchLIVEStock()
	{
		$arr_string = [0];		//columns that are strings. Numbers shall be fomatted to remove commas
		$this->load->library('simple_html_dom');
		echo "<h4>http://www.nepalstock.com.np/stocklive</h4>";
		$html='';
		$counter = 0; 
		$table_count = 1;

		$arr_row = array();		
		$arr_data = array();	
		
		$url = 'http://www.nepalstock.com.np/stocklive';
		$html = file_get_html( $url );		
	
		foreach ( $html->find("#market-watch .panel-heading") as $x) {
			$date_str = $x->plaintext;
		}
		$txdate = substr($date_str, 5);							//strip As of from string.... As of 2016/07/20 14:25:25
		$txdate_str = str_replace('-', '', substr($date_str, 5,11) );			//remove all dashes from  eg date 2016-09-08 15:00:00. also strip the time
		$txdate_str = trim($txdate_str);
		
		echo 'Transaction date : '. $txdate . '<br>' ;				//full datetime
					
		foreach( $html->find('table tbody') as $element) 
		{	if ($table_count++ > 1 ) break;						//only scrap the first table found			
			foreach($element->find('tr') as $trow)
			{	foreach ($trow->find('td') as $tdata) 
				{  	
			 	 	if( $counter > 11) break;			 		//only 11 cols per row
			 	 	if( $tdata->plaintext == "S.N.") break;		//ignores column heading (eg, S.N., Company)					
					//if( $counter == 0 ) continue;				//skip first td for each row whch contains SN
					echo $tdata->plaintext . ',  ';			
					if( in_array($counter, $arr_string))			//if string column (symbol), no need to strip the commas. String col arrays are defined earlier
						array_push($arr_row, $tdata->plaintext );	
					else
						array_push($arr_row, str_replace( ',', '', $tdata->plaintext ));	//strip commas from numbers values if any and push to array
					$counter++;
				}	//foreach td								
				echo '<br/>';
				$counter = 0;
			 	array_push($arr_data, $arr_row);	//push all the $row level data			 	
			 	$arr_row=array();			//reset the $row array			 	
			}	//foreach	tr
				
		}	//table

		//process all data in the arrays, print and save to database
		foreach ($arr_data as $row) 
		{	
			if (!empty($row)) 
			{   
				//$local_date = date('Y/m/d h:i:s', time());
				//$date_short = date('Ymd', time());
				$row_id = $row[1] .'_'. $txdate_str;	//id=symbol_shortDate
				$source = 'stocklive';				
				
				//reset the flag_new flag of each symbol to 0. The new query will set it 1 indicating its is the new record
				$this->db->set('flag_new',FALSE);	
				$this->db->where( array('Symbol'=>$row[1]));
				$this->db->update( $this->tblNepsedata );
				echo $this->db->last_query() . '<br/>';

				//1.Symbol,2.LTP, 3.LTV,4.Change_point,5.Change_per,6.Open,7.High,8.Low,9.Volume,10.Prev_closing,		->site
				//1.Symbol,2.LTP, 3.LTV, 4.Difference, 5.Difference_per,6.Open_price,7.Max_price,8.Min_price,9.Volume,10.prev_closing->db
				$sql = "INSERT INTO $this->tblNepsedata (ID,Symbol,LTP, LTV, Difference, Difference_per,Open_price,Max_price,Min_price,Volume,Prev_closing,trans_date,flag_new,source) 
				VALUES ('$row_id', '$row[1]', $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10],'$txdate',TRUE,'$source') 
				ON DUPLICATE KEY 
				UPDATE Symbol = '$row[1]', LTP = $row[2], LTV = $row[3], 
				Difference = $row[4], Difference_per = $row[5], 
				Open_price = $row[6], Max_price = $row[7],Min_price = $row[8], 
				Volume = $row[9], Prev_closing = $row[10], 
				trans_date = '$txdate',flag_new=TRUE, source = '$source'";
				echo $sql . '<hr>';

				$this->db->query( $sql );				
			}		 	
		}
		echo 'DONE';
	// $this->output->enable_profiler(TRUE);
	}//end of function fetchStockLive()

//fetches data from nepalstock.com.np... 7 pages of data
	public function fetchStock()
	{

		$this->load->library('simple_html_dom');
		echo "<h4>fetching data from http://nepalstock.com.np (Columns<em>symbol, max_price, min_price, closing_price,prev_closing,Traded Shares, Amount, diff</em>)</h4>";
		$html='';
		$counter; $trans_date;
		$forget=[0,2];		//ignore these columns

		$row=array();		$all = array();		$source=array();
		
		$url = 'http://www.nepalstock.com.np/main/todays_price/index/';
		//prepare the source array. it includes the url with 7 different parameters used for pagination.
		for ($i=1; $i <=7; $i++) { 
			array_push($source, $url . $i);
		}

		//loop via source urls and parse DOM elements
		foreach ($source as $url) 
		{
			//echo '<h4>url :'. $url.'</h4>';
			$html = file_get_html($url);		

			foreach($html->find('table.table') as $element) 
			{	
				foreach($element->find('tr') as $trow)
				{	
					$counter=0;			//reset for every row

					foreach ($trow->find('td') as $tdata) 
					{  	
						//first row with td colspan=10 contains transaction date, grab it
						if (isset($tdata->colspan))
						{
							if( isset($trans_date) ) break;			//if the transaction date is already read, ignore
							if($tdata->colspan=="10")
							{
								$str = $tdata->firstchild()->plaintext;	//get the label which contains date time
								$trans_date = substr($str, 5,strlen($str)-5);
								echo '<h4>NEPSE transaction as of ' . $trans_date . '</h4>';
							}
							break;			//break table data with colspan attributes							
						}
							
						if( $tdata->plaintext == "S.N.") break;		//ignores column heading (eg, S.N., Company)
						
						//<td> with transaction data ... loop and parse them
						echo $tdata->plaintext . ',  ';				

						if(!in_array($counter, $forget))	//search $counter inside $forget array [used to skip certain cols as defined above]
						{	
							//rip off extra spaces between words like "chilime     hydropower" should be "chilime hydropower"
							$cleanStr = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $tdata->plaintext)));							

							if( $counter == 1) {//first column has full company names, so get the respective Symbol								
								array_push($row, $this->getSymbolName( $cleanStr ));	//Get symbol from company name using function
							}
							else
								array_push($row,(float)($tdata->plaintext));	//push other values apart from symbol name									   									
						}

					$counter++;		//increase for every table data <td>
					}//foreach	$tdata
					echo '<br>';
					array_push($all, $row);	//push all the $row level data
					$row=array();			//reset the $row array
				}	//foreach trow
				//echo '<hr>';
			}	//foreach element
		} //foreach url
		$counter =0;
		
		$dt_short = substr($trans_date, 0,11);				//strip time part from datetime
		$dt_short = str_replace('-', '', $dt_short);		//strip dashes from date eg 2016-09-09 resutl date=20160909
		$trans_date = trim($trans_date);

		//process all data in the arrays, print and save to database
		foreach ($all as $row) 
		{	
			if (!empty($row)) 
			{   
				$counter ++;
				
				$row_id = trim($row[0]) . '_' . trim($dt_short);			//eg CHCL_20160909
				$source = 'todaysprice';					
				//0.Symbol,1.Max_price 2. Min_price 3.Closing_price 4. volume (traded shares) 5. Amount 6. Prev_closing, 7. Difference_rs
				//1.Symbol,2.LTP, 3.LTV, 4.Difference, 5.Difference_per,6.Open_price,7.Max_price,8.Min_price,9.Volume,10.prev_closing->db				
				//reset the flag_new flag to false for each symbol being inserted/updated
				$this->db->set('flag_new',FALSE);	
				$this->db->where( array('Symbol'=>$row[0]));
				$this->db->update( $this->tblNepsedata );
				echo $this->db->last_query() . '<br/>';

				$sql = "INSERT INTO $this->tblNepsedata (ID,Symbol,Max_price,Min_price,LTP,volume, total_amount, Prev_closing, Difference, trans_date, source, flag_new) 
				VALUES ('$row_id', '$row[0]', $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], '$trans_date', '$source', TRUE)
				ON DUPLICATE KEY 
				UPDATE Symbol = '$row[0]', Max_price = $row[1],Min_price = $row[2], LTP = $row[3], 
				volume = $row[4], total_amount = $row[5], Prev_closing = $row[6], Difference= $row[7],
				trans_date = '$trans_date', source = '$source', flag_new=TRUE";

				echo $sql . '<hr>';
				$this->db->query( $sql );		
					
			}
			//echo '<br>';
		}
		echo 'SUCCESS';
	// $this->output->enable_profiler(TRUE);
	}//end of function fetchStock()

	function getSymbolName( $company_name )
	{
		$company = substr($company_name,0,150); 
		$nepse_code='';
		$sql = "SELECT Symbol FROM ". $this->tblNepseCompany." WHERE LOWER(Company) LIKE LOWER(?)";
		//$sql = "SELECT * FROM some_table WHERE id = ? AND status = ? AND author = ?"; 
		$query = $this->db->query($sql, $company .'%');
		
		if ($query->num_rows() > 0)
		{
		   $row = $query->row(); 		   
   			$nepse_code = $row->Symbol;
		}
		else
		{	//if the company doesn't exist, insert into company table  with flag
			$nepse_code = mt_rand(8848, 9999) . substr($company_name,0,10);
			$this->db->set('New',1);
			$this->db->set('Symbol', $nepse_code);
			$this->db->set('Company', $company_name);
			$this->db->insert($this->tblNepseCompany );
			echo '<h4 style="display:inline-block"> *NEW** </h4>';
		}
		return $nepse_code;
	}

	function getClosingPrice($symbol)
	{
		$closing_price = 0;
		$sql = "SELECT LTP FROM nepse_data WHERE Symbol = ? AND Insert_date >= (SELECT MAX(Insert_date) FROM nepse_data)";
		$query = $this->db->query($sql, $symbol);
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$closing_price = $row->LTP;
		}
		return $closing_price;
	}

	function getPreviousClosingPrice($symbol)
	{
		$closing_price = 0;
		$sql = "SELECT Prev_closing FROM nepse_data WHERE Symbol = ? AND Insert_date >= (SELECT MAX(Insert_date) FROM nepse_data)";
		$query = $this->db->query($sql, $symbol);
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$closing_price = $row->Prev_closing;
		}
		return $closing_price;
	}	

	function get_market_rate()
	{	//last 30 day's market rates
		$sql = "SELECT d.*, DATE_FORMAT(d.Insert_date, '%Y-%m-%d') AS Insert_datef, c.Company FROM " . $this->tblNepsedata . " d INNER JOIN ". $this->tblNepseCompany ." c ON d.Symbol = c.Symbol WHERE d.Insert_date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()";
			   
		return $this->db->query($sql)->result_array();
	}

	function get_company_details()
	{	
		$sql = "SELECT c.*, d.LTP, d.*, DATE_FORMAT(d.trans_date, '%Y-%m-%d') AS trans_datef, w.Symbol as watch FROM " 
		. $this->tblNepseCompany . " c LEFT JOIN ". $this->tblNepsedata ." d ON c.Symbol = d.Symbol "
		. "LEFT JOIN $this->tblNepseWatch w ON c.Symbol=w.Symbol WHERE d.flag_new = TRUE ORDER BY w.Symbol, c.Symbol";
		return $this->db->query($sql)->result_array();
	}

	function get_watch_lists( $login_id ){
		$sql = "SELECT c.Symbol, c.Company, d.LTP, d.*, DATE_FORMAT(d.trans_date, '%Y-%m-%d') AS trans_datef \n"
	    . "FROM $this->tblNepseWatch w\n"
	    . "LEFT JOIN $this->tblNepseCompany c ON w.Symbol=c.Symbol\n"
	    . "LEFT JOIN $this->tblNepsedata d ON w.Symbol = d.Symbol \n"
	    . "WHERE w.Login_id=$login_id AND d.flag_new=1";
		return $this->db->query($sql)->result_array();
	}

	function get_new_company()
	{	
		$sql = "SELECT DATE_FORMAT(x.create_date, '%Y-%m-%d') AS create_date,    x.symbol,    x.company,
		(SELECT DISTINCT CONCAT('(',Symbol,') ',Company) FROM $this->tblNepseCompany WHERE LEFT(Company,30) = LEFT(x.Company,30) AND New=FALSE LIMIT 1) as similiar_company
		FROM    $this->tblNepseCompany x
		WHERE    x.New = TRUE  OR x.last_change_date >= CURRENT_DATE";
		return $this->db->query($sql)->result_array();
	}

	function remove_company( $symbol )
	{	
		if(strlen($symbol)>2) {			//valid symbol is at least of 3 letters
			$sql = "DELETE FROM ". $this->tblNepseCompany ." WHERE Symbol= ? ";
			return $this->db->query($sql, $symbol);
		}
	}
	
	//update symbol as val received from array [symbol, new_symbo]
	function update_symbol( $obj ){
		
		$local_date = date('Y/m/d h:i:s', time());		
		
		//check if the symbol is being used somewhere
		$sql = 'SELECT Symbol,Company FROM ' . $this->tblNepseCompany . ' WHERE Symbol=?';
		$result = $this->db->query($sql, $obj['new_symbol']);
		
		if ($result->num_rows() > 0)
		{
		   	$row = $result->row(); 					   	
			return  "The symbol <em>" . $row->Symbol . '</em> that you specified has been assigned to <em>'. $row->Company . '</em>.<br/>Please try again with a valid Symbol or contact the system administrator.';	
		}

		//if not used, update it
		$data = array(
			'Symbol' => $obj['new_symbol'],
			'New' => 0,
			'last_change_date' => $local_date,
			'changed_by' => $this->session->login_id
			);

		$this->db->where('Symbol', $obj['symbol']);
		$this->db->update($this->tblNepseCompany, $data);
		$total = $this->db->affected_rows();
		return $total . ' record(s) updated with the Symbol <em>'.$obj['new_symbol'] .'</em>';
	
	}
	
	function get_tax_info()
	{	
		$sql = "select t.*, s.stock_type, o.offr_code, o.offr_title from ". $this->tblNepseTax ." t INNER JOIN ". $this->tblNepseStockType ." s ON t.stock_type_id=s.stock_type_id INNER JOIN ". $this->tblNepseShareOffer." o ON o.offr_code=t.offr_code";
		return $this->db->query($sql)->result_array();
	}

	function save_tax_info( $o )
	{
		$sql = "INSERT INTO " . $this->tblNepseTax . " (tax_id, action, stock_type_id, offr_code, low_range, high_range, tax_per) 
				VALUES ('$o[tax_id]', '$o[action]',$o[stock_type_id],'$o[offr_code]',$o[low_range],$o[high_range],$o[tax_per]) 
				ON DUPLICATE KEY UPDATE offr_code='$o[offr_code]',stock_type_id=$o[stock_type_id] , action='$o[action]', tax_per=$o[tax_per], low_range=$o[low_range], high_range=$o[high_range]";
		return $this->db->query($sql);
	}

	function remove_tax_info( $tax_id )
	{	
		$sql = "DELETE FROM ". $this->tblNepseTax ." WHERE tax_id= ? ";
		return $this->db->query($sql, $tax_id);
	}

}
?>