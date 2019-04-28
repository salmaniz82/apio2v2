<?php 
class rolepermissionsModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'rolepermissions';
	}


	public function returnAllRolePermissions()
	{
		

		$sql = "SELECT rp.id, rp.role_id, rp.permission_id, rp.status as 'status', 
		per.name as 'permission', r.role as role 
		from rolepermissions rp
		INNER JOIN permissions per on per.id = rp.permission_id
		INNER JOIN roles r on r.id = rp.role_id order by r.role, per.name ASC";

		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}

		return false;

	}


	public function getById($id)
	{

		$sql = "SELECT rp.id, rp.role_id, rp.permission_id, rp.status as 'status', 
		per.name as 'permission', r.role as role 
		from rolepermissions rp
		INNER JOIN permissions per on per.id = rp.permission_id
		INNER JOIN roles r on r.id = rp.role_id WHERE rp.id = $id LIMIT 1";


		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}
		else {
			return false;
		}

	}


	public function insert($dataPayload)
	{
		

		if($last_id = $this->DB->insert($dataPayload))
		{

			return $last_id;
		}
		else {
			return false;
		}
	}


	public function checkDuplicate($role_id, $permission_id)
	{

		if($this->DB->build('S')->Colums()->Where("role_id = '".$role_id."'")->Where("permission_id = '".$permission_id."'")->go()->returnData())
		{
			return true;
		}
		return false;
	
	}



	public function removeItem($id)
	{
		if($this->DB->delete($id))
		{
			return true;
		}

		else {
			return false;
		}

	}


	public function statusToggle($dataPayload)
	{


		$status =  $dataPayload['status'];
		$role_id = $dataPayload['role_id'];
		$permission_id = $dataPayload['permission_id'];
		
		$sql = "UPDATE rolepermissions SET status = $status WHERE role_id = $role_id AND permission_id = $permission_id";
		$this->DB->rawSql($sql);
		return $this->DB->connection->affected_rows;

	}


}