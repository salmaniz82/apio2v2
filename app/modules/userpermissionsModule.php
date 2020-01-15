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


	public function userPermissionByID($id)
	{

		$sql = "SELECT u.id as user_id, up.permission_id, per.name as 'permission',
		up.status as 'pStatus' from users u 
		inner join userpermissions up on up.user_id = u.id
		inner join permissions per on per.id = up.permission_id 
		WHERE up.id = $id";

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
    	) AS P )";


    	if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;	
		}	
		return false;

	}



	public function deletePermissionsForSingleUser($userID, $role_id)
	{
		$sql = "DELETE FROM userpermissions where user_id = $userID";
		$this->DB->rawSql($sql);

		return $this->insertUserPermissionSingleUser($userID, $role_id);
		
	}


	public function insertUserPermissionSingleUser($userID, $role_id)
	{

		$sql = "INSERT INTO userpermissions(user_id, permission_id, status)	
		SELECT $userID as 'user_id', rp.permission_id as 'permission_id', rp.status as 'status' from rolepermissions rp where rp.role_id = $role_id";
		$this->DB->rawSql($sql);
		return $this->DB->connection->affected_rows;

	}


	public function privateUserPermisstionToggle($dataPayload)
	{

		$user_id = $dataPayload['user_id'];
		$permission_id = $dataPayload['permission_id'];
		$status = $dataPayload['pStatus'];



		$sql = "UPDATE userpermissions SET status = $status WHERE user_id = $user_id AND permission_id = $permission_id";


		$this->DB->rawSql($sql);


		if($this->DB->connection->affected_rows != 0)
		{
			return true;
		}

		return false;

	}


	public function userPermissionCustomList($user_id)
	{


		$sql = "SELECT per.id as 'id', per.name as 'permission' from permissions per
		WHERE id NOT IN (select permission_id as 'id' from userpermissions where user_id = $user_id)";


		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}

		return false;

	}



	public function save($dataPayload)
	{


		if($lastId = $this->DB->insert($dataPayload))
		{
			return $lastId;
		}

		return false;

	}


	public function checkDuplicate($user_id, $permission_id)
	{

		if($this->DB->build('S')->Colums()->Where("user_id = '".$user_id."'")->Where("permission_id = '".$permission_id."'")->go()->returnData())
		{
			return true;
		}
		return false;
	
	}


	public function rolePermissionIds($role_id, $permission_id)
	{
		$sql = "SELECT GROUP_CONCAT(CONCAT(uper.id)) as 'ids' from users u 
		INNER JOIN userpermissions uper on u.id = uper.user_id WHERE u.role_id = $role_id AND permission_id = $permission_id";


		if($row = $this->DB->rawSql($sql)->returnData())
		{
			$concatedList = $row[0]['ids'];
			return $concatedList;
		}

	}



	public function statusTogglePermissionOnRoleUpdate($uperIds, $status)
	{

		$sql = "UPDATE userpermissions SET status = $status WHERE id IN ($uperIds) ";
		$this->DB->rawSql($sql);
		return $this->DB->connection->affected_rows;

	}



	public function assignNewUserDefaultRolePermissions($user_id, $role_id)
	{
		$sql = "INSERT INTO userpermissions (user_id, permission_id) 
		SELECT $user_id as 'user_id', permission_id FROM rolepermissions where role_id = $role_id";

		if($this->DB->rawSql($sql))
		{
			return true;
		}

		return false;

	}



	public function resetDefaultPermissionForAllUserUnderRole($role_id)
	{


		$this->deleteUserPermissionForRole($role_id);

		$this->insertUserPermissionForRole($role_id);



	}


	public function insertUserPermissionForRole($role_id)
	{

		$sql = "INSERT INTO userpermissions(user_id, permission_id, status) 
    	SELECT u.id as user_id, rp.permission_id as 'permission_id', rp.status as 'status' from users u 
		INNER JOIN rolepermissions rp on u.role_id = rp.role_id WHERE u.role_id = $role_id";


		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

		else {
			return false;
		}

	}



	public function deleteUserPermissionForRole($role_id)
	{

		$sql = "DELETE FROM userpermissions where user_id IN (

       		SELECT u.id FROM users u where u.role_id = $role_id 

    	)";

    	if($this->DB->rawSql($sql))
    	{
    		return $this->DB->connection->affected_rows;
    	}

    	return false;


	}



	public function postUserUploadCanidatePermissionAssignment($entity_id, $maxId = null)
	{


		$sql  = "INSERT IGNORE INTO userpermissions (user_id, permission_id, status) 
			SELECT u.id as user_id, p.id as permission_id, rp.status as status from users u  
			INNER JOIN roles r on r.id = u.role_id 
			INNER JOIN rolepermissions rp on rp.role_id = r.id 
			INNER JOIN permissions p on p.id = rp.permission_id 
			where u.role_id = 4 AND created_by = $entity_id ";

			if($maxId != null)
			{
			  $sql .= "AND u.id > $maxId ";	
			}

			if($this->DB->rawSql($sql))
	    	{
	    		return $this->DB->connection->affected_rows;
	    	}
	    	
	    	return false;	


	}


	/*
	post mass insert via upload
	INSERT IGNORE INTO userpermissions (user_id, permission_id) 
	SELECT u.id as user_id, p.id as permission_id from users u  
	INNER JOIN roles r on r.id = u.role_id 
	INNER JOIN rolepermissions rp on rp.role_id = r.id 
	INNER JOIN permissions p on p.id = rp.permission_id 
	where u.role_id = 4 AND created_by $entity_id AND u.id > 137;
	*/



	/*

	GETTING ALL PERMISSION ASSOCIATED TO USER FILTER BY ROLE TYPE
	-------------------------------------------------------------
	SELECT u.id as user_id, rp.permission_id from users u INNER JOIN rolepermissions rp on u.role_id = rp.role_id
	WHERE u.role_id = 4;


	INSERT PERMISSION FOR ALL USERS MATCHED FROM ROLE PERMISSIONS
	--------------------------------------------------------
	INSERT INTO userpermissions(user_id, permission_id, status)
	SELECT u.id as user_id, rp.permission_id as 'permission_id', rp.status as 'status' from users u 
	INNER JOIN rolepermissions rp on u.role_id = rp.role_id;


	INSERT PERMISSION FOR A SINGLE USER
	-----------------------------------
	INSERT INTO userpermissions(user_id, permission_id, status)	
	SELECT $user_id as 'user_id', rp.permission_id as 'permission_id', rp.status as 'status' from rolepermissions rp where rp.role_id = $role_id;


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