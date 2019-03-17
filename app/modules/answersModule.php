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


	public function prepareReturnDatasetArray($vehicle_id, $options)
	{

		$options = explode(',', $options);

		$dataset['cols'] = array('vehicle_id', 'options_id');

		for($i=0; $i<=sizeof($options)-1; $i++) { 

			$dataset['vals'][$i] = array(
				'vehicle_id'=> $vehicle_id,
				'options_id'=> (int)$options[$i]
			);

		}

		return $dataset;

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
		
		$sql = "UPDATE stdattempts sta SET sta.score = $score WHERE sta.id = $attempt_id";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

			return false;

	}



	public function saveCalculatedSubjectsScore($attempt_id)
	{


				
				$sql = "INSERT INTO scoresheet (attempt_id, quiz_id, enroll_id, subject_id, maxScore, score, rightAnswers)
				SELECT st.id AS 'attempt_id', en.quiz_id, st.enroll_id, 
						que.section_id as 'subject_id', sub.points as 'maxScore', ((sub.points / sub.quePerSection ) * COUNT(sa.isRight) ) as 'actualScore', COUNT(sa.isRight) as 'rightAnswers' 

						from stdattempts st 

						INNER JOIN enrollment en on en.id = st.enroll_id 
						INNER JOIN stdanswers sa on sa.attempt_id = st.id 
						INNER JOIN questions que on que.id = sa.question_id 
						INNER JOIN subjects sub on sub.subject_id = que.section_id AND en.quiz_id = sub.quiz_id 
						where st.id = $attempt_id AND sa.isRight = 1 GROUP BY sub.subject_id";



				if($this->DB->rawSql($sql))
						{
					return $this->DB->connection->affected_rows;
				}

				return false;

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


		/*

		$sql = "SELECT cat.name as 'subjects', sc.maxScore as 'maxScore', 
		sc.score as 'actualScore', (sc.score / sc.maxScore) * 100 as 'per'  
		from scoresheet sc 
		INNER JOIN categories cat on cat.id = sc.subject_id 
		WHERE sc.attempt_id = $attempt_id";

		*/

		$sql = "SELECT subjects, maxScore, MAX(actualScore) AS 'actualScore', MAX(per) as 'per' FROM 
		( 
        SELECT 	sd.name as 'subjects', 
        	subj.points as 'maxScore', 0 as 'actualScore', 0 as 'per' 
        	from questions que 
        	INNER JOIN subjects subj on (subj.subject_id = que.section_id AND subj.quiz_id = $quiz_id) 
			INNER JOIN categories sd on sd.id = que.section_id 
			INNER JOIN stdanswers sa on sa.question_id = que.id 
			WHERE sa.attempt_id = $attempt_id AND sa.isRight = 0 
        	GROUP BY que.section_id 
		UNION 
    	SELECT cat.name as 'subjects', sc.maxScore as 'maxScore', 
			sc.score as 'actualScore',  (sc.score / sc.maxScore) * 100 as 'per'  
			from scoresheet sc 
			INNER JOIN categories cat on cat.id = sc.subject_id 
			WHERE sc.attempt_id = $attempt_id 
		)result GROUP BY subjects";


		if($scoreCard = $this->DB->rawSql($sql)->returnData())
		{
			return $scoreCard;	
		}

		return false;



	}



	/*

	UNION FOR ALL FALSE ANSWER MATHING

	SELECT subjects, maxScore, actualScore, per FROM (
    SELECT cat.name as 'subjects', sc.maxScore as 'maxScore', 
		sc.score as 'actualScore',  (sc.score / sc.maxScore) * 100 as 'per'  
		from scoresheet sc 
		INNER JOIN categories cat on cat.id = sc.subject_id 
		WHERE sc.attempt_id = 191
    UNION
        SELECT 	sd.name as 'subjects', 
        subj.points as 'maxScore', 0 as 'actualScore', 0 as 'per' 
        from questions que 
        INNER JOIN subjects subj on (subj.subject_id = que.section_id AND subj.quiz_id = 43) 
		INNER JOIN categories sd on sd.id = que.section_id 
		INNER JOIN stdanswers sa on sa.question_id = que.id 
		WHERE sa.attempt_id = 191 AND sa.isRight = 0 
        GROUP BY que.section_id ORDER BY que.section_id 
	)result;



	*/
	

}