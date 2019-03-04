<?php 
class quizQuestionsModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'quizquestions';		
	}


	public function quizQuestionsByQuizId($quiz_id)
	{
		$sql = "SELECT qz.id as quiz_id, que.id as question_id from quiz qz
				INNER JOIN questions que on qz.category_id = que.category_id 
				WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL)";

		if($questions = $this->DB->rawSql($sql)->returnData())
		{
			return $questions;
		}

			return false;
	}



	public function allocateQuestionsByQuizId($quiz_id)
	{

		$sql = "INSERT INTO quizQuestions (quiz_id, question_id)
			SELECT qz.id as quiz_id, que.id as question_id from quiz qz
			INNER JOIN questions que on qz.category_id = que.category_id 
			WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL)";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

			return false;
	}


	public function reSyncQuestions($quiz_id)
	{


		$sql = "INSERT IGNORE INTO quizquestions (quiz_id, question_id)
			SELECT qz.id as quiz_id, que.id as question_id from quiz qz
			INNER JOIN questions que on qz.category_id = que.category_id 
			WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL)";
			$this->DB->rawSql($sql);
			return $this->DB->connection->affected_rows;
		
	}


	public function synchronizeCheck($quiz_id)
	{
		/*
			pick only non matching rows from questions which are not in quizquestions
		*/
		$sql = "SELECT GROUP_CONCAT(que.id) as question_id, count(que.id) as quecount from quiz qz
				INNER JOIN questions que on qz.category_id = que.category_id 
				WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL) AND 
    			que.id NOT IN (SELECT question_id from quizquestions where quiz_id = $quiz_id)";


			if($data = $this->DB->rawSql($sql)->returnData())
			{
				return $data;	
			}

			else {
				return false;
			}
	}



	public function statusToggle()
	{




	}


	public function destroy()
	{

	}


	public function listMatchQuestions($quiz_id)
	{
		$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			cat.name as category, 
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer 
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			WHERE qq.quiz_id = $quiz_id";

			if($questions = $this->DB->rawSql($sql)->returnData())
			{
				return $questions;
			}

				return false;
	}


	public function SynchronizeHandler($quiz_id, $queIDs)
	{


		$sql = "INSERT INTO quizquestions (quiz_id, question_id)
			SELECT $quiz_id as quiz_id, que.id as question_id from questions que 
			WHERE que.id IN($queIDs)";


			if($this->DB->rawSql($sql))
			{
				return $this->DB->connection->affected_rows;
			}

				return false;

	}


	public function newSyncAddedQuestions($quiz_id, $queIDs)
	{


		$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			cat.name as category, 
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer 
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			WHERE qq.quiz_id = $quiz_id AND qq.question_id IN ($queIDs)";

			if($questions = $this->DB->rawSql($sql)->returnData())
			{
				return $questions;
			}

				return false;

	}

	public function autoSyncPrivateQuizQuestions($dataPayload)
	{

			if($last_id = $this->DB->insert($dataPayload))
			{
				return $last_id;
			}
			
			return false;

	}



	public function listQuizPlayQuestions($quiz_id, $reqQues)
	{



			$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.type_id, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			cat.name as category, 
            sec.name as 'subDecipline',
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN 
			
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
            INNER JOIN categories sec on que.section_id = sec.id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			WHERE qq.quiz_id = $quiz_id AND qq.status = 1 
			ORDER BY sec.name, RAND() LIMIT $reqQues";

			if($questions = $this->DB->rawSql($sql)->returnData())
			{
				return $questions;
			}

				return false;

	}



}