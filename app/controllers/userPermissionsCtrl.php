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


	public function restoreToDefaults()
	{

		return true;

	}



	


	


	
}