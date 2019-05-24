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

		$sql = "SELECT que.id as 'question_id', que.status as 'status', que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			que.consumed as 'hits',  
			cat.name as category, sub.name as 'subject', que.section_id as 'subject_id',   
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer,

			(CASE 
            WHEN que.entity_id IS NULL AND que.quiz_id IS NULL 
             THEN 'public' 
             WHEN que.entity_id IS NULL AND que.quiz_id IS NOT NULL 
             then 'private'
             ELSE 'local'
             end ) as 'scope' 

			from questions que 
			
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN categories sub on sub.id = que.section_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			ORDER BY que.status DESC, que.consumed DESC";


		$data = $this->DB->rawSql($sql)->returnData();

		if($data != null)
		{
			return $data;
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