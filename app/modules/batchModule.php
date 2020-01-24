<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class batchModule extends appCtrl {


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'batches';
	}



	public function listAllBatches($user_id)
	{
		
		$sql = "SELECT id, title, maxScore, passingScore, user_id from batches WHERE user_id = $user_id";

		if($rows = $this->DB->rawSql($sql)->returnData())
		{
			return $rows;
		}

		return false;

	}


	public function duplicateCheck($batch_id, $candidate_id)
	{

		$this->DB->table = 'tagging';

		if($this->DB->build('S')->Colums()->Where("batch_id = '".$batch_id."'")->Where("candidate_id = '".$candidate_id."'")->go()->returnData())
		{
			return true;
		}
		return false;
	}


	public function getCandiateParticipation($batchId, $user_id)
	{

		

		$sql = "SELECT tg.candidate_id, u.name, u.email, (SELECT COUNT(*) from batchquiz where batch_id = tg.batch_id) 'noQuiz',
		(
			select count(sta.id) from batchquiz bq 
			INNER JOIN enrollment en on en.quiz_id = bq.quiz_id  
			INNER JOIN stdattempts sta on sta.enroll_id = en.id 
			WHERE bq.batch_id = tg.batch_id AND en.student_id = tg.candidate_id 
			AND sta.id IN (SELECT max(id) as id from stdattempts WHERE is_active = 0 group by enroll_id)
			GROUP BY en.student_id
		) as totalAttemmpted,

		(select sum(sta.score) from batchquiz bq 
		INNER JOIN enrollment en on en.quiz_id = bq.quiz_id  
		INNER JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE bq.batch_id = tg.batch_id AND en.student_id = tg.candidate_id 
		AND sta.id IN (SELECT max(id) as id from stdattempts WHERE is_active = 0 group by enroll_id)
		GROUP BY en.student_id
		) as score 

		from tagging tg
		INNER JOIN users u on u.id = tg.candidate_id 
		INNER JOIN batches bt on bt.id = tg.batch_id 
		WHERE tg.batch_id = $batchId 
		AND bt.user_id = $user_id";

		$row = $this->DB->rawSql($sql)->returnData();

		if($row)
		{
			return $row;
		}

		return false;

	}


	public function candParticipation($batchId, $user_id)
	{


		$data = $this->getCandiateParticipation($batchId, $user_id);

		if(!$data)
		{
			return false;
		}

		for($i = 0; $i<sizeof($data); $i++)
		{
			
			if($data[$i]['totalAttemmpted'] == null)
			{
				$data[$i]['totalAttemmpted'] = 0;	
			}

			$data[$i]['complition'] = round($data[$i]['totalAttemmpted'] / $data[$i]['noQuiz'] * 100);
		}

		return $data;

	}


	public function listBatchEligibleQuiz($user_id)
	{

		$sql = "SELECT qz.id, qz.title, qz.maxScore, qz.minScore from quiz qz
		WHERE qz.status = 1 AND qz.enrollment = 1 AND qz.user_id = $user_id  
		AND qz.endDateTime > NOW()";	

		$rows = $this->DB->rawSql($sql)->returnData();

		if($rows){
			return $rows;
		}
		
		return false;

	}



	public function saveBatchMaster($dataPayload)
	{

		if(!$last_id = $this->DB->insert($dataPayload))
		{
			return false;
		}

		return $last_id;
	}




	public function saveBatchQuiz($batchID, $quizIds)
	{

		$this->DB->table = 'batchquiz';
		$dataset['cols'] = array('batch_id', 'quiz_id');	

		$keyCounter = 0;

		foreach($quizIds as $key => $value) {

			$dataset['vals'][$keyCounter] = array($batchID, $quizIds[$keyCounter]);

			$keyCounter++;

		}	

		if($this->DB->multiInsert($dataset))
		{
			return true;
		}

		return false;

	}


	public function getBatchById($id, $user_id)
	{


		$sql = "SELECT id, title, maxScore, passingScore, user_id from batches WHERE id = $id AND user_id = $user_id";

		if($rows = $this->DB->rawSql($sql)->returnData())
		{
			return $rows;
		}

		return false;

	}

	public function candiateBatchQuizPerformance($batch_id, $candiate_id)
	{

		/*
		$sql = "SELECT qz.id as 'quizId', sta.id as 'attemptId', std.id as 'student_id', en.id as 'enroll_id',  std.name, std.email,  qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration, 
		qz.noques, qz.user_id, en.attempts, en.retake,  sta.attempted_at as attempted_at, sta.score as 'score',

		((sta.score * 100) / qz.maxScore) as 'per',

		(SELECT gd.grade from grading gd WHERE ((sta.score * 100) / qz.maxScore) BETWEEN gd.spmin AND gd.spmax LIMIT 1 ) as grade,  
 
        (SELECT gd.gpa from grading gd WHERE ((sta.score * 100) / qz.maxScore) BETWEEN gd.spmin AND gd.spmax LIMIT 1) as gpa   
		
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		INNER JOIN users std on std.id = en.student_id 

		LEFT JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id) AND
		qz.id IN (SELECT quiz_id from batchquiz where batch_id = $batch_id) AND std.id = $candiate_id ORDER BY sta.attempted_at DESC";

		*/


		$sql = "SELECT quizId, attemptId, enroll_id, student_id, name, email, title, category_id, maxScore, minScore, duration, 
		noques, user_id, attempts, retake, attempted_at, score, per, grade,  gpa 
		from (
    
    	SELECT qz.id as 'quizId', sta.id as 'attemptId', std.id as 'student_id', en.id as 'enroll_id',  std.name, std.email,  
    	qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration, qz.noques, qz.user_id, en.attempts, en.retake,  
    	sta.attempted_at as attempted_at, sta.score as 'score',

		((sta.score * 100) / qz.maxScore) as 'per',

		(SELECT gd.grade from grading gd WHERE ((sta.score * 100) / qz.maxScore) BETWEEN gd.spmin AND gd.spmax LIMIT 1 ) as grade,  
 
        (SELECT gd.gpa from grading gd WHERE ((sta.score * 100) / qz.maxScore) BETWEEN gd.spmin AND gd.spmax LIMIT 1) as gpa   
		
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		INNER JOIN users std on std.id = en.student_id 

		LEFT JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id) AND
		qz.id IN (SELECT quiz_id from batchquiz where batch_id = $batch_id) AND std.id = $candiate_id  
    
    UNION 
    
    SELECT en.quiz_id as quizId, 0 as attemptId, en.id as enroll_id, en.student_id as student_id,

	u.name as name, u.email as email, qz.title as title, qz.category_id as category_id, qz.maxScore as maxScore, qz.minScore as minScore, qz.duration as duration,
	qz.noques as noques, qz.user_id as user_id, 0 as attempts, 0 as retake, 0 as attempted_at, 0 as score, 0 as per, 0 as grade, 0 as gpa 

	from enrollment en

	INNER JOIN users u on u.id = en.student_id 
	INNER JOIN quiz qz on qz.id = en.quiz_id 

	WHERE en.student_id = $candiate_id  

	AND en.id NOT IN (SELECT enroll_id from stdattempts where is_active = 0 AND enroll_id = en.id)
	AND qz.id IN (SELECT quiz_id from batchquiz where batch_id = $batch_id)
    
    
    ) converge";


		if($progress = $this->DB->rawSql($sql)->returnData())
		{
			return $progress;
		}

		else {
			return false;
		}	



	}




	public function batchPreCheck($batch_id, $canidate_id)
	{


	/*
	GET THE COUNT OF QUIZ BY BATCH_ID
	SELECT count(*) from batchquiz where batch_id = 1;

	GET COUNT OF ACTIVE AND ENROLLMENT ENABLED QUIZ
	SELECT count(*) from quiz where id IN (select quiz_id from batchquiz where batch_id = 1) and enrollment = 1 and status = 1;

	GET COUNT WHERE DATES ARE VALID AND NOT EXPIRED
	SELECT count(*) from quiz where id IN (select quiz_id from batchquiz where batch_id = 1) and endDateTime > NOW();

	ALL THREE EQUALLY MATHCED BEFORE IT CAN ENROLL ANY CANIDATES IN 
	*/

	}


	public function batchQuizCount($batch_id)
	{

		$sql = "SELECT count(*) as 'quizCount' from batchquiz where batch_id = $batch_id";

		if($count = $this->DB->rawSql($sql)->returnData())
		{
			return $count[0]['quizCount'];	
		}

		return false;

	}


	public function batchActiveQuizCount($batch_id)
	{

		$sql = "SELECT count(*) as 'activeQuizCount' from quiz where id IN (select quiz_id from batchquiz where batch_id = $batch_id) and enrollment = 1 and status = 1";

		if($count = $this->DB->rawSql($sql)->returnData())
		{
			return $count[0]['activeQuizCount'];
		}

		return false;

	}


	public function batchValidDatesQuizCount($batch_id)
	{

		$sql = "SELECT count(*) as 'validDateQuizCount' from quiz where id IN (select quiz_id from batchquiz where batch_id = $batch_id) and endDateTime > NOW()";

		if($count = $this->DB->rawSql($sql)->returnData())
		{
			return $count[0]['validDateQuizCount'];
		}

		return false;

	}


	public function patchEnroll($batch_id, $candidate_id)
	{


		$sql  = "INSERT IGNORE INTO enrollment (student_id, quiz_id) 
		SELECT $candidate_id as student_id, quiz_id from batchquiz where batch_id = $batch_id";


	 	if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

		return false;


	}


	public function batchTagging($batch_id, $candidate_id)
	{


		$this->DB->table = 'tagging';

		$dataPayload = array(
			'batch_id' => $batch_id,
			'candidate_id' => $candidate_id,
			'isActive'=> 1

		);


		if($last_id = $this->DB->insert($dataPayload))
		{
			return $last_id;	
		}
		return false;
	
	}


	public function listTaggedStudents($batch_id)
	{

		$sql = "SELECT u.id, u.name, u.email FROM users u 
		INNER JOIN tagging tg on tg.candidate_id = u.id
		WHERE tg.batch_id = $batch_id";


		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}

		return false;

	}


	public function getSingleTaggedEnrolledCanidate($candidate_id, $batch_id)
	{

		$sql = "SELECT u.id, u.name, u.email FROM users u 
		INNER JOIN tagging tg on tg.candidate_id = u.id
		WHERE tg.batch_id = $batch_id AND tg.candidate_id = $candidate_id LIMIT 1";


		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}

		return false;

	}


	public function batchDetails($batch_id)
	{


		

		$sql = "SELECT qz.id, qz.title, qz.maxScore, qz.minScore, 
			qz.startDateTime, qz.endDateTime from quiz qz
			INNER JOIN batchquiz btq on btq.quiz_id = qz.id
			WHERE btq.batch_id = $batch_id";

		if($row = $this->DB->rawSql($sql)->returnData())
		{
			return $row;
		}

		return false;

	}


}