<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class permissionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'permissions');
	}


	public function index()
	{

		if($rows = $this->module->returnAllPermissions())
		{
			$data['permissions'] = $rows;
			$statusCode = 200;

		}
		else {

			$data['message'] = "Permission not found";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}


	public function save()
	{

		
		if(isset($_POST['name']) && $_POST['name'] != null)
		{

				// $dataPayload['name'] = slugify($_POST['name']);

				$dataPayload['name'] = trim($_POST['name']);

				if($this->module->checkDuplicate($dataPayload['name']))
				{

					$data['message'] = "Permission Already Exists";
					$statusCode = 406;
					return View::responseJson($data, $statusCode);
				}

				if($last_id = $this->module->insert($dataPayload))
				{
					$data['message'] = "New Permission created";
					$statusCode = 200;
					if($lastRecord = $this->module->getById($last_id))
					{
						$data['permission'] = $lastRecord;
					}
				}
				else {
					$data['message'] = "Failed While adding new permission";
					$data['debug'] = $this->module->DB;
					$statusCode = 500;
				}	
		}

		else {
			$data['message'] = "Mission Permission Name";
			$statusCode = 406;
		}
		
		return View::responseJson($data, $statusCode);
	}


	public function delete()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		
		
		if(!jwACL::has('permission-remove')) 
			return $this->accessDenied();


		
		if(!$id = (int) $this->getID())
		{

			$data['message'] = "Expecting Permission ID to be removed";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);		
			die();					
		}


		if(!$this->module->getById($id))
		{
			$data['message'] = "Permission does not exists";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);		
			die();					
		}


		if($this->module->isLinkedPermissionCheck($id))
		{
			$data['message'] = "Linked or Non-Orphan Permissions cannot be removed";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);		
			die();			
		}


		if($this->module->destroy($id))
		{
			$data['message'] = "Permission Removed Successfully";
			$statusCode = 200;
			return View::responseJson($data, $statusCode);		
			die();
		}


	}



	


}