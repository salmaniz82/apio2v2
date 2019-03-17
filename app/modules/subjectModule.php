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
		
		SELECT qq.quiz_id, que.category_id, que.section_id as 'subject_id', cat.name as 'category', sec.name as 'subjects', count(sec.id) as 'questions',
			sub.points as 'points', sub.quePerSection as 'quePerSection' 
			from quizquestions qq 
			inner join questions que on que.id = qq.question_id 
			inner join categories cat on cat.id = que.category_id 
			inner join categories sec on sec.id = que.section_id 
            inner join subjects sub on sub.subject_id = que.section_id 
			where qq.quiz_id = $quiz_id AND sub.quiz_id = $quiz_id AND qq.status = 1 
			AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id) GROUP BY sec.id
			UNION 
		SELECT qz.id as 'quiz_id', qz.category_id, sub.subject_id, cat.name as 'category', sec.name as 'subjects', 0 as questions,
			sub.points as 'points', sub.quePerSection as 'quePerSection'  
			from subjects sub 
			INNER JOIN quiz qz on qz.id = sub.quiz_id 
			INNER JOIN categories cat on cat.id = qz.category_id 
			INNER JOIN categories sec on sec.id = sub.subject_id 
			WHERE qz.id = $quiz_id 

		) results GROUP BY subject_id";

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
			$quiz_id = $value['quiz_id'];
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


}



