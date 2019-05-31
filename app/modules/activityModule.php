<?php 
class activityModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'activity';		
	}



	public function pluckByRole($rolename)
	{

		return $this->DB->pluck('role')->where("role = '".$rolename."'");
	}


	public function startNewActivity($attempt_id)
	{

		$dataPayload = array('attempt_id'=> $attempt_id, 'answer'=> 'first');


		if($lastId = $this->DB->insert($dataPayload))
		{
			return $lastId;
		}

		return false;

	}



	public function activityHandler($dataPayload)
	{


		$attempt_id = $dataPayload['attempt_id'];


		if($this->isFirst($attempt_id))
		{

			$this->removeFirst(['attempt_id', $dataPayload['attempt_id']]);
			return $this->saveNew($dataPayload);


		}

		else if($id = $this->checkExisting($dataPayload['attempt_id'], $dataPayload['question_id']))
		{


			// do udpate


			return $this->update($dataPayload, $id);
			

		}

		else {
		
			// do a save
			return $this->saveNew($dataPayload);


		}


		/* 

		check if that is a first entry in the table if yes then udpate it

		check recored with existing attempt and questions id 

		if we have then update the existing one otherwise 

		add new row to the record

		*/

	}



	public function addNewActity($dataPayload)
	{

		if($this->DB->insert($dataPayload))
		{
			return true;
		}

		return false;

	}


	public function isFirst($attempt_id)
	{

		return $this->DB->rawSql("SELECT id from activity where attempt_id = $attempt_id AND question_id IS NULL LIMIT 1")->returnData();

	}

	public function removeFirst($dataPayload)
	{

		$this->DB->delete($dataPayload);

	}



	public function checkExisting($attempt_id, $question_id)
	{


		if($row = $this->DB->build('S')->Colums('id')->Where("attempt_id = '".$attempt_id."'")->Where("question_id = '".$question_id."'")->go()->returnData())
		{
			return $row[0]['id'];
		}

		return false;
	}


	public function saveNew($dataPayload)
	{

		return $this->DB->insert($dataPayload);
	}


	public function update($dataPayload, $id)
	{
		
		return $this->DB->update($dataPayload, $id);
		
	}





}