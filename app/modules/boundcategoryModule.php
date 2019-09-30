<?php 
class boundcategoryModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'boundcategory';
	}


	public function save($data)
	{

		if($last_id = $this->DB->insert($data))
		{
			return $last_id;	
		}
		else {
			return false;
		}

	}


	public function pluckTopCategoryByUserId($user_id)
	{

		if($user_id = $this->DB->pluck('category_id')->Where("user_id = '".$user_id."'"))
		{
			return $user_id;
		}

		else {
			return false;
		}

	}


}