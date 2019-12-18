<?php 
class jwtauthCtrl extends appCtrl {


	public function check()
	{
		if(JwtAuth::hasToken())
		  {	      
	  		if(JwtAuth::validateToken())
	  		{
	  			$data['message'] = "Token Authenticated";
		    	$data['status'] = true;
		    	View::responseJson($data, 200);
	  		}
	  		else {
	  			$data['message'] = "Invalid Token";
		    	$data['status'] = false;
		    	View::responseJson($data, 401);
	  		}
		  }
		  else
		      {
		          $data['message'] = "Un Authenticated token was not provided";
		          $data['status'] = false;
		          View::responseJson($data, 403);
		      }
	}


	public function attachProfileLogoWithPayload($userId)
	{


		$profileModule = $this->load('module', 'profile');


		if($logo = $profileModule->autoProfileLogo($userId))
		{
			/*
			check if file exist and its readable and its an image
			*/

			if(!file_exists(ABSPATH.$logo))
			{
				return false;
			}

			return $logo;

		}


		return false;


	}


	public function login()
	{

	    $creds = array(
	    	'email'=> $_POST['email'],
	    	'password' => $_POST['password']
	    );


	    $userModule = $this->load('module', 'user');

	    if($user = $userModule->userByEmail($creds['email']))
	    {

	    	$user_id = $user[0]['id'];

	    	$userLockedDuration = $user[0]['Secs'];
	    	$inputPassword = $creds['password'];
	    	$storedPassword = $user[0]['password'];
	    	/*
		    	1. when user is not found at all with email
		    	2. user found with status is not 1
	    		3. user found with password didn't match
	    		4. user found but it is locked
	    	*/
	    	
	    	if($user[0]['isLocked'] == 1 && $userLockedDuration >= LOC_DUR)
	    	{
	    		
	    		if($userModule->unLockUser($user_id))
    			{
    				$user[0]['isLocked'] = 0;
    			}
	    		
	    	}
			
			
	    	

	    	if( $user[0]['status'] == 1 && $user[0]['isLocked'] == 0 &&  password_verify($inputPassword, $storedPassword) )
	    	{

	    		unset($user[0]['password']);
	    		unset($user[0]['Secs']);
	    		unset($user[0]['isLocked']);
	    		unset($user[0]['lockedDateTime']);
	    		unset($user[0]['loginAttempts']);   

	    		$userModule->unLockUser($user[0]['id']);

	    		$userPermissionModule = $this->load('module', 'userpermissions');

	    		if($permissions = $userPermissionModule->permissionArrayList($user_id))
	    		{
	    			$user[0]['permissions'] = $permissions;	
	    		}

	    		if($user[0]['role'] == 'entity')
	        	{
	        		$user[0]['profileLogo'] = $this->attachProfileLogoWithPayload($user[0]['id']);	

	        	}


	    		$payload = $user[0];
	    		$token = JwtAuth::generateToken($payload);
	        	$data['status'] = true;
	        	$data['message'] = 'user found';
	        	$data['token'] = $token;
	        	$data['user'] = $payload;
	        	

	        	$statusCode = 200;

	    	}

	    	else if($user[0]['status'] == 1 && $user[0]['isLocked'] == 1 && password_verify($inputPassword, $storedPassword) )
	    	{
	    		$data['status'] = false;
	            $statusCode = 401;
	            $data['duration'] = $userLockedDuration;
	            $data['message'] = "Account locked try later after ". (LOC_DUR - (int) $userLockedDuration) ." Seconds";
	    	}
	    	else if($user[0]['status'] == 1 && $user[0]['isLocked'] == 1 && !password_verify($inputPassword, $storedPassword) )
	    	{
	    		$attempts = ++$user[0]['loginAttempts'];
	    		$user_id = $user[0]['id'];
		   		$userModule->incrementAttempts($attempts, $user_id);
		   		$data['duration'] = $userLockedDuration;
	    		$data['status'] = false;
	            $statusCode = 401;
	            $data['attempt'] = $attempts;

	            $data['message'] = "Locked Account & will disabled after 10 Attempts " . $attempts . ' Out of ' . 10 . " attempts";

	            if($attempts >= 10)
	            {
	            	$userModule->disableUser($user_id);
	            }


	    	}
	    	
	    	else if($user[0]['status'] == 0 && (password_verify($inputPassword, $storedPassword) || !password_verify($inputPassword, $storedPassword)) )
	    	{
	    		$data['status'] = false;
	            $statusCode = 401;
	            $data['duration'] = $userLockedDuration;
	            $data['message'] = 'Account has been disabled contact Adminstrator';
	    	}
	    	else if($user[0]['status'] == 1 && $user[0]['isLocked'] == 0 && !password_verify($inputPassword, $storedPassword))
	    	{
	    		// activate attempt increment
	    		$attempts = ++$user[0]['loginAttempts'];
	    		$user_id = $user[0]['id'];
		   		$userModule->incrementAttempts($attempts, $user_id);
		   		$data['duration'] = $userLockedDuration;
	    		$data['status'] = false;
	            $statusCode = 401;
	            $data['attempt'] = $attempts;
	            $data['message'] = "Invalid Credentials " . $attempts . ' Out of ' . MAX_LOGIN . " attempts";
	    	}
	    	else {


	    		$data['status'] = false;
	        	$statusCode = 401;
	        	$data['message'] = 'Invalid Credentials pass did not match';

	    	}

	    }
	    else {
	    	// no user matched with email
	    	$data['status'] = false;
	        $statusCode = 401;
	        $data['message'] = 'Invalid Credentials';
	    }

	    return View::responseJson($data, $statusCode);

	    die();
	    

	    if( $payload = JwtAuth::findUserWithCreds($creds) )
	    {
	        $token = JwtAuth::generateToken($payload);
	        $data['status'] = true;
	        $data['message'] = 'user found';
	        $data['token'] = $token;
	        $data['user'] = $payload;
	        $statusCode = 200;
	    }
	    else
	        {
	            $data['status'] = false;
	            $statusCode = 401;
	            $data['message'] = 'Invalid Credentials';
	            
	        }

	        
	}

	public function validateToken()
	{

		if( JwtAuth::validateToken() )
	    {
	        $data['status'] = true;
	        $data['message'] = 'user found';
	        $data['user'] = JwtAuth::$user;
	        return View::responseJson($data, 200);
	    }
	    else
	    {
	        $data['status'] = false;
	        $data['message'] = 'not a valid token';
	        return View::responseJson($data, 401);
	    }

	}

	public function adminOnlyProtected()
	{
		if( JwtAuth::validateToken() && JwtAuth::$user['role_id'] == 1)
    	{
        	$data['message'] = "you are admin you can access this route";
        	return View::responseJson($data, 200);
    	}

    	else {
        	$data['message'] = "Un Authorize attempt you don not have permission to access this route";
        	return View::responseJson($data, 401);
    	}
	}


	public function register()
	{
		$db = new Database();
		$db->table = 'users';

		$user['name'] = $_POST['name'];
		$user['email'] = $_POST['email'];
		$user['role_id'] = 3;

		$password = $_POST['password'];
		
		$password = password_hash($password, PASSWORD_BCRYPT, array(
		'cost' => 12
		));

		$user['password'] = $password;

		if( $db->insert($user) ) 
		{

			$data['title'] = 'Congratulations';
			$data['message'] = 'You are now registered you can use your credentials to login';
			$statusCode = 200;
			
		} else 
			{

			$data['message'] = 'Some thing went wrong during registration';
			$statusCode = 500;
			
			}

		view::responseJson($data, $statusCode);
	}

}