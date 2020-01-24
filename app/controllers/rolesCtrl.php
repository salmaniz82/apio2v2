<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class rolesCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'role');
	}


	public function index()
	{
		
		$role_id = $this->jwtRoleId();

		$data = $this->module->returnAllRoles($role_id);

		View::responseJson($data, 200);
	}


}