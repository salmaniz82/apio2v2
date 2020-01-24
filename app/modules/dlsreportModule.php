<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');



class dlsreportModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'dlsreport';		
	}



	public function saveDlsReport($attempt_id)
	{

		$sql = "INSERT INTO dlsreport (attempt_id, section_id, level_id, queFromLevel, correct, dlsDistro, ability, obtained) 
		SELECT $attempt_id as attempt_id, que.section_id, que.level_id, count(stx.id) as queFromLvel, sum(stx.isRight) as correct, 
		(case when lvl.id = 1 then 20 when lvl.id = 2 then 30 when lvl.id = 3 then 50 END) as dlsDistro,

		round( SUM(stx.isRight) * 100 / (COUNT(lvl.id) ), 2) as ability,

		(  (SUM(stx.isRight) / COUNT(lvl.id)) * qsub.points) * (case when lvl.id = 1 then 20 when lvl.id = 2 then 30 when lvl.id = 3 then 50 END) / 100   as obtained 

		from stdanswers stx
		INNER JOIN questions que on que.id = stx.question_id 
		INNER JOIN categories sub on sub.id = que.section_id 
		INNER JOIN level lvl on lvl.id = que.level_id 
		INNER JOIN stdattempts sta on sta.id = stx.attempt_id 
		INNER JOIN enrollment en on en.id = sta.enroll_id 
		INNER JOIN quiz qz on qz.id = en.quiz_id 
		INNER JOIN subjects qsub on qsub.quiz_id = qz.id 
		WHERE stx.attempt_id = $attempt_id 
		GROUP BY lvl.id, que.section_id, qsub.points";

		if($this->DB->rawSql($sql))
		{

			return $this->DB->connection->affected_rows;

		}

		return false;

	}



	public function updateScoresheetDlsMatrix($attempt_id)
	{


		$sql = "UPDATE scoresheet as t1 LEFT JOIN (

		SELECT attempt_id, section_id, SUM(obtained) as obtained from dlsreport where attempt_id = $attempt_id 
		GROUP BY attempt_id, section_id 

		) AS d ON d.attempt_id = t1.attempt_id 

			AND t1.subject_id = d.section_id 
    
	    SET t1.score = d.obtained
    
    WHERE t1.attempt_id = $attempt_id";


    	if($this->DB->rawSql($sql))
    	{

    		return $this->DB->connection->affected_rows;

    	}

    	return false;

	}



	


}


/*

SELECT attempt_id, section_id, level_id, queFromLevel, SUM(obtained) as obtained from dlsreport where attempt_id = $attempt_id 
		GROUP BY section_id, level_id, queFromLevel

		) AS d ON d.attempt_id = t1.attempt_id 

			AND t1.subject_id = d.section_id 
    
	    SET t1.score = d.obtained
    
    WHERE t1.attempt_id = $attempt_id


*/