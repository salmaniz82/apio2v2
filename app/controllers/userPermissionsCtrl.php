<?php class userPermissionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'userpermissions');
	}


	public function index()
	{
		
		$user_id = $this->getID();
		if($rows = $this->module->userPermissionList($user_id))
		{
			$data['userPermissions'] = $rows;
			$statusCode = 200;
		}
		else {
			$statusCode = 404;
		}

		return View::responseJson($data, $statusCode);

	}


	public function permissionArrayList()
	{
		$user_id = $this->getID();


		if($permissions = $this->module->permissionArrayList($user_id))
		{
			
			$data['permissions'] = $permissions;
			$statusCode = 200;
		}

		else {
			$data['message'] = "permission found found";
			$statusCode = 500;

		}
		
		return View::responseJson($data, $statusCode);


	}	


	public function resetUserPermission()
	{

		
		
		$userID = (int) $this->getParam('user_id');
		$role_id = (int) $this->getParam('role_id');

		if($resetCount = $this->module->deletePermissionsForSingleUser($userID, $role_id))
		{
			$data['message'] = "User set to defaults with " . $resetCount ." permissions";
			$statusCode = 200;
		}

		else {

			$data['message'] = "Cannot reset user permission please try later";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}



	


	


	
}