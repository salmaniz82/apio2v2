<?php class questionsModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'questions';

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



	public function store($dataPayload)
	{

		if($last_Id = $this->DB->insert($dataPayload))
		{
			return $last_Id;
		}

		return false;

	}


	public function summaryCount()
	{
		$sql = "SELECT count(que.id) as 'noQues', que.category_id, que.section_id, cat.name as 'category', sec.name as 'section' from questions que
			inner join categories cat on cat.id = que.category_id
			inner join categories sec on sec.id = que.section_id
			GROUP BY que.category_id, que.section_id";

		if($data = $this->DB->rawSql($sql)->returnData())
		{
			return $data;
		}
		else {

			return false;
		}
	}



	


}