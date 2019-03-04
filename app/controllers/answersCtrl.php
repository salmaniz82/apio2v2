<?php 
class answersCtrl extends appCtrl 
{

	public $module;

	public function __construct()
	{
		$this->module = $this->load('module', 'answers');

	}


	public function patchAnswers()
	{

		
		$payload = $_POST['answers'];

		$attempt_id = $payload[0]['attempt_id'];
		$dataset['cols'] = array('attempt_id', 'question_id', 'answer');
		$dataset['vals'] = $payload;

		if($this->module->patchBulkAnswers($dataset))
		{
			
			$correct = $this->module->markRightAnswers($attempt_id);
			$inCorrect = $this->module->markIncorrectAnswers($attempt_id);
			$this->module->setBasicScore($attempt_id, $correct);

			$data['message'] = "Answers Were Saved";
			$data['correct'] = $correct;
			$data['wrong'] = $inCorrect;
			$statusCode = 200;
			$data['status'] = true;
		}

		else 
		{
			$data['message'] = "Cannot Save Amswers";
			$statusCode = 400;
			$data['status'] = false;
		}

		return View::responseJson($data, $statusCode);

	}






}