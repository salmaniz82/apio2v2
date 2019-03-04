<?php class typeModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'type';

	}

	

	public function listall()
	{

		$types = $this->DB->listall()->returnData();

		if($types != null)
		{
			return $types;
		}
		else {
			return false;
		}

	}

}