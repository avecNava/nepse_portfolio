<?php
class AccountModel extends CI_Model  
{
	private $tblLogin = 'nepse_login';
	private $tblShareholder  = 'nepse_shareholder';
	private $tblShareholderGroup  = 'nepse_share_group';
	private $tblPortfolio ='nepse_portfolio';
	public function __construct()
    {
        parent::__construct();
        $this->load->database();				        
    }   

	
	function get_user_details( $login_id )
	{
		if( isset( $login_id) ) {
			$sql = "SELECT login_id, full_name, active, email FROM ". $this->tblLogin ." WHERE login_id = ? ";
			$qry = $this->db->query($sql, $login_id);

			if ($qry->num_rows() > 0)
				return $qry->row();	
		}
		return false;

	}

	function get_user_details_by_email( $email )
	{
		if( isset($email)){
		$sql = "SELECT login_id, full_name, email, unique_string, active, privilege FROM ". $this->tblLogin ." WHERE email = ? ";
		$qry = $this->db->query($sql, $email);

		if ($qry->num_rows() > 0)
			return $qry->row();		 
		}

		return false;
	}	

	function update_user_roles($obj)		//$obj contains [id, target(active? or privilege?), state]
	{
		$data = array($obj['target'] => $obj['state']);
		$this->db->where('login_id',$obj['id']);
		$this->db->update($this->tblLogin, $data);
		return $this->db->affected_rows();
	}


	function setLastLoginDateTime($login_id)		//$obj contains [id, target(active? or privilege?), state]
	{
		date_default_timezone_set('Asia/Kathmandu');
		$local_date = date('Y/m/d h:i:s', time());					
		$this->db->set('last_login_date', $local_date); 
		$this->db->where('login_id', $login_id );
		$this->db->update($this->tblLogin);		
	}

	//list detail of every users
	function get_all_user_status(){
		$this->db->select('*, DATEDIFF(CURDATE(), last_login_date) as login_days, DATEDIFF(CURDATE(), create_date) as create_days');
		$row=$this->db->get($this->tblLogin);
		return $row->result_object();
	}

	//get user roles
	function get_user_roles($email){
		if ( isset( $email )) {
			$sql = "SELECT CASE WHEN email=? THEN 1 ELSE 0 END AS myself, login_id, full_name,email, active, privilege FROM " . $this->tblLogin;
	    	$row=$this->db->query($sql, $email);
			return $row->result_object();
		}	
	}

	function get_decrypted_password_by_email($email)
	{
		if (isset($email)) {
			$sql = "SELECT password FROM ". $this->tblLogin ." WHERE email = ? AND active = true";
			
			$rs=$this->db->query($sql, $email)->row();
			if ($rs)
				return $this->encrypt->decode($rs->password);		
		}
	}

	/*returns a decrypted password*/
	function get_decrypted_password($login_id)
	{
		if (isset($login_id)) {
			$sql = "SELECT password FROM ". $this->tblLogin ." WHERE login_id = ?";
			
			$rs=$this->db->query($sql, $login_id)->row();
			if ($rs)
				return $this->encrypt->decode($rs->password);		
		}
	}

	function UpdatePassword( $login_id, $password )
	{
		$local_date = date('Y/m/d h:i:s', time());
		$data=array('Password'=>$password, 'password_changed_on' => $local_date, 'password_changed_by'=>$login_id );
		$this->db->where('login_id',$login_id);
		return $this->db->update($this->tblLogin, $data);		
	}

	
	function IsAuthenticated($email,$password)
	{
		$options=array('email'=>$user_name,'Password'=>$password);
		$this->db->where($options);
		$result=$this->db->get($this->tblAccount);
		return ($result->num_rows()>0 ? true : false);
	}
	
	function is_password_valid($login_id, $password)
	{
		$pwd = $this->get_decrypted_password( $login_id );

		return $password == $pwd ? true : false;
	}	
	
