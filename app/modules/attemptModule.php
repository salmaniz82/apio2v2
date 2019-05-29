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

}