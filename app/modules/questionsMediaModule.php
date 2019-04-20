<?php class questionsMediaModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'quemedia';

	}


	public function saveQuestionMedia($queId, $mediaPayload)
	{

		$dataset['cols'] = array('question_id', 'media_id', 'qmlabel');	

		$keyCounter = 0;

		foreach($mediaPayload as $key => $value) {

			$dataset['vals'][$keyCounter] = array($queId, $value['id'], $value['title']);

			$keyCounter++;
		}	

		if($this->DB->multiInsert($dataset))
		{
			return true;
		}

		return false;

	}


	
	


}