	function GetEmail($staff_guid)
	{
		if($staff_guid!=null){
			
			$this->db->where('StaffGUID',$staff_guid);
			$this->db->select('PrimaryEmail');
			$row=$this->db->get($this->tblPerson)->row();
			
			if($row!=null)
			{
				return ($row->PrimaryEmail);
			}
		}
		return null;
	}
	
	
	function IsLoginActive($email){
		
		$sql = 'SELECT email FROM '.$this->tblAccount.' WHERE email="'.$email.'" AND active =1';
		$rs=$this->db->query($sql);
		return $rs->num_rows > 0 ? true : false ;
		
	}	
	
	function is_email_available($email)
	{	
		$sql = "SELECT COUNT(login_id) AS total FROM " . $this->tblLogin . ' WHERE email = ?';
		$result = $this->db->query($sql, $email)->row();
		return ($result->total > 0 ? true : false);			
	}

	//receives an array of values and saves them
	function create_login( $obj ){
		return $this->db->insert($this->tblLogin, $obj);
	}
	
	//checks if the password has already been generated using the UNIQUE String
	function IsPasswordAlreadyChanged($email, $unique_str)
	{
		$sql= 'SELECT StaffGUID FROM '	. $this->tblLogin . ' WHERE email=' . $email . ' AND unique_string="' . $unique_str . '"';
		return $this->db->query($sql)->result();		
	}
	
	function update_unique_string($login_id, $unique_string)
	{
		$param = array('unique_stringx'=>$unique_string, 'login_idx'=>$login_id);
		$sql = "UPDATE " . $this->tblLogin . " SET unique_string = ? WHERE login_id =?";
		$this->db->query($sql, $param);
	}	

	function get_unique_string($login_id)
	{
		$sql = "SELECT unique_string FROM " . $this->tblLogin . " WHERE login_id =?";
		$row = $this->db->query($sql, $login_id)->row();
		if($row != null)
			return ($row->unique_string);
		return null;
	}	

	function set_unique_string_null($login_id)
	{
		$this->db->where('login_id', $login_id);
		return $this->db->update($this->tblLogin, array('unique_string'=>null));
	}
		
	function UpdateLoginDate($staff_guid,$date,$ip_address,$user_agent)
	{
		$this->db->set('LastLoginDate',$date,false);
		$this->db->set('IPAddress',$ip_address);
		$this->db->set('UserAgent',$user_agent);
		$this->db->where('StaffGUID',$staff_guid);
		$this->db->update($this->tblLogin);
	}
	
	function set_account_active( $login_id ){
		
		if( isset($login_id) ){
			$this->db->set('active',1);
			$this->db->where('login_id',$login_id);
			$result=$this->db->update($this->tblLogin);
			return $result;
		}
		return -1;
	}

	//save shareholder name
	function save_shareholder_name( $name, $login_id ){
		$this->db->set('shareholder_name', $name );
		$this->db->set('login_id', $login_id );
		$this->db->insert($this->tblShareholder);
	}

	//save shareholder name
	function save_sharegroup_name( $name, $login_id ){
		$this->db->set('GroupName',$name );
		$this->db->set('Shareholder_id', $login_id );
		$this->db->insert($this->tblShareholderGroup);
	}	

	//remove_shareholder_group
	function remove_shareholder_group( $param ){
		$sql = "DELETE FROM " . $this->tblShareholderGroup . " WHERE GroupID=? \n"
	    . "AND 0 = (SELECT COUNT(1) FROM " . $this->tblPortfolio . " WHERE GroupID = ? AND login_id = ?)";
		$rs = $this->db->query($sql, array($param['GroupID'], $param['GroupID'], $param['login_id']));
		return $this->db->affected_rows();
	}

	//remove_shareholder_group
	function remove_shareholder( $param ){
		$sql = "DELETE FROM " . $this->tblShareholder . " WHERE shareholder_id=? \n"
		. "AND 0 = (SELECT COUNT(1) FROM " . $this->tblPortfolio . " WHERE shareholder_id = ? AND login_id = ?)";
		$rs = $this->db->query($sql, array($param['shareholder_id'], $param['shareholder_id'], $param['login_id']));
		return $this->db->affected_rows();
	}
}
//end of file