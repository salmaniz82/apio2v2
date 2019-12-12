<?php 
class roleModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'roles';		
	}


	public function returnAllRoles($role_id)
	{
		if($role_id == 1)
		{
			return $roles =  $this->DB->listall()->returnData();	
		}

		else {

			$sql = "SELECT * FROM roles where id IN ('3', '4', '6')";

			return $this->DB->rawSql($sql)->returnData();

		}
		
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


	public function pluckRoleNameById($roleID)
	{

		return $this->DB->pluck('role')->where("id = '".$roleID."'");
	}

}