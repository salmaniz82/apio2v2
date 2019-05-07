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

		/*

		$sql = "INSERT INTO quizQuestions (quiz_id, question_id)
			SELECT qz.id as quiz_id, que.id as question_id from quiz qz
			INNER JOIN questions que on qz.category_id = que.category_id 
			WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL)";

			*/

		$sql = "INSERT INTO quizQuestions (quiz_id, question_id)
			SELECT qz.id as quiz_id, que.id as question_id from quiz qz
			INNER JOIN questions que on qz.category_id = que.category_id 
			WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL) 
			AND que.consumed <= qz.threshold  
			AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id)";

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
			WHERE qz.id = $quiz_id AND que.status = 1 
			AND que.consumed <= qz.threshold 
			AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL)";
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
				WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL) 
				AND que.consumed <= qz.threshold 
				AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id) 
    			AND que.id NOT IN (SELECT question_id from quizquestions where quiz_id = $quiz_id)";


			if($data = $this->DB->rawSql($sql)->returnData())
			{
				return $data;	
			}

			else {
				return false;
			}
	}


	public function listMatchQuestions($quiz_id)
	{
		$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			que.consumed as 'hits',  
			cat.name as category, sub.name as 'subject', que.section_id as 'subject_id',   
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer,

			(CASE 
            WHEN que.entity_id IS NULL AND que.quiz_id IS NULL 
             THEN 'public' 
             WHEN que.entity_id IS NULL AND que.quiz_id = qq.quiz_id   
             then 'private'
             ELSE 'local'
             end ) as 'scope' 

			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN categories sub on sub.id = que.section_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			WHERE qq.quiz_id = $quiz_id ORDER BY qq.status DESC, que.consumed DESC";

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
				que.consumed as 'hits', 
			cat.name as category, 
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer,

			(CASE 
            WHEN que.entity_id IS NULL AND que.quiz_id IS NULL 
             THEN 'public' 
             WHEN que.entity_id IS NULL AND que.quiz_id = qq.quiz_id   
             then 'private'
             ELSE 'local'
             end ) as 'scope' 


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


	public function appliedQuizSections($quiz_id)
	{


		$sql = "SELECT subject_id, quePerSection, points from subjects WHERE quiz_id = $quiz_id AND quePerSection > 0 AND points > 0";


		if($subjects = $this->DB->rawSql($sql)->returnData())
		{
			return $subjects;
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


	public function listQuizPlayQuestionsDistro($quiz_id)
	{

			$subjects = $this->appliedQuizSections($quiz_id);



			$questionsArray = [];


			$counter = 1;




			foreach ($subjects as $key => $subj) {



			$subject_id = $subj['subject_id'];
			$queFromSection = $subj['quePerSection'];



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
			WHERE qq.quiz_id = $quiz_id AND qq.status = 1 AND que.section_id = $subject_id  
			ORDER BY RAND() LIMIT $queFromSection";


			if($questions = $this->DB->rawSql($sql)->returnData())
			{
				foreach ($questions as $key => $item) {				
					array_push($questionsArray, $item);	
				}
			}


			
			}
		
			return $questionsArray;
			
	}



	public function quizAllocatedQuestionsSubjects($quiz_id)
	{


		$sql = "SELECT que.section_id as 'subject_id', sec.name as 'subjects' from quizquestions qq 
		inner join questions que on que.id = qq.question_id inner join categories cat on cat.id = que.category_id 
		inner join categories sec on sec.id = que.section_id 
		inner join subjects sub on sub.subject_id = que.section_id 
		where qq.quiz_id = $quiz_id AND sub.quiz_id = $quiz_id 
		AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id) GROUP BY sec.id";


		if($qqSubjects = $this->DB->rawSql($sql)->returnData())
		{
			return $qqSubjects;
		}

		return false;


	}


	public function statusToggle($dataPayload, $qqid)
	{


		if($this->DB->update($dataPayload, $qqid))
		{
			return true;
		}

		return false;
			

	}



	public function getQuestionMedia($question_id)
	{

		$sql = "SELECT m.type, qm.qmlabel as 'title', m.filepathurl from media m
		INNER JOIN quemedia qm on m.id = qm.media_id where qm.question_id = $question_id";
		return $this->DB->rawSql($sql)->returnData();

	}


	public function thresholdValidation($quizId)
	{
		$sql  = "SELECT count(qq.id) as 'expired',
			qz.threshold  
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
            INNER JOIN quiz qz on qz.id = qq.quiz_id 
			WHERE qq.status = 1 AND qq.quiz_id = $quizId  
            AND que.consumed > qz.threshold";

            if($row = $this->DB->rawSql($sql)->returnData())
            {
            	return $row[0];	
            }

	}

	public function globalThresholdCount($quizId)
	{
		$globalThreshold = GLOBAL_Threshold;

		$sql  = "SELECT count(qq.id) as 'expired'
			
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
            INNER JOIN quiz qz on qz.id = qq.quiz_id 
			WHERE qq.status = 1 AND qq.quiz_id = $quizId  
            AND que.consumed > $globalThreshold";

            if($row = $this->DB->rawSql($sql)->returnData())
            {
            	return $row[0]['expired'];
            }

            return 0;
	}

	public function globalThresholdByQuizId($quiz_id)
	{

		/*
		disable questions global status when crossed global threshold limit
		fire prerior to on question listings
		- before allocation
		- before listings
		- before synchronization check

		*/
		$globalThreshold = GLOBAL_Threshold;
		$sql = "UPDATE questions que INNER JOIN quiz qz on que.quiz_id = qz.id 
		SET que.status = 0 
		WHERE que.quiz_id = $quiz_id AND que.consumed > $globalThreshold";
		$this->DB->rawSql($sql);
		return $this->DB->connection->affected_rows;
	}
	
		


}


/*

public private
SELECT quiz_id, entity_id, IF(entity_id IS NULL AND quiz_id IS NULL, "public", "private") AS 'SCOPE' from questions;

*/