<?php 

class jwACL {

	/*

	1. Load MODULES: one
	2. authenticate : one
	3. Get authentiacted user detail
			user_id : done
			role_id : done
			roleName : done
			permission list: done
	6. has
	6. hasAll
	*/

	
	public static function refinePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('/\/+/', '/', $path);

        return $path;
    }

	public static function load($loadType, $Loadentity)
	{

		if($loadType == 'module')
		{

			$path = ABSPATH.'app/modules/'.$Loadentity.'Module.php';
			$path = self::refinePath($path);
			require_once $path;
			$ModuleClass =  $Loadentity.'Module';
			return new $ModuleClass();
		}

		elseif($loadType == 'external')
		{
			
			$path = ABSPATH.'app/external/'.$Loadentity.'.php';
			require_once($path);
			
		}

	}


	public static function Message()
	{
		echo "A message form jwtACL";
	}


	public static function has($checkPermission)
	{

		if(self::isLoggedIn())
		{
			$authPermissions = self::authPermissions();
		return	( in_array($checkPermission, $authPermissions) ) ? true : false;	
		}
		return false;
	}


	public static function getResourceId($resourceName)
	{
		$resModule = self::load('module', 'resource');
		return $resModule->pluckIdByName($resourceName);

	}

	
	public static function authUserId()
	{
		
		if(JwtAuth::validateToken())
		{
			return (int) JwtAuth::$user['id'];
		}
		
		return false;
	}

	public static function isAdmin()
	{
		
		if(JwtAuth::validateToken())
		{
			return (int) (JwtAuth::$user['role_id'] == 1) ? true : false;	
		}

		return false;
	}

	public static function isLoggedIn()
	{
		return (JwtAuth::validateToken()) ? true : false;
	}

	public static function authRole()
	{
		
		if(JwtAuth::validateToken())
		{
			return JwtAuth::$user['role'];
		}

		return false;
	}


	public static function authPermissions()
	{
		$user_id = self::authUserId();
		if($user_id)
		{
			$userPermissionModule = self::load('module', 'userpermissions');

	    	if($permissions = $userPermissionModule->permissionArrayList($user_id))
	    	{
	    		return $permissions;
	    	}
		}
		return false;
	}


}