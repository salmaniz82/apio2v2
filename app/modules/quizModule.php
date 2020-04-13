<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');



class quizModule extends appCtrl {


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


	public function fetchQuizList($user_id, $role_id)
	{


		if($role_id == 1)
		{
			$sql = "SELECT * from quiz ORDER BY id desc";
		}
		else {

			$sql = "SELECT * from quiz where user_id = $user_id ORDER BY id desc";
		}


		if($data = $this->DB->rawSql($sql)->returnData())
		{

			return $data;
		}
		else {
			return false;
		}

	}



	public function getQuizById($quiz_id)
	{

		$sql = "SELECT qz.id, qz.code, qz.title, qz.category_id, cat.name as 'category', 
		qz.maxScore, qz.minScore, qz.duration, qz.startDateTime, qz.endDateTime, qz.noques, qz.user_id, qz.status as 'status',
		qz.enrollment as 'enrollment', qz.dls, qz.uniqueOnRetake, qz.showScore, qz.showResult, qz.showGrading, qz.showGPA, qz.venue,

		DATE_FORMAT(qz.endDateTime, '%d %b %Y') as formatedEndDate, 
		DATE_FORMAT(qz.endDateTime, '%h:%m %p') as formatedEndTime,



		(SELECT count(id) as enrolledStudent from enrollment WHERE quiz_id = $quiz_id GROUP by quiz_id ) as enrolledStudent 


		 from quiz qz
		INNER JOIN categories cat on cat.id = category_id
		WHERE qz.id = $quiz_id LIMIT 1";

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
		

		return false;

	}



	public function getStudentPendingQuizList($studentId)
	{


		// pending quiz query

		$sql = "SELECT qz.id, en.id as 'enroll_id', qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration, qz.noQues, 
		qz.showScore, qz.showResult, qz.showGrading, qz.showGPA, 
		en.student_id, en.dateEnrolled, en.dtsScheduled,  en.attempts, en.retake,
			IF(qz.endDateTime > NOW(), 'valid', 'expired') as 'validity',  IF(en.dtsScheduled > NOW(), 'countdown', 'eligible') as 'schedule' 
		from enrollment en 
		INNER JOIN quiz qz on qz.id = en.quiz_id 
		WHERE en.id NOT IN (SELECT DISTINCT(enroll_id) from stdattempts) AND en.student_id = $studentId";

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
		qz.noques, qz.user_id, qz.showScore, qz.showResult, qz.showGrading, qz.showGPA, 


		en.attempts, en.retake,  sta.attempted_at, 
		IF(qz.endDateTime > NOW(), 'valid', 'expired') as 'validity' 
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		LEFT JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id) AND
		en.student_id = $student_id ORDER BY sta.attempted_at DESC";


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



	public function update($dataPayload, $id)
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

	public function alphaIDBinaryCheck($alphaID)
	{

		$sql = "SELECT alphaID from quiz WHERE BINARY alphaID = '{$alphaID}' LIMIT 1";


		if($data = $this->DB->rawSql($sql)->returnData($sql))
		{
			return $data[0]['alphaID'];
		}

		return false;

	}

	public function pluckQuizID($id)
	{
		$sql = "SELECT id from quiz WHERE id = {$id} LIMIT 1";

		if($this->DB->rawSql($sql)->returnData($sql))
		{
			return true;
		}

		return false;
	}

	public function pluckAlphaIDviaID($id){

		$sql = "SELECT alphaID from quiz WHERE id = '{$id}' LIMIT 1";


		if($data = $this->DB->rawSql($sql)->returnData($sql))
		{
			return $data[0]['alphaID'];
		}

		return false;

	}

	public function pluckIdfromAlphaID($alphaID)
	{

		$sql = "SELECT id from quiz WHERE BINARY alphaID = '{$alphaID}' LIMIT 1";


		if($data = $this->DB->rawSql($sql)->returnData($sql))
		{
			return $data[0]['id'];
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
		qz.noques, qz.user_id, en.attempts, en.retake,  sta.attempted_at as attempted_at, sta.score as 'score', sta.is_active, 

		SUBSTR(SEC_TO_TIME(sta.timeLeft), 4 ,5) as timeLeft, sta.endState,  

		((sta.score * 100) / qz.maxScore) as 'per',

		(SELECT gd.grade from grading gd WHERE  round( ((sta.score * 100) / qz.maxScore) ) BETWEEN gd.spmin AND gd.spmax LIMIT 1 ) as grade,  
 
        (SELECT gd.gpa from grading gd WHERE  round( ((sta.score * 100) / qz.maxScore) ) BETWEEN gd.spmin AND gd.spmax LIMIT 1) as gpa,

        (case when sta.score >= qz.minScore then true else false end) as resultStatus      
		
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		INNER JOIN users std on std.id = en.student_id 

		LEFT JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id) AND
		qz.id = $quiz_id ORDER BY sta.attempted_at DESC";



		if($progress = $this->DB->rawSql($sql)->returnData())
			{
				return $progress;
			}

			else {
				return false;
			}	

	}



