<?php 
class userpermissionsModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'userpermissions';
	}



	public function userPermissionList($user_id, $status = null)
	{

		$sql = "SELECT u.id as user_id, up.permission_id, per.name as 'permission',
		up.status as 'pStatus' from users u 
		inner join userpermissions up on up.user_id = u.id
		inner join permissions per on per.id = up.permission_id 
		where user_id = $user_id ";

		if($status == true)
		{
			$sql .= " AND up.status = 1";
		}

		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}

		return false;

	}

	public function userPermissionConcat($user_id)
	{

		$sql = "SELECT GROUP_CONCAT(CONCAT(per.name)) AS 'permissions' from users u 
			inner join userpermissions up on up.user_id = u.id
			inner join permissions per on per.id = up.permission_id 
			where user_id = $user_id AND up.status = 1";

		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}

		return false;

	}


	public function permissionArrayList($user_id)
	{

		if($row = $this->userPermissionConcat($user_id))
		{
			$concatedList = $row[0]['permissions'];
			return explode(',', $concatedList);
		}

		return false;

	}


	public function permissionUpdateTriggerOnInsert($role_id, $permission_id)
	{
		$sql = "INSERT INTO userpermissions(user_id, permission_id, status)
		SELECT u.id as user_id, rp.permission_id as 'permission_id', 1 as 'status' from users u 
		INNER JOIN rolepermissions rp on u.role_id = rp.role_id 
		WHERE rp.permission_id = $permission_id AND rp.role_id = $role_id";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;	
		}	
		return false;
	}



	public function userPermissionTriggerOnDelete($role_id, $permission_id)
	{
		$sql = "DELETE FROM userpermissions where id IN (
        SELECT * FROM (
    	SELECT uper.id as 'id' from users u 
			INNER JOIN userpermissions uper on u.id = uper.user_id
			WHERE u.role_id = $role_id AND permission_id = $permission_id
    	) AS P ";


    	if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;	
		}	
		return false;

	}


	/*

	GETTING ALL PERMISSION ASSOCIATED TO USER FILTER BY ROLE TYPE
	-------------------------------------------------------------
	SELECT u.id as user_id, rp.permission_id from users u INNER JOIN rolepermissions rp on u.role_id = rp.role_id
	WHERE u.role_id = 4;


	INSERT PERMISSION TO USERS MATCHED FROM ROLE PERMISSIONS
	--------------------------------------------------------
	INSERT INTO userpermissions(user_id, permission_id, status)
	SELECT u.id as user_id, rp.permission_id as 'permission_id', 1 as 'status' from users u 
	INNER JOIN rolepermissions rp on u.role_id = rp.role_id;


	INSERT PERMISSION FOR A SINGLE USER
	-----------------------------------
	INSERT INTO userpermissions(user_id, permission_id, status)	
	SELECT $last_id as 'user_id', rp.permission_id as 'permission_id', rp.status as 'status' from rolepermissions rp where rp.role_id = $role_id;


	GETTING PERMISSION FOR A SINGLE USER
	------------------------------------
	SELECT u.id as user_id, up.permission_id, per.name from users u 
	inner join userpermissions up on up.user_id = u.id
	inner join permissions per on per.id = up.permission_id 
	where user_id = 80 AND up.status = 1;
	*/

	/*

	USER PERMISSION CONCAT
	----------------------
	SELECT GROUP_CONCAT(CONCAT(per.name)) AS 'permissions' from users u 
	inner join userpermissions up on up.user_id = u.id
	inner join permissions per on per.id = up.permission_id 
	where user_id = 80 AND up.status = 1;


	DELETE ROLE PERMISSION TRIGGER UPDATE FOR USER PERMISSIONS
	----------------------------------------------------------	

	DELETE FROM userpermissions where id IN (
    
    SELECT * FROM (
    	SELECT uper.id as 'id' from users u 
	INNER JOIN userpermissions uper on u.id = uper.user_id
	WHERE u.role_id = 1 AND permission_id = 83
    ) AS P 
	
    
);

	*/	





}