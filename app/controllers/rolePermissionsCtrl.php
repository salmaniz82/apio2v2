<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class rolePermissionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'rolepermissions');
	}


	
	public function index()
	{
		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::isAdmin())
			return $this->accessDenied();


		if($rows = $this->module->returnAllRolePermissions())
		{
			$data['permissions'] = $rows;
			$statusCode = 200;
		}
		else {
			$data['message'] = "Permission not found";
			$statusCode = 500;
		}
			$roleModule = $this->load('module', 'role');
			$permssionModule = $this->load('module', 'permissions');
			
			$data['allPermissions'] = $permssionModule->returnAllPermissions();
			$data['allRoles'] = $roleModule->returnAllRoles($this->jwtRoleId());


		return View::responseJson($data, $statusCode);
	}


	public function save()
	{


		if(isset($_POST['role_id']) && isset($_POST['permission_id']))
		{
			$role_id = $_POST['role_id'];
			$permission_id = $_POST['permission_id'];

		}

		else {

			$data['message'] = "Expected values were not found";
			$statusCode = 406;

			return View::responseJson($data, $statusCode);	

		}


		if(!$this->module->checkDuplicate($role_id, $permission_id))
		{

			// then insert new value
			$dataPayload['role_id'] = $role_id;
			$dataPayload['permission_id'] = $permission_id;

			if($last_id = $this->module->insert($dataPayload))
			{
				
				$data['lastRecord'] = $this->module->getById($last_id);
				$statusCode = 200;

				// load permission module

				$userPermissionModule = $this->load('module', 'userpermissions');
				if($affected = $userPermissionModule->permissionUpdateTriggerOnInsert($role_id, $permission_id))
				{
					$data['message'] = "Done with " . $affected . " users permission updated ";
				}

				else {
					$data['message'] = "New Permission Added failed to udpate user permissions";
				}


			}
			else {

				$data['message'] = "Failed while adding new permission";
				$statusCode = 500;
			}

		}
		else {

			$data['message'] = "Permission already associated with this role";
			$statusCode = 406;
		}
		
		return View::responseJson($data, $statusCode);
	}


	public function delete()
	{


		$rolePermissionId = $this->getID();
		$role_id = (int) Route::$params['role_id'];;
		$permission_id = Route::$params['permission_id'];
		

		if($this->module->removeItem($rolePermissionId))
		{

			$data['message'] = "role permission removed";

			$statusCode = 406;

			// if delete is successfull trigger clean userPermission tables
			$userPermissionModule = $this->load('module', 'userpermissions');	


			if($affected = $userPermissionModule->userPermissionTriggerOnDelete($role_id, $permission_id))
			{
				
				$data['count'] = $affected;
				$statusCode = 200;
				$data['meta'] = "removed and triggered worked";
			}
		}

		else {

			$data['message'] = "Failed while removing role permissions";
			$statusCode = 500;

		}


		return View::responseJson($data, $statusCode);


	}


	public function statusToggle()
	{

		$_POST = Route::$_PUT;
		$dataPayload['status'] = $_POST['status'];		
		$dataPayload['role_id'] = (int) $_POST['role_id'];
		$dataPayload['permission_id'] = (int) $_POST['permission_id'];


		if($this->module->statusToggle($dataPayload))
		{

			$userPermissionModule = $this->load('module', 'userpermissions');
			$data['message'] = ($dataPayload['status'] == 0) ? "Permission Disabled" :  "Permission Enabled";
			$data['status'] = $_POST['status'];
			$permissionIdsToUPdate = $userPermissionModule->rolePermissionIds($dataPayload['role_id'], $dataPayload['permission_id']);


			$udpateCount = $userPermissionModule->statusTogglePermissionOnRoleUpdate($permissionIdsToUPdate, $data['status']);


			$data['count'] = $udpateCount;




			$statusCode = 200;
		}

		else {

			$data['message'] = "Failed permission status cannot be updated";
			$data['status'] = $_POST['status'];
			$statusCode = 500;

		}


		return View::responseJson($data, $statusCode);


	}


	


}