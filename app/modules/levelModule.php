<?php class levelModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'level';

	}

	

	public function listall()
	{

		$levels = $this->DB->listall()->returnData();

		if($levels != null)
		{
			return $levels;
		}
		else {
			return false;
		}

	}

}