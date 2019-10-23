<?php 
class markedModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'markedtable';
	}



	public function multiInsert($payload)
	{
		

		if($this->DB->multiInsert($payload))
		{
			return true;
		}

		return false;

	}


	public function preparePayload()
	{

		$dataset['cols'] = array('attempt_id','answer_id', 'question_id', 'markedStatus');
		$dataset['vals'] = $payload;
	}


	public function deletePreviousScoreSheet($attempt_id)
	{




		$sql = "DELETE from scoresheet where attempt_id = $attempt_id";


		if($this->DB->rawSql($sql))
		{
			return true;
		}

		return false;

	}


	public function deleteMarkedTableData($attempt_id)
	{

		$sql = "DELETE FROM markedtable where attempt_id = $attempt_id";

		if($this->DB->rawSql($sql))
		{
			return true;
		}

		return false;
	}


	public function neutralStatusAnswersTable($attempt_id)
	{

		$sql = "UPDATE stdanswers SET markedStatus = NULL where attempt_id = $attempt_id";

		if($this->DB->rawSql($sql))
		{
			return true;
		}

		return false;

	}



}