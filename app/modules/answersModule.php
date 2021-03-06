<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class answersModule extends appCtrl {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'stdanswers';		
	}



	public function patchBulkAnswers($payload)
	{
		if($this->DB->multiInsert($payload))
		{
			return true;
		}

		return false;
	}



	public function markAnswers($attempt_id)
	{

		/*
		refactoring previously using 2 queries to mark correct and incorrect answers
		mark 0 for incorrect 
		mark 1 for correct
		*/


		$sql = "UPDATE stdanswers stda 
		INNER JOIN questions que on que.id = stda.question_id  
		SET stda.isRight = IF(que.answer = stda.answer, '1', '0') 
		WHERE stda.attempt_id = $attempt_id";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

		return false;

	}



	public function markRightAnswers($attempt_id)
	{
		
		$sql = "UPDATE stdanswers stda INNER JOIN questions que on que.id = stda.question_id 
		SET stda.isRight = 1 
		WHERE stda.answer = que.answer AND stda.attempt_id = $attempt_id";



		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

			return false;

	}


	public function markIncorrectAnswers($attempt_id)
	{


		$sql = "UPDATE stdanswers stda INNER JOIN questions que on que.id = stda.question_id 
		SET stda.isRight = 0  
		WHERE stda.answer <> que.answer AND stda.attempt_id = $attempt_id";


		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

			return false;

	}


	public function setBasicScore($attempt_id, $score)
	{
		
		$sql = "UPDATE stdattempts sta SET sta.score = $score, sta.attempted_at = NOW() WHERE sta.id = $attempt_id";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

			return false;

	}



	public function saveCalculatedSubjectsScore($attempt_id)
	{

    			$sql = "INSERT INTO scoresheet (attempt_id, quiz_id, enroll_id, subject_id, maxScore, score, rightAnswers, quePerSection) 

    			SELECT attempt_id, quiz_id, enroll_id, subject_id, max(maxScore) as maxScore, actualScore, rightAnswers, quePerSection FROM (

						SELECT st.id AS 'attempt_id', en.quiz_id, st.enroll_id, 
  						que.section_id as 'subject_id', sub.points as 'maxScore', 
						((sub.points / sub.quePerSection ) * COUNT(sa.isRight) ) as 'actualScore', 
						COUNT(sa.isRight) as 'rightAnswers', sub.quePerSection as  'quePerSection' 

						from stdattempts st 

						INNER JOIN enrollment en on en.id = st.enroll_id 
						INNER JOIN stdanswers sa on sa.attempt_id = st.id 
						INNER JOIN questions que on que.id = sa.question_id 
						INNER JOIN subjects sub on sub.subject_id = que.section_id AND en.quiz_id = sub.quiz_id 
						where st.id = $attempt_id AND sa.isRight = 1 GROUP BY st.id, en.quiz_id, sub.subject_id, que.section_id, sub.points, sub.quePerSection 
                        
                        UNION 
                        
                        SELECT st.id AS 'attempt_id', en.quiz_id, st.enroll_id, 
						que.section_id as 'subject_id', sub.points as 'maxScore', 0 as 'actualScore', SUM(sa.isRight) as 'rightAnswers', sub.quePerSection    

						from stdattempts st 

						INNER JOIN enrollment en on en.id = st.enroll_id 
						INNER JOIN stdanswers sa on sa.attempt_id = st.id 
						INNER JOIN questions que on que.id = sa.question_id 
						INNER JOIN subjects sub on sub.subject_id = que.section_id AND en.quiz_id = sub.quiz_id 
						where st.id = $attempt_id 
						GROUP BY 
                        st.id, en.quiz_id, st.enroll_id, que.section_id, sub.points, sub.quePerSection 
                        HAVING SUM(sa.isRight) = 0 
                        
                        ) converge 
                        
                        GROUP BY converge.attempt_id, converge.quiz_id, converge.enroll_id, converge.subject_id, converge.actualScore, converge.quePerSection,
                            converge.rightAnswers";		


				if($this->DB->rawSql($sql)) {

					return $this->DB->connection->affected_rows;

				}

				return false;

	}



	public function entireSectionFailedExists($attempt_id)
	{
		$sql = "";
	}


	public function getCalcuatedScoreSum($attempt_id)
	{


		$sql = "SELECT SUM(score) AS 'score' from scoresheet where attempt_id = $attempt_id";

		if($score = $this->DB->rawSql($sql)->returnData())
		{
			return $score[0]['score'];
		}
		else {
			return 0;
		}

	}



	public function inspectAnswerByAttemptId($attempt_id)
	{
		$sql ="SELECT sa.id as answerId, que.id as 'Question_id', sd.name as 'subdiscipline', que.queDesc as 'QuestionDescription', que.answer as 'answer', 
				sa.answer as 'response', que.type_id, sa.isRight as 'isCorrect', sa.markedStatus from questions que 
				INNER JOIN categories sd on sd.id = que.section_id 
				INNER JOIN stdanswers sa on sa.question_id = que.id 
				WHERE sa.attempt_id = $attempt_id ORDER BY que.section_id";

				if($answers = $this->DB->rawSql($sql)->returnData())
				{
					return $answers;
				}

				return false;

	}


	public function scoreCardBreakDown($quiz_id, $attempt_id)
	{


		$sql = "SELECT cat.name as 'subjects', sc.maxScore as 'maxScore', 
			sc.score as 'actualScore',  (sc.score / sc.maxScore) * 100 as 'per', sc.quePerSection as 'quePerSection', sc.rightAnswers as 'correctAnswers'  
			from scoresheet sc 
			INNER JOIN categories cat on cat.id = sc.subject_id 
			WHERE sc.attempt_id = $attempt_id";


		if($scoreCard = $this->DB->rawSql($sql)->returnData())
		{
			return $scoreCard;	
		}

		return false;



	}



	public function quizAttemptQuestionSubjects($attempt_id)
	{
		$sql = "SELECT que.section_id AS 'subject_id', sd.name as 'subjects' from questions que INNER JOIN categories sd on sd.id = que.section_id INNER JOIN stdanswers sa on sa.question_id = que.id WHERE sa.attempt_id = $attempt_id GROUP BY que.section_id";



		if($subjects = $this->DB->rawSql($sql)->returnData())
		{
			return $subjects;
		}

		else {
			return false;
		}
	}



	public function udpateQuestionCounter($attempt_id)
	{

		$sql = "UPDATE questions que 
			INNER JOIN stdanswers sa on sa.question_id = que.id 
			SET que.consumed = que.consumed + 1 WHERE sa.attempt_id = $attempt_id AND sa.answer <> 'u/a'";

		if($this->DB->rawSql($sql))
		{

			$this->globalThresholdByAttemptID($attempt_id);
			return $this->DB->connection->affected_rows;
		}

		return false;

	}


	public function globalThresholdByAttemptID($attempt_id)
	{

		/*
		disable questions global status when crossed global threshold limit
		fire when questions are submitted for answers 
		*/
		$globalThreshold = GLOBAL_Threshold;
		$sql = "UPDATE questions que INNER JOIN stdanswers sa on que.id = sa.question_id 
		SET que.status = 0 
		WHERE sa.attempt_id = $attempt_id AND que.consumed > $globalThreshold";
		$this->DB->rawSql($sql);
		return $this->DB->connection->affected_rows;
	}


	public function recoverAnswersFromActivity($attempt_id)
	{

		$sql = "INSERT IGNORE INTO stdanswers (attempt_id, question_id, answer)
		SELECT $attempt_id as attempt_id, question_id, answer from activity where attempt_id = $attempt_id";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;		
		}

		return false;


	}



	public function singleProgressByAtemptId($attempt_id)
	{


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
		sta.id = $attempt_id ORDER BY sta.attempted_at DESC";



		if($progress = $this->DB->rawSql($sql)->returnData())
		{
				return $progress;
		}

		else {
				return false;
		}				


	}




	public function buildDLSSummary($attempt_id)
	{

			$sql = "SELECT sub.name, lvl.levelEN, SUM(stx.isRight) as correct, COUNT(stx.id) as queFromLvel, que.level_id, que.section_id, (case when lvl.id = 1 then 20 when lvl.id = 2 then 30 when lvl.id = 3 then 50 END) as dlsDistro, qz.maxScore, qsub.points as pointsPerSection,
				round( SUM(stx.isRight) * 100 / (COUNT(lvl.id) )  ) as ability,
				( (qsub.points * (case when lvl.id = 1 then 20 when lvl.id = 2 then 30 when lvl.id = 3 then 50 END) / 100) * (  SUM(stx.isRight) / (COUNT(lvl.id) * 100 ) / 100 ) ) as obtained 
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

				if($data =  $this->DB->rawSql($sql)->returnData() )
				{
					return $data;
				}

				return false;

	}



	public function updateMarkedAnswers($attempt_id)
	{


		$sql = "UPDATE stdanswers stda 
		INNER JOIN markedtable mt on stda.id = mt.id AND stda.question_id = mt.question_id   
		SET stda.markedStatus = mt.markedStatus   
		WHERE stda.attempt_id = $attempt_id";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

		return false;	


	}


	public function upgradeMarkedAnswers($attempt_id)
	{

		/*
		refactoring previously using 2 queries to mark correct and incorrect answers
		mark 0 for incorrect 
		mark 1 for correct
		*/


		$sql = "UPDATE stdanswers stda 
		INNER JOIN questions que on que.id = stda.question_id  
		SET stda.answer = que.answer, stda.isRight = 1  
		WHERE stda.attempt_id = $attempt_id AND stda.markedStatus = 'up'";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

		return false;

	}



	public function downgradeMarkedAnswers($attempt_id)
	{
		

		$sql = "UPDATE stdanswers stda 

		INNER JOIN questions que on que.id = stda.question_id  SET stda.answer = 
		(case 
        when stda.answer = 'a' then 'b' 
        when stda.answer = 'b' then 'c' 
        when stda.answer = 'c' then 'd' 
        when stda.answer = 'd' then 'b' 
        else stda.answer  
        END), stda.isRight = 0  WHERE stda.attempt_id = $attempt_id AND stda.markedStatus = 'down' AND stda.isRight = 1 AND (que.type_id = 1 OR que.type_id = 2)";

       	if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

		return false;

	}


	public function setTotalScore($attempt_id)
	{


		$sql = "UPDATE stdattempts as t1 LEFT JOIN (

		SELECT attempt_id, SUM(score) as totalObtained FROM scoresheet WHERE attempt_id = $attempt_id GROUP BY attempt_id


		) AS d ON d.attempt_id = t1.id 

    
	    SET t1.score = d.totalObtained 
    
    	WHERE t1.id = $attempt_id";


    	if($this->DB->rawSql($sql))
    	{

    		return $this->DB->connection->affected_rows;

    	}

    	return false;

	}

	/*

	UPDATE stdanswers SET answer='e'
	WHERE id IN (
    SELECT id FROM (
        SELECT id FROM stdanswers 
        ORDER BY id ASC  
        LIMIT 20
    	) tmp
	);


	*/




}

