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


	public function setBasicScore($attempt_id, $correctAnswers)
	{
		
		$sql = "UPDATE stdattempts sta
		INNER JOIN  enrollment en on sta.enroll_id = en.id 
		INNER JOIN quiz qz on qz.id = en.quiz_id 
		SET sta.score = $correctAnswers * (qz.maxScore / qz.noques) WHERE sta.id = $attempt_id";


		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

			return false;


	}
	

}