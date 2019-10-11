<?php 
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
		$sql ="SELECT que.id as 'Question_id', sd.name as 'subdiscipline', que.queDesc as 'QuestionDescription', que.answer as 'answer', 
				sa.answer as 'response', sa.isRight as 'isCorrect' from questions que 
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



	

}