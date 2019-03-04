<?php 
class roleModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'roles';		
	}


	public function returnAllRoles()
	{
		return $roles =  $this->DB->listall()->returnData();
	}


	public function pluckByRole($rolename)
	{

		return $this->DB->pluck('role')->where("role = '".$rolename."'");
	}

	public function insert($rolename)
	{
		$data['role'] = trim($rolename);
		if($this->DB->insert($data))
		{
			return true;
		}
		else {
			return false;
		}
	}

}