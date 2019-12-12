<?php 
class attemptModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'stdattempts';		
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



}


