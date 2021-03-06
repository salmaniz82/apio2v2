<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');

class userPermissionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'userpermissions');
	}


	public function index()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::isAdmin())
			return $this->accessDenied();



		$user_id = $this->getID();

		if($user_id === 0)
			return $this->nonIntegorResponse();	
		



		if($rows = $this->module->userPermissionList($user_id))
		{
			$data['userPermissions'] = $rows;
			$data['customPermissionList'] = $this->module->userPermissionCustomList($user_id);
			
			$statusCode = 200;
		}
		else {
			$statusCode = 404;
			$data['message'] = "No Permission found please reset permission";
		}

		return View::responseJson($data, $statusCode);

	}


	public function permissionArrayList()
	{
		

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(!jwACL::isAdmin())
			return $this->accessDenied();



		$user_id = $this->getID();


		if($user_id === 0)
			return $this->nonIntegorResponse();	


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



		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


		if(!jwACL::isAdmin())
			return $this->accessDenied();


		
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



	public function userPrivatePermissionToggle()
	{

		
			if(!jwACL::isLoggedIn()) 
				return $this->uaReponse();	
	

			if(!jwACL::isAdmin())
				return $this->accessDenied();


		$_POST = Route::$_PUT;
		$dataPayload['pStatus'] = $_POST['pStatus'];		
		$dataPayload['user_id'] = (int) $this->getParam('user_id');
		$dataPayload['permission_id'] = (int) $this->getParam('permission_id');


		if($this->module->privateUserPermisstionToggle($dataPayload))
		{
			$data['message'] = ($dataPayload['pStatus'] == 1) ? "User Permission Enabled" : "User Permission Disabled";
			$data['permissionStatus'] = $dataPayload['pStatus'];			
			$statusCode = 200;
		}

		else {

			$data['message'] = "User Permission Status Cannot be updated";
			$statusCode = 500;

		}


		return View::responseJson($data, $statusCode);

	}


	public function saveCustomPermission()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::isAdmin())
			return $this->accessDenied();



		$this->load('external', 'gump.class');

			$gump = new GUMP();


		if(isset($_POST)) 
		{

			$_POST = $gump->sanitize($_POST);

		}

		else {

			return $this->emptyRequestResponse();

		}


		$gump->validation_rules(array(
			
			'user_id' => 'required|integer',
			'permission_id' => 'required|integer',
			
		));



		$pdata = $gump->run($_POST);


		if($pdata === false) 
		{

			// validation failed
			$data['status'] = false;

			$errorList = $gump->get_errors_array();
			$errorFromArray = array_values($errorList);
			$data['errorlist'] = $errorList;
			$data['message'] = $errorFromArray[0];
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}


		if(!isset($_POST['user_id']) || !isset($_POST['permission_id']))
		{

			$statusCode = 406;
			$data['message'] = "Valid user and permisions ids required";

			return View::responseJson($data, $statusCode);

		}

		$dataPayload['user_id'] = $_POST['user_id'];
		$dataPayload['permission_id'] = $_POST['permission_id'];


		if($this->module->checkDuplicate($dataPayload['user_id'], $dataPayload['permission_id']))
		{

			$data['message'] = "Permission Already Assinged to user";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}


		if($last_id = $this->module->save($dataPayload))
		{
			$data['message'] = "New Permission Assigned to user";
			$data['lastAdded'] = $this->module->userPermissionByID($last_id);
			$statusCode = 200;
		}

		else {

			$data['message'] = "Permission Assignment Failed";
			$statusCode = 500;

		}


		return View::responseJson($data, $statusCode);

	}


	public function resetAllUserPermissionUnderRole()
	{

		

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(!jwACL::isAdmin())
			return $this->accessDenied();



		$role_id = $this->getID();

		$deleted = $this->module->deleteUserPermissionForRole($role_id);

		$inserted = $this->module->insertUserPermissionForRole($role_id);

		$data['inserted'] = $inserted;

		$data['deleted'] = $deleted;


		return View::responseJson($data, 200);



	}


	
}