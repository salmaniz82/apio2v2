<?php class servicetestModule extends appCtrl
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'testsw';

	}

	

	public function save($dataPayload)
	{
		if($id = $this->DB->insert($dataPayload))
		{
			return $id;
		}

		return false;
	}

}