/*


	strength
---------------------
91 - 100 Phenominal
81 - 90 Expert
71 - 80 Solid
61 - 70 Above Average
51 - 60 Average
40 - 50 Below Average
31 - 40 Weak
0 - 30 Poor

	TESTING QUERY FOR THE DYNAMIC LEVEL QUIZ

	SCORE IS DISTRUBUTED AND ALLOCATED ON THE BASIS OF DIFFCULTY LEVELS

	EASY IS 20
	MEDIUM IS 30
	DIFFICULT IS 50

	get all attmept id for quiz
	SELECT std.id, en.id, std.score as enrollID from stdattempts std 
	INNER JOIN enrollment en on en.id = std.enroll_id
	INNER JOIN quiz qz on qz.id = en.quiz_id where qz.id = 150;



-----------------------------------------------------------------

DLS SCORING

SET @attempt_id := 546;
SET @quiz_id := 150;

SELECT sub.name, lvl.levelEN, sum(stx.isRight) as correct, count(stx.id) as queFromLvel, que.level_id, que.section_id, (case when lvl.id = 1 then 20 when lvl.id = 2 then 30 when lvl.id = 3 then 50 END) as dlsDistro, qz.maxScore, qsub.points as pointsPerSection,

round( SUM(stx.isRight) * 100 / (COUNT(lvl.id) )  ) as ability,

( (qsub.points * (case when lvl.id = 1 then 20 when lvl.id = 2 then 30 when lvl.id = 3 then 50 END) / 100) * (SUM(stx.isRight) * 100 / (COUNT(lvl.id) ) / 100 ) ) as obtained 

from stdanswers stx
INNER JOIN questions que on que.id = stx.question_id 
INNER JOIN categories sub on sub.id = que.section_id 
INNER JOIN level lvl on lvl.id = que.level_id 
INNER JOIN stdattempts sta on sta.id = stx.attempt_id 
INNER JOIN enrollment en on en.id = sta.enroll_id 
INNER JOIN quiz qz on qz.id = en.quiz_id 
INNER JOIN subjects qsub on qsub.quiz_id = qz.id 

WHERE stx.attempt_id = @attempt_id 

GROUP BY lvl.id, que.section_id;

	*/