	public function quizDistroValidity($quiz_id)
	{

		$sql = "SELECT qz.maxScore as 'quizPoints', qz.noques as 'quizQues',  
		SUM(sb.quePerSection) as 'distQues', SUM(sb.points) as 'distPoints', 
		IF(qz.maxScore = SUM(sb.points) AND qz.noques = SUM(sb.quePerSection), 'valid', 'invalid') as 'distStatus'  
		from quiz qz 
		INNER JOIN subjects sb on sb.quiz_id = qz.id WHERE qz.id = $quiz_id";

		if($data = $this->DB->rawSql($sql)->returnData())
		{
			return $data[0];
		}

		return false;

	}


	public function validateEnrollmentRange($quizId, $enrollmentDateTime = null)
	{
	

		if($enrollmentDateTime == null)
		{
				$sql = "SELECT id, title,startDateTime, endDateTime from quiz where startDateTime <= NOW() AND endDateTime >= NOW() AND id = $quizId";
		}		
		else {
			$sql = "SELECT id, title, startDateTime, endDateTime from quiz where startDateTime <= NOW() AND endDateTime >= '{$enrollmentDateTime}' AND id = $quizId";		
		}


		return $this->DB->rawSql($sql)->returnData();
		

	}


	public function pluckThresholdByQuizId($quizID)
	{

		if($threshold = $this->DB->pluck('threshold')->Where("id = '".$quizID."'"))
		{
			return $threshold;
		}

		else {
			return false;
		}

	}


	public function pluckMaxAllocationSize($quiz_id)
	{

		$maxSize = $this->DB->pluck('maxAllocation')->Where("id = '".$quiz_id."'");

		if($maxSize)
		{
			return $maxSize;
		}

		return false;

	}


	public function deleteQuiz($quizId)
	{


		if($this->DB->delete($quizId, true))
		{
			return true;
		}

		return false;

	}


	public function optionToggleHandler($dataPayload, $id)
	{


		if($this->DB->update($dataPayload, $id))
		{
			return true;
		}
		else {
			return false;
		}


	}


	public function isDLSEnabledQuiz($quiz_id)
	{


		$dlsStatus = $this->DB->pluck('dls')->Where("id = '".$quiz_id."'");
		return $dlsStatus;

	}

	public function checkExists($quiz_id)
	{


		$dlsStatus = $this->DB->pluck('id')->Where("id = '".$quiz_id."'");
		return $dlsStatus;

	}


	public function getPendingInvitedQuiList($studentId, $enroll_id)
	{


		// pending quiz query

		$sql = "SELECT qz.id, en.id as 'enroll_id', qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration, qz.noQues, 
		qz.showScore, qz.showResult, qz.showGrading, qz.showGPA, 
		en.student_id, en.dateEnrolled, en.dtsScheduled,  en.attempts, en.retake,
			IF(qz.endDateTime > NOW(), 'valid', 'expired') as 'validity',  IF(en.dtsScheduled > NOW(), 'countdown', 'eligible') as 'schedule' 
		from enrollment en 
		INNER JOIN quiz qz on qz.id = en.quiz_id 
		WHERE en.student_id = $studentId AND en.id = $enroll_id AND en.id NOT IN (SELECT DISTINCT(enroll_id) from stdattempts)";

		if($quiz = $this->DB->rawSql($sql)->returnData())
			{
				return $quiz;	
			}

			else {
				return false;
			}

	}



	public function getAttemptedInvitedQuizList($student_id, $enroll_id)
	{
		$sql = "SELECT qz.id as 'id', sta.id as 'attemptId', en.id as 'enroll_id', qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration,
		qz.noques, qz.user_id, qz.showScore, qz.showResult, qz.showGrading, qz.showGPA, 


		en.attempts, en.retake,  sta.attempted_at, sta.score, 
		IF(qz.endDateTime > NOW(), 'valid', 'expired') as 'validity' 
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		INNER JOIN stdattempts sta on sta.enroll_id = en.id 
		WHERE sta.id IN (SELECT max(id) as id from stdattempts group by enroll_id) AND
		en.student_id = $student_id AND 
		en.id = $enroll_id ORDER BY sta.attempted_at DESC";

		
		if($quiz = $this->DB->rawSql($sql)->returnData())
			{
				return $quiz;	
			}

			else {
				return false;
			}

	}



