<?php class rolesCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'role');
	}


	public function index()
	{
		$data = $this->module->returnAllRoles();

		View::responseJson($data, 200);
	}


}