<?php class questionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'questions');
	}


	public function index()
	{
		$data = $this->module->listall();
		View::responseJson($data, 200);
	}


	public function save()
	{

		$keys = array('category_id', 'section_id', 'level_id', 'type_id', 'queDesc', 'optionA', 'optionB', 'optionC', 'optionD', 'answer');
		$dataPayload = sanitize($keys);
		$dataPayload['user_id'] = $this->jwtUserId();
		$dataPayload['status'] = 1;


		if(isset($_POST['quiz_id']))
		{
			$dataPayload['quiz_id'] = $_POST['quiz_id'];
		}


		if($last_Id = $this->module->store($dataPayload))
		{
			$data['last_id'] = $last_Id;
			$statusCode = 200;


			if(isset($_POST['mediaIds']))
			{

				$mediaPost = $_POST['mediaIds'];

				$mediaPayload = [];

				
				$questionMediaModule = $this->load('module', 'questionsMedia');
				if($questionMediaModule->saveQuestionMedia($last_Id, $mediaPost))
				{
					$data['message'] = "New Question Added Successfully";
					$statusCode = 200;
				}
				else {
					$data['message'] = "Failed to attach media to question";
					$statusCode = 406;
				}

			}


			/*
				if that stored with quiz id that need to be auto synced to question table
			*/

				if(isset($_POST['quiz_id']))
				{
					$quiz_id = $_POST['quiz_id'];
					$quizQuestionModule = $this->load('module', 'quizQuestions');


					$PrivateQuestionData = array(
						'quiz_id' => $quiz_id,
						'question_id' => $last_Id
					);



					if($quizQuestionModule->autoSyncPrivateQuizQuestions($PrivateQuestionData))
					{
							$data['message'] = "New Question Added and Synced";
					}

					else {
						 $data['message'] = "New Question But not sync to questions";	
						 
					}
				}
				
		}
		else {
			$data['res'] = $last_Id;
			$statusCode = 500;
			$data['message'] = "Failed to Add new Question";
			$data['db'] = $this->module->DB->connection;

		}


		View::responseJson($data, $statusCode);

	}


	public function summaryCount()
	{
		if($summary = $this->module->summaryCount())
		{
			$data['queSum'] = $summary;
			$data['status'] = true;
			$statusCode = 200;
		}
		else {
			$data['status'] = false;
			$statusCode = 500;
		}

		View::responseJson($data, $statusCode);

	}


}