<?php class quizModule extends appCtrl {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'quiz';

	}


	public function listQuiz()
	{

		if($quiz = $this->DB->listall()->returnData())
		{
			return $quiz;
		}
		else {
			return false;
		}

	}



	public function getQuizById($quiz_id)
	{

		$sql = "SELECT qz.id, qz.title, qz.category_id, cat.name as 'category', qz.maxScore, qz.minScore, qz.duration, qz.startDateTime, qz.endDateTime, qz.noques, qz.user_id from quiz qz
		INNER JOIN categories cat on cat.id = category_id
		WHERE qz.id = $quiz_id LIMIT 1;";

		if($quiz = $this->DB->rawSql($sql)->returnData())
			{
				return $quiz;	
			}

			else {
				return false;
			}

	}




	public function addQuiz($dataPayload)
	{

		if($lastId = $this->DB->insert($dataPayload))
		{
			return $lastId;
		}
		else {
			return $this->DB;
		}

	}



	public function getStudentPendingQuizList($studentId)
	{


		// pending quiz query

		$sql = "SELECT qz.id, en.id as 'enroll_id', qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration, qz.noQues, 
		en.student_id, en.dateEnrolled, en.attempts, en.retake,
			IF(qz.endDateTime > NOW(), 'valid', 'expired') as 'validity' 
		from enrollment en 
		INNER JOIN quiz qz on qz.id = en.quiz_id 
		WHERE en.id NOT IN (SELECT DISTINCT(enroll_id) from stdattempts) AND en.student_id = $studentId";

				/*
				attempted quiz list query
				
				SELECT sta.id as 'attemptId', qz.id as 'quizId', en.id as 'enroll_id', qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration,
					qz.noques, qz.user_id, en.attempts, en.retake,  sta.attempted_at, sta.score, 
					IF(qz.endDateTime > NOW(), 'valid', 'expired') as 'validity' 
				FROM quiz qz 
				INNER JOIN enrollment en on en.quiz_id = qz.id 
				LEFT JOIN stdattempts sta on sta.enroll_id = en.id 
				WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id);

				*/

			if($quiz = $this->DB->rawSql($sql)->returnData())
			{
				return $quiz;	
			}

			else {
				return false;
			}

	}



	public function getStudentAttemptedQuizList($student_id)
	{
		$sql = "SELECT qz.id as 'id', sta.id as 'attemptId', en.id as 'enroll_id', qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration,
		qz.noques, qz.user_id, en.attempts, en.retake,  sta.attempted_at, sta.score, 
		IF(qz.endDateTime > NOW(), 'valid', 'expired') as 'validity' 
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		LEFT JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id) AND
		en.student_id = $student_id";


		if($quiz = $this->DB->rawSql($sql)->returnData())
			{
				return $quiz;	
			}

			else {
				return false;
			}


	}



	public function quizQuestionEligible($quiz_id)
	{
		$sql = "SELECT qz.noques as 'required', count(qq.id) as 'allocated', 
			IF(qz.noques <= count(qq.id), 'valid', 'invalid') as 'validity' 
			FROM quizquestions qq
			INNER JOIN quiz qz on qz.id = qq.quiz_id 
			WHERE qq.quiz_id = $quiz_id";


			if($quiz = $this->DB->rawSql($sql)->returnData())
			{
				return $quiz;	
			}

			else {
				return false;
			}

	}



	public function udpate($dataPayload, $id)
	{
		if($this->DB->update($dataPayload, $id))
		{
			return true;
		}
		else {
			return false;
		}
	}

	public function quizEnrollmentEnabled($quiz_id)
	{

		$enroll = $this->DB->pluck('enrollment')->Where("id = '".$quiz_id."'");

		if($enroll == 1)
		{
			return true;
		}

		return false;

	}


	public function getQuizInfo($quiz_id)
	{
		
		$sql = "SELECT qz.id, qz.title, qz.category_id, cat.name, qz.maxScore, 
		qz.duration, qz.startDateTime, qz.endDateTime, qz.noques from quiz qz
		INNER JOIN categories cat on cat.id = qz.category_id where qz.id = $quiz_id";

		if($quiz = $this->DB->rawSql($sql)->returnData())
			{
				return $quiz;	
			}

			else {
				return false;
			}

	}



	public function quizProgress($quiz_id)
	{


		$sql = "SELECT qz.id as 'quizId', sta.id as 'attemptId', std.id as 'student_id', en.id as 'enroll_id',  std.name, std.email,  qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration, 
		qz.noques, qz.user_id, en.attempts, en.retake,  DATE_FORMAT(sta.attempted_at,'%d-%m-%y %h:%i %p') as attempted_at, TRUNCATE(sta.score, 2) as 'score',

		TRUNCATE(((sta.score * 100) / qz.maxScore), 2) as 'per' 
		
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		INNER JOIN users std on std.id = en.student_id 

		LEFT JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id) AND
		qz.id = $quiz_id";



		if($progress = $this->DB->rawSql($sql)->returnData())
			{
				return $progress;
			}

			else {
				return false;
			}	




	}




/*



teacher inspect quiz performance


		SELECT qz.id as 'id', sta.id as 'attemptId', en.id as 'enroll_id', qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration,
		qz.noques, qz.user_id, en.attempts, en.retake,  sta.attempted_at, TRUNCATE(sta.score, 2) as 'score'
		
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		LEFT JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id) AND
		qz.id = 46;


*/





}