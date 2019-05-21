<?php 
class subjectModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'subjects';		
	}


	public function saveQuizSubjects($quizid, $subdeciples)
	{

		$dataset['cols'] = array('quiz_id', 'subject_id');	

		$keyCounter = 0;

		foreach($subdeciples as $key => $value) {

			$dataset['vals'][$keyCounter] = array($quizid, $value);

			$keyCounter++;
		}	

		if($this->DB->multiInsert($dataset))
		{
			return true;
		}

		return false;

	}


	public function baseDistro($quiz_id)
	{


		$sql = "SELECT quiz_id, category_id, subject_id, category, subjects, MAX(questions) as 'questions', points, quePerSection  from 
		( 
		SELECT  qq.quiz_id, que.category_id, que.section_id as subject_id, 
			cat.name as 'category', sec.name as 'subjects', count(*) as 'questions', sub.points, sub.quePerSection  
			from quizquestions qq 
			inner join questions que on que.id = qq.question_id 
			inner join categories cat on cat.id = que.category_id 
			inner join categories sec on sec.id = que.section_id 
            inner join subjects sub on sub.subject_id = que.section_id 
			where qq.quiz_id = $quiz_id AND sub.quiz_id = $quiz_id AND qq.status = 1 
			AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id) 
            GROUP BY que.section_id, cat.name, sec.name, qq.quiz_id, que.category_id, sub.points, sub.quePerSection

			UNION 

			SELECT qz.id as 'quiz_id', qz.category_id, sub.subject_id, cat.name as 'category', sec.name as 'subjects', 0 as questions,
			sub.points as 'points', sub.quePerSection as 'quePerSection'  
			from subjects sub 
			INNER JOIN quiz qz on qz.id = sub.quiz_id 
			INNER JOIN categories cat on cat.id = qz.category_id 
			INNER JOIN categories sec on sec.id = sub.subject_id 
			WHERE qz.id = $quiz_id 

		) results GROUP BY quiz_id, category_id, subject_id, category, subjects, points, quePerSection";

		 if($data = $this->DB->rawSql($sql)->returnData())
		 {
		 	return $data;
		 }

		 else {
		 	return false;
		 }

	}



	public function updateDistro($quiz_id, $dataPayload)
	{


		$affetctedRows = 0;

		foreach ($dataPayload as $key => $value) {

			$quePerSection = $value['quePerSection'];
			$points = $value['points'];
			$subject_id = $value['subject_id']; 
			$quiz_id = $quiz_id;
			$sql = "UPDATE subjects SET quePerSection = $quePerSection, points = $points WHERE quiz_id = $quiz_id AND subject_id = $subject_id";
			if($this->DB->rawSql($sql))
			{
				$affetctedRows += 1;
	
			}

		}


		if(sizeof($dataPayload) == $affetctedRows)
		{
			return true;
		}

		else {
			return false;
		}

	}



	public function canToggleQuizQuestions($quiz_id, $subject_id)
	{


		$sql = "SELECT que.section_id as 'subject_id', sec.name as 'subjects', count(sec.id) as 'allocated',
			sub.quePerSection as 'quePerSection', IF(sub.quePerSection < count(sec.id), 1, 0) AS 'enableStatusToggle' 
			from quizquestions qq 
			inner join questions que on que.id = qq.question_id 
			inner join categories sec on sec.id = que.section_id 
            inner join subjects sub on sub.subject_id = que.section_id 
			where qq.quiz_id = $quiz_id AND sub.quiz_id = $quiz_id AND qq.status = 1 
			AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id) AND que.section_id = $subject_id  GROUP BY sec.id";



			if($subjectsToggleRow = $this->DB->rawSql($sql)->returnData())
			{
				return $subjectsToggleRow[0];
			}
			else {

				return false;

			}

	}




	public function questionsCountSummaryOnWizard($threshold, $subjectIds, $entityId = null)
	{


			$subjectIds = "'" . implode("','", $subjectIds) . "'";

			$sql = "SELECT subject_id, subject, MAX(questions) as 'questions', 0 as 'quePerSection', 0 as 'points' FROM ( 
                
            SELECT que.section_id as 'subject_id', sub.name as 'subject', count(que.id) as 'questions', 0 as 'quePerSection', 0 as 'points' from questions que 
			INNER JOIN categories sub on sub.id = que.section_id 
			WHERE status = 1 AND que.quiz_id IS NULL AND (que.entity_id IS NULL OR que.entity_id = $entityId) 
			AND que.consumed < $threshold  
            AND que.section_id IN ({$subjectIds})  
			GROUP BY que.section_id
                
            UNION
			
            SELECT cat.id as 'subject_id', cat.name as 'subject', 0 as 'questions', 0 as 'quePerSection', 0 as 'points' from categories cat
            WHERE cat.id IN ($subjectIds)    
			
            ) results GROUP BY subject_id, subject";


		if($data =  $this->DB->rawSql($sql)->returnData())
		{
			return $data;	
		}

		return false;


	}


}



