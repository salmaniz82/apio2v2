<?php 


if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');

class attemptModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'stdattempts';		
	}


	public function getAttemptDetails($attemptID)
	{


		$sql = "SELECT stda.id, stda.attempted_at, 

		(SELECT gd.grade from grading gd WHERE  round( ((stda.score * 100) / qz.maxScore) ) BETWEEN gd.spmin AND gd.spmax LIMIT 1 ) as grade,  
 
        (SELECT gd.gpa from grading gd WHERE  round( ((stda.score * 100) / qz.maxScore) ) BETWEEN gd.spmin AND gd.spmax LIMIT 1) as gpa,

        (case when stda.score >= qz.minScore then true else false end) as resultStatus,

		DATE_FORMAT(stda.attempted_at, '%d %b %Y') as formatedDate, 
		DATE_FORMAT(stda.attempted_at, '%h:%m %p') as formatedTime,

		qz.title, u.name, u.email from stdattempts stda
			INNER JOIN enrollment en on en.id = stda.enroll_id
			INNER join quiz qz on qz.id = en.quiz_id 
			INNER JOIN users u on u.id = en.student_id 
			WHERE stda.id = $attemptID limit 1";


			if($data = $this->DB->rawSql($sql)->returnData())
			{
				return $data;	
			}

			return false;


	}




	public function initiateQuiz($enroll_id)
	{

		$data['enroll_id'] = $enroll_id;
		
		if($attemptId = $this->DB->insert($data))
		{
			return $attemptId;
		}
		else {
			return false;
		}
	}



	public function attemptDurationValidity($attemptId)
	{
		
		/*
		$sql = "SELECT
	    sta.id,
	    qz.duration,
	    en.id AS 'enroll_id',
	    sta.attempted_at,
	    DATE_ADD(
	        sta.attempted_at,
	        INTERVAL qz.duration +5 MINUTE
	    ) AS 'validity',
	    NOW(), IF(
	        DATE_ADD(
	            sta.attempted_at,
	            INTERVAL qz.duration +5 MINUTE
	        ) > NOW(), 'VALID', 'INVALID') AS datetimevalidty
	    FROM
	        stdattempts sta
	    INNER JOIN enrollment en ON
	        en.id = sta.enroll_id
	    INNER JOIN quiz qz ON
	        qz.id = en.quiz_id 
	        WHERE sta.id = $attemptId";

	    */

	     $sql = "SELECT
			    sta.id, 
			     IF(
			        DATE_ADD(
			            sta.created_at,
			            INTERVAL qz.duration +5 MINUTE
			        ) > NOW(), 'VALID', 'INVALID') AS datetimevalidty
			    FROM
			        stdattempts sta
			    INNER JOIN enrollment en ON
			        en.id = sta.enroll_id
			    INNER JOIN quiz qz ON
			        qz.id = en.quiz_id
			    WHERE sta.id = $attemptId";

			  return $this->DB->rawSql($sql)->returnData();

	}



	public function incrementUsageXTimes($attemptId)
	{
	
		$sql = "UPDATE stdattempts SET usedxtimes = usedxtimes + 1 WHERE ID = $attemptId";
		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}
		return false;

	}


	public function getXTimesUsed($attemptId)
	{

		return $this->DB->pluck('usedxtimes')->Where("id = '".$attemptId."'");
		
	}

	public function toggleActive($attemptID, $status)
	{

		$data['is_active'] = $status;


		if($this->DB->update($data, $attemptID))
		{
			return true;
		}

		return false;

	}



	public function activeMonitoring($entity_id)
	{

		$sql = "SELECT
	    qz.id AS quizID,
	    qz.title, 
	    u.name, 
	    qz.duration,
	    qz.noQues, en.id AS enrollID,
	    sta.id AS attemptID,
	    sta.usedxtimes AS 'usedX',
	    sta.created_at, DATE_ADD(sta.created_at, INTERVAL + qz.duration MINUTE) AS 'ending_at',
	    TIMESTAMPDIFF(MINUTE,NOW(), DATE_ADD(sta.created_at, INTERVAL + qz.duration MINUTE)) as 'remainingTime',

	    COUNT(CASE WHEN act.atype = 'm' THEN act.atype END) AS marked,
  		COUNT(CASE WHEN act.atype = 'a' THEN act.atype END) AS answered,
  		COUNT(*) AS position 


		FROM
	    stdattempts sta 
	    INNER JOIN enrollment en on en.id = sta.enroll_id 
	    INNER JOIN users u on u.id = en.student_id 
	    INNER JOIN quiz qz on qz.id = en.quiz_id 
	    INNER JOIN activity act on act.attempt_id = sta.id 
	    WHERE NOW() < DATE_ADD(sta.created_at, INTERVAL qz.duration MINUTE) AND sta.is_active = 1 
		AND qz.user_id = $entity_id 
		GROUP BY qz.id, qz.title, u.name, qz.duration, qz.noQues, en.id, sta.id, sta.usedxtimes, sta.created_at";


	    if($rows = $this->DB->rawSql($sql)->returnData())
	    {
	    	return $rows;
	    }

	    	return false;

	}



	public function isQuizDLSbyAttempt_id($attempt_id)
	{

		$sql  = "SELECT qz.dls from stdattempts std 
		INNER JOIN enrollment en on en.id = std.enroll_id
		INNER JOIN quiz qz on qz.id = en.quiz_id where std.id = $attempt_id limit 1";

		if($data = $this->DB->rawSql($sql)->returnData())
		{
			if($data[0]['dls'] == 1)
			{
				return true;
			} 

			return false;

		}

		return false;
	}


	public function postUpdateMetaInformation($payload, $id)
	{

		if($this->DB->update($payload, $id))
		{
			return true;
		}

		return false;

	}


	public function clearAttemptsOnResetEnroll($enrollID)
	{

		$this->DB->delete(['enroll_id', $enrollID], false);
		return $this->DB->resource;

	}



	public function clearActivity($payload, $id)
	{

		if($this->DB->update($payload, $id))
		{
			return true;
		}

		return false;

	}


	public function interceptForAttempt($attemptId)
	{

		$sql = "SELECT en.id, en.intercept, en.direction, en.lastLimit, qz.minScore, qz.maxScore from enrollment en 
		INNER JOIN stdattempts stdx on stdx.enroll_id = en.id 
		INNER JOIN quiz qz on qz.id = en.quiz_id 
		WHERE stdx.id = $attemptId limit 1";


		if($intercept = $this->DB->rawSql($sql)->returnData())
		{
			return $intercept;
		}

		return false;


	}

	public function returnXamountOfRecentQuizResults($entity_id, $limit)
	{
		$sql = "SELECT sta.id as attemptID, en.id as enrollID, 

		qz.id, qz.code, qz.title, qz.maxScore, qz.minScore, 

		c.name as candidate, c.email as candidateEmail,

		sta.score as obtainedScore, ROUND((sta.score / qz.maxScore) * 100, 2) as percentageObtained,

		DATE_FORMAT(sta.attempted_at, '%d %b %Y') as fmDate, DATE_FORMAT(sta.attempted_at, '%h:%i %p') as fmTime,

		sta.attempted_at as rawDatetime, 

		(case when sta.score >= qz.minScore then true else false end) as resultStatus 

		from quiz qz 

		INNER JOIN enrollment en on en.quiz_id = qz.id 

		INNER JOIN users c on c.id = en.student_id 

		INNER JOIN stdattempts as sta on sta.enroll_id = en.id 

		where 
			qz.user_id = $entity_id AND 
			sta.score IS NOT NULL AND 
			sta.is_active = 0 AND 
			sta.id IN (
			SELECT max(stax.id) as id from stdattempts stax 
					INNER JOIN enrollment enx on enx.id = stax.enroll_id 
					INNER JOIN quiz qzx on qzx.id = enx.quiz_id 
					WHERE 
					qzx.user_id = $entity_id AND 
					stax.score IS NOT NULL AND 
					stax.is_active = 0 
					group by enroll_id)
			ORDER BY sta.id desc LIMIT $limit";


		if($recentFinished = $this->DB->rawSql($sql)->returnData())
		{
			return $recentFinished;
		}


		return false;


	}


	public function returnTopPerformer($entity_id, $limit)
	{
		$sql = "SELECT sta.id as attemptID, en.id as enrollID, 

		qz.id, qz.code, qz.title, qz.maxScore, qz.minScore, 

		c.name as candidate, c.email as candidateEmail,

		sta.score as obtainedScore, ROUND((sta.score / qz.maxScore) * 100, 2) as percentageObtained,

		DATE_FORMAT(sta.attempted_at, '%d %b %Y') as fmDate, DATE_FORMAT(sta.attempted_at, '%h:%i %p') as fmTime,

		sta.attempted_at as rawDatetime, 

		(case when sta.score >= qz.minScore then true else false end) as resultStatus 

		from quiz qz 

		INNER JOIN enrollment en on en.quiz_id = qz.id 

		INNER JOIN users c on c.id = en.student_id 

		INNER JOIN stdattempts as sta on sta.enroll_id = en.id 

		where 
			qz.user_id = $entity_id AND 
			sta.score IS NOT NULL AND 
			sta.is_active = 0 AND 
			sta.id IN (
			SELECT max(stax.id) as id from stdattempts stax 
					INNER JOIN enrollment enx on enx.id = stax.enroll_id 
					INNER JOIN quiz qzx on qzx.id = enx.quiz_id 
					WHERE 
					qzx.user_id = $entity_id AND 
					stax.score IS NOT NULL AND 
					stax.is_active = 0 
					group by enroll_id)

					HAVING resultStatus = 1 
			ORDER BY percentageObtained desc LIMIT $limit";
			


		if($topPerformant = $this->DB->rawSql($sql)->returnData())
		{
			return $topPerformant;
		}


		return false;


	}



}


