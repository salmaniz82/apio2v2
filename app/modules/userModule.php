<?php 
class userModule extends appCtrl{

	public $DB;
	
	public function __construct()
	{
		$this->DB = new Database();
		$this->DB->table = 'users';
	}



	public function allUsers()
	{
		
		
		$user_id = jwACL::authUserId();
		$role = jwACL::authRole();		

		$sql = "SELECT users.id, role_id, roles.role as 'role', name, email,  
			isLocked, lockedDateTime, loginAttempts, status from users 
			INNER JOIN roles on users.role_id = roles.id ";	

			if($role != 'admin') 
			{
				$sql .= " WHERE users.created_by = $user_id ";
			}

		$user = $this->DB->rawSql($sql)->returnData();

		if($user != null)
		{
			return $user;
		}
		else {
			return false;
		}
	}


	public function userById($id)
	{
		$sql = "SELECT users.id, role_id, roles.role as 'role', name, email,  
			isLocked, lockedDateTime, loginAttempts, status from users 
			INNER JOIN roles on users.role_id = roles.id WHERE users.id = $id LIMIT 1";	

		if(!$user = $this->DB->rawSql($sql)->returnData())
		{
			return false;
		}

		return $user;

		
	}


	public function addNewUser($data)
	{

		$password = $data['password'];

		$hashedPassword = $this->hashPassword($password);

		$data['password'] = $hashedPassword;


		if(!$last_id = $this->DB->insert($data))
		{
			return false;
		}

		return $last_id;

	}

	
	public function emailExists($email)
	{

		if($user_id = $this->DB->pluck('email')->Where("email = '".$email."'"))
		{
			return true;
		}

		else {
			return false;
		}

	}


	public function userByCreds($creds)
	{

		return $this->DB->returnSet($creds['email'], $creds['password']);

	}

	public function changePassword($newPassword, $id)
	{

		 $hashedPassword = $this->hashPassword($newPassword);

		 $data['password'] = $hashedPassword;

		if($this->DB->update($data, $id))
		{
			return true;
		}

		return false;
		
	}


	public function hashPassword($password)
	{

		$hashedPassword = password_hash($password, PASSWORD_BCRYPT, array(
		'cost' => 12
		));


		return $hashedPassword;

	}


	public function userByEmail($email)
	{

		

		$sql = "SELECT users.id, role_id, roles.role as 'role', name, email, password, 
			isLocked, lockedDateTime, loginAttempts, status, 
			TIMESTAMPDIFF(SECOND, lockedDateTime, NOW()) AS 'Secs' from users 
			INNER JOIN roles on users.role_id = roles.id 
			where email = '{$email}' LIMIT 1";	

		$user = $this->DB->rawSql($sql)->returnData();

		if($user != null)
		{
			return $user;
		}
		else {
			return false;
		}
	}

	public function incrementAttempts($attempts, $user_id)
	{

		$data['loginAttempts'] = $attempts;


		if($attempts >= MAX_LOGIN)
		{
			$this->lockUser($user_id);
		}



		return ($this->DB->update($data, $user_id)) ? true : false;
	}

	public function lockUser($user_id)
	{

		$data['isLocked'] = 1;
		$data['lockedDateTime'] = $this->Dt_24();
		$this->DB->update($data, $user_id);

	}

	public function unLockUser($user_id)
	{
		$data['isLocked'] = 0;
		$data['loginAttempts'] = 0;
		$data['lockedDateTime'] = '1000-01-01 00:00:00';
		if($this->DB->update($data, $user_id))
		{
			return true;
		}
		else {
			return false;
		}
	}


	public function disableUser($user_id)
	{

		$this->lockUser($user_id);
		$data['status'] = 0;
		$this->DB->update($data, $user_id);
	}


	public function statusToggle($data, $user_id)
	{


		if($data['status'] == 1)
		{

			$data['loginAttempts'] = 0;
			$data['lockedDateTime'] = '1000-01-01 00:00:00';
		}
		

		if($this->DB->update($data, $user_id))
		{
			return true;
		}
		else {
			return false;
		}

	}


	public function lockedDurationCheck($user_id)
	{

		
	}


	public function pluckIdByEmail($email)
	{

		if($user_id = $this->DB->pluck('id')->Where("email = '".$email."'"))
		{
			return $user_id;
		}

		else {
			return false;
		}

	}


	public function deleteUser($userId)
	{


		if($this->DB->delete($userId, true))
		{
			return true;
		}
		return false;

	}


	public function generateRandomPassword()
	{
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789$#";
    	$pass = array(); //remember to declare $pass as an array
    	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    	
    	for ($i = 0; $i < 8; $i++) {
        	$n = rand(0, $alphaLength);
        	$pass[] = $alphabet[$n];
    	}

    	return implode($pass); //turn the array into a string
	}


	public function singnupDetails($userId)
	{

		$sql = "SELECT u.id, u.name, u.email, u.status, REVERSE(p.ticket) AS ticket from users u 
				INNER JOIN preliminary p on p.user_id = u.id 
				WHERE u.id = $userId LIMIT 1";

		if($ticket = $this->DB->rawSql($sql)->returnData())
		{
			return $ticket;
		}

		return false;

	}


	public function taggedUsersList($entity_id)
	{

		$sql = "SELECT u.id, u.name, u.email, u.status, u.isLocked, u.lockedDatetime, (case when u.created_by = $entity_id then true else false end) as isOwnedBy, r.role as role from users u
		INNER JOIN roles r on r.id = u.role_id
		INNER JOIN taggedusers tu on tu.user_id = u.id
		WHERE tu.entity_id = $entity_id";

		if($users = $this->DB->rawSql($sql)->returnData())
		{
			return $users;
		}

		return false;
	}



	public function singleTaggedUser($userId, $entity_id)
	{

		$sql = "SELECT u.id, u.name, u.email, u.status, u.isLocked, u.lockedDatetime, 
		(case when u.created_by = $entity_id then true else false end) as isOwnedBy, r.role as role from users u 
		INNER JOIN roles r on r.id = u.role_id 
		INNER JOIN taggedusers tu on tu.user_id = u.id 
		WHERE u.id = $userId AND tu.entity_id = $entity_id LIMIT 1";

		if($user = $this->DB->rawSql($sql)->returnData())
		{
			return $user;
		}

		return false;
	}


}