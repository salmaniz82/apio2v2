<?php 
class permissionsModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'permissions';		
	}


	public function returnAllPermissions()
	{
		$sql = "SELECT * from permissions order by name ASC";

		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}

		return false;
	}


	public function getById($id)
	{


		if($row = $this->DB->getbyId($id)->returnData())
		{
			return $row;
		}
		else {
			return false;
		}

	}


	
	public function insert($data)
	{
		$data['name'] = trim($data['name']);
		$data['status'] = 1;

		if($last_id = $this->DB->insert($data))
		{
			return $last_id;
		}
		else {
			return false;
		}
	}

}