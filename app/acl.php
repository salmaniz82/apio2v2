<?php 

class ACL {

	/*

		$allowedRoles = [1,3,4];
    	if( JwtAuth::validateToken() && in_array((int) JwtAuth::$user['role_id'], $allowedRoles) )
    	{
    		$user_id = (int) JwtAuth::$user['id'];
    		$role_id = (int) JwtAuth::$user['role_id'];
		}
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
		echo "A message form ACL";
	}


	public static function isPermitted($resourceName, $scope = null)
	{
		/*

		$scope null will check for both has to be true;
		private will check only private
		public will check only public
		
		*/

		if(JwtAuth::validateToken())
		{
			
			
			$authRoleId = (int) JwtAuth::$user['role_id'];
						 
			$resId = self::getResourceId($resourceName);
			$permissionModule = self::load('module', 'permissions');
            $postData['role_id'] = $authRoleId;
            $postData['resource_id'] = $resId;
            if($permission = $permissionModule->checkDuplicate($postData))
            {
            	// not to check duplicate but to trick if permission exists
            	if($scope == null)
            	{ // check for both
	            	if($permission[0]['public'] == '0' || $permission[0]['private'] == '0')
	            	{
	            		return false;
	            	}
	            	else {
	            		return true;
	            	}

            	}

            	else if ($scope == 'public' && $permission[0]['public'] == '1'){

            		return true;

            	}

            	else if ($scope == 'private' && $permission[0]['private'] == '1'){

            		return true;

            	}

            	else {
            		return false;
            	}

            }	            
            else {

            	return false;

            }

    	}

        else {

        	return false;
		}


	}


	public static function getResourceId($resourceName)
	{
		$resModule = self::load('module', 'resource');
		return $resModule->pluckIdByName($resourceName);
	}


	public static function authUserId()
	{
		return (int) JwtAuth::$user['id'];
	}

	public static function isAdmin()
	{
		if(JwtAuth::validateToken())
		{
			return (JwtAuth::$user['role_id'] == 1) ? true : false;	
		}
		else {
			return false;
		}	
	}

	public static function isLoggedIn()
	{
		return (JwtAuth::validateToken()) ? true : false;
	}


}