	public function candidateSelfProgressReport($attempt_id, $student_id)
	{
		$sql = "SELECT qz.id as 'id', sta.id as 'attemptId', en.id as 'enroll_id', qz.title, qz.category_id, qz.maxScore, qz.minScore, qz.duration,
		qz.noques, qz.user_id, qz.showScore, qz.showResult, qz.showGrading, qz.showGPA, 
		en.attempts, en.retake,  sta.attempted_at, DATE_FORMAT(qz.endDateTime, '%d %b %Y %h:%m %p') as formatedAttemptDateTime, 
        IF(qz.showScore = 1, sta.score, 'N/A') as score,
        IF(qz.showScore = 1, ((sta.score * 100) / qz.maxScore), 'N/A') as per,
        IF(qz.showGrading = 1, (SELECT gd.grade from grading gd WHERE  round( ((sta.score * 100) / qz.maxScore) ) BETWEEN gd.spmin AND gd.spmax LIMIT 1 ), 'N/A') as grade,
        IF(qz.showGPA = 1, (SELECT gd.gpa from grading gd WHERE  round( ((sta.score * 100) / qz.maxScore) ) BETWEEN gd.spmin AND gd.spmax LIMIT 1), 'N/A') as gpa,
        IF(qz.showResult = 1, (case when sta.score >= qz.minScore then true else false end), 'N/A') as resultStatus,
        sta.is_active 
		FROM quiz qz 
		INNER JOIN enrollment en on en.quiz_id = qz.id 
		INNER JOIN stdattempts sta on sta.enroll_id = en.id 
        WHERE sta.id = $attempt_id AND en.student_id = $student_id ORDER BY sta.attempted_at DESC";

		if($quiz = $this->DB->rawSql($sql)->returnData())
			{
				return $quiz;	
			}

			else {
				return false;
			}

	}

		public function getQuizEntityLogo($quizId)
		{

			$sql = "SELECT qz.id, pro.logo from quiz qz 
			inner join profile pro on pro.user_id = qz.user_id 
			where qz.id = $quizId LIMIT 1";

			if($logo = $this->DB->rawSql($sql)->returnData())
			{
				return $logo;
			}

			return false;
	
		}



		public function isOwnedByUser($quizID, $ownerID)
		{

			$sql = "SELECT id from quiz where id = {$quizID} AND user_id = {$ownerID} limit 1";

			if($this->DB->rawSql($sql)->returnData())
			{
				return true;
			}

			return false;

		}


		public function quizTemplateClone($entity_id, $templateQuizIDs)
		{

			$sql = "
			INSERT INTO quiz (code, title, category_id, maxScore, minScore, 
			duration, startDateTime, endDateTime, noques, user_id, enrollment, 
			threshold, dls, dlsFrequency, uniqueOnRetake, showScore, showResult, 
			showGrading, showGPA, maxAllocation, venue, status)

			SELECT code, title, category_id, maxScore, minScore, 
			duration, NOW() AS startDateTime, DATE_ADD(NOW(), INTERVAL 1 YEAR) AS endDateTime, 
			noques, $entity_id as user_id, enrollment, threshold, dls, dlsFrequency, uniqueOnRetake, 
			showScore, showResult, showGrading, showGPA, maxAllocation, venue, 
			status from quiz WHERE id IN ( " . $templateQuizIDs .") ORDER BY id asc";


			if($this->DB->rawSql($sql))
			{
				return true;
			}

			return false;

		}


		public function extractClonedQuizIds($entity_id)
		{
			
			$sql = "SELECT id from quiz where user_id = $entity_id";

			if($ids = $this->DB->rawSql($sql)->returnData())
			{
				return $ids;
			}

			return false;

		}


		public function extractEntitySlugviaQuizID($quiz_id)
		{


			$sql = "SELECT qz.id, p.slug, u.id as entity_id from quiz qz 
					INNER JOIN users u on qz.user_id = u.id 
					INNER JOIN profile p on p.user_id = u.id 
					where qz.id = {$quiz_id}";


			if($data = $this->DB->rawSql($sql)->returnData())
			{
				return $data;
			}

			return false;


		}


		
		

}