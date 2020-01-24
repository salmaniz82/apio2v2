<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class residueModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'residue';
	}


	
	public function save($payload)
	{


		if($last_id = $this->DB->insert($payload))
		{
			return $last_id;
		}

		return false;

	}


	public function updateWithToken($token, $id)
	{

		$datapayload = array('accesstoken'=> $token);

		if($this->DB->update($datapayload, $id))
		{
			return true;
		}

		return false;

	}


	public function forgetEmailHasToken($email)
	{


	}



	public function recoverInformation($id)
	{


		$sql = "SELECT r.id, r.accesstoken, DATE_FORMAT(r.created_at, '%d %b %Y') as datecreated, DATE_FORMAT(r.created_at, '%h:%m %p') as timecreated,  u.name, u.email from residue r INNER JOIN users u on u.id = r.user_id where r.id = $id";


		if($data = $this->DB->rawSql($sql)->returnData())
		{
			return $data;
		}

		return false;

	}


	public function verifyValidity($id)
	{

		$sql = "SELECT r.id, r.accesstoken, DATE_FORMAT(r.created_at, '%d %b %Y') as datecreated, DATE_FORMAT(r.created_at, '%h:%m %p') as timecreated,  u.name, u.email,

		IF( NOW() < (DATE_ADD(created_at, INTERVAL 1 HOUR)), 1, 0 ) AS status 

		 from residue r INNER JOIN users u on u.id = r.user_id where r.id = $id";

		if($data = $this->DB->rawSql($sql)->returnData())
		{
			return $data;
		}

		return false;

	}

	public function removeToken($id)
	{
		if($this->DB->delete($id))
		{
			return true;
		}

		return false;
	}

}