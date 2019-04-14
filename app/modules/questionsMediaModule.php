<?php class questionsMediaModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'quemedia';

	}


	public function saveQuestionMedia($queId, $mediaIds)
	{

		$dataset['cols'] = array('question_id', 'media_id');	

		$keyCounter = 0;

		foreach($mediaIds as $key => $value) {

			$dataset['vals'][$keyCounter] = array($queId, $value);

			$keyCounter++;
		}	

		if($this->DB->multiInsert($dataset))
		{
			return true;
		}

		return false;

	}


	
	


}