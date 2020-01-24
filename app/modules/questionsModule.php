<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class questionsModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'questions';

	}

	

	public function listall()
	{

		$user_id = jwACL::authUserId();
		$role = jwACL::authRole();

		$sql = "SELECT que.id as 'question_id', que.created_at,  u.email as 'author', que.status as 'status', SUBSTRING(fnStripTags(que.queDesc), 1,350) as excerptDesc, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			que.consumed as 'hits', (case when CHAR_LENGTH(fnStripTags(que.queDesc)) > 350 then true else false end) as hasExcerpt,  
			cat.name as category, sub.name as 'subject', que.section_id as 'subject_id',   
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer,   

			que.scope as 'scope' 

			from questions que 
			
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN users u on u.id = que.user_id 
			INNER JOIN categories sub on sub.id = que.section_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id ";

			if($role == 'contributor')
			{
				
				$sql .= "WHERE que.user_id = $user_id ";
			}

			else if($role == 'entity')
			{
				$sql .= "WHERE que.user_id = $user_id OR que.entity_id = $user_id ";	
			}

			else if ($role == 'content developer')
			{
				$sql .= " WHERE que.user_id = $user_id ";	
			}


			$sql .= "ORDER BY que.status DESC, que.consumed DESC";



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



	public function statusToggle($stsValue, $id)
	{

		$payload = array('status'=> $stsValue);


		if($this->DB->update($payload, $id))
		{
			return true;
		}

		return false;
			
	}



	public function getsingle($user_id, $role, $queID)
	{


		$sql = "SELECT que.id as 'question_id', que.category_id, que.section_id, que.type_id, que.level_id,  que.created_at, que.user_id, u.email as 'author', que.status as 'status', que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			que.consumed as 'hits',  
			cat.name as category, sub.name as 'subject', que.section_id as 'subject_id',   
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer,   

			que.scope as 'scope' 

			from questions que 
			
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN users u on u.id = que.user_id 
			INNER JOIN categories sub on sub.id = que.section_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id ";

			$sql .= " WHERE que.id = $queID ";

			$sql .= "ORDER BY que.status DESC, que.consumed DESC";


		if($data = $this->DB->rawSql($sql)->returnData())
		{
			return $data;
		}

		return false;

	}



	public function updateQuestionBasic($payload, $id)
	{

		if($this->DB->update($payload, $id))
		{
			return true;
		}

		return false;

	}


	public function bulkQuestionUpload($payload)
	{
		if($this->DB->multiInsert($payload))
		{
			return true;
		}

		return false;
	}



	public function isOwnerofQuestion($questionId, $authID)
	{
		
		$sql = "SELECT id from questions where id = $questionId AND (user_id = $authID OR entity_id = $authID) LIMIT 1";

		if($this->DB->rawSql($sql)->returnData())
		{
			return true;
		}

		return false;


	}


	public function checkActivateAllocation($questionID)
	{
		
		/*
		pre removal checks
		0. stdQuestion : candiate already consumed that and results were generated based on that questions : dissallow
		1. activeAlloctionTest
		2. activeRemoval = 'not having any side effects'
		3. disableWontAllow further usage from global space
		*/

		$sql = "SELECT count(qq.id) as quizQuestionId from questions que 
			INNER JOIN quizquestions qq on que.id = qq.question_id 
			WHERE qq.status = 1 AND que.id = $questionID GROUP BY que.id";


		if($count = $this->DB->rawSql($sql)->returnData())
		{
			return $count[0]['quizQuestionId'];
		}

		return false;

	}

}


/*

Delete last N NUMBERS of records from tables 

DELETE from questions where id IN(SELECT * FROM (
    SELECT id FROM questions ORDER BY id DESC LIMIT 20
) sub
ORDER BY id ASC);

*/