<?php class questionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'questions');
	}


	public function index()
	{
		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		
		if($questions = $this->module->listall())
		{
			$data['questions'] = $questions;
			$statusCode = 200;
		}

		else {
		
			$data['debug'] = $this->module->DB;
			$data['message'] = "No questions found please add some";
			$statusCode = 204;
		}


		return View::responseJson($data, $statusCode);


	}


	public function save()
	{

		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		$keys = array('category_id', 'section_id', 'level_id', 'type_id', 'queDesc', 'optionA', 'optionB', 'optionC', 'optionD', 'answer');
		$dataPayload = $this->module->DB->sanitize($keys);
		$dataPayload['user_id'] = $this->jwtUserId();
		$dataPayload['status'] = 1;


		if(jwACL::authRole() == 'contributor')
		{
			$contributorModule = $this->load('module', 'contributor');
			$entity_id = $contributorModule->pluckEntity_id($this->jwtUserId());
			$dataPayload['entity_id'] = $entity_id;
			$dataPayload['scope'] = 'private';
		}

		else if(jwACL::authRole() == 'entity')
		{
			
			$dataPayload['entity_id'] = $this->jwtUserId();

			if(!isset($_POST['quiz_id']))
			{
				$dataPayload['scope'] = 'private';		
			}


		}


		if(isset($_POST['quiz_id']))
		{
			$dataPayload['quiz_id'] = $_POST['quiz_id'];

			$dataPayload['scope'] = 'linked';
		}


		if($last_Id = $this->module->store($dataPayload))
		{
			$data['last_id'] = $last_Id;
			$statusCode = 200;

			$data['message'] = "New Question Added Successfully";


			if(isset($_POST['mediaIds']))
			{

				$mediaPost = $_POST['mediaIds'];

				$mediaPayload = [];

				
				$questionMediaModule = $this->load('module', 'questionsMedia');
				if($questionMediaModule->saveQuestionMedia($last_Id, $mediaPost))
				{
					$data['message'] = "New Question Added Successfully with media";
					$statusCode = 200;
				}
				else {
					$data['message'] = "Saved but failed to attach media to question";
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

		return View::responseJson($data, $statusCode);

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

		return View::responseJson($data, $statusCode);

	}


	public function statusToggle()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


		/*
		bind permission
		!admin check ownership
		*/


		$_POST = Route::$_PUT;
		$id = $this->getID();

		$statusValue = $_POST['status'];

		if($this->module->statusToggle($statusValue, $id))
		{

			$stsText = ($statusValue == 1) ? 'enabled' : 'disabled';
			$data['message'] = "Question status : " . $stsText;
			$data['status'] = true;
			$statusCode = 200;

		}

		else {

			$data['message'] = "Failed while updating status";
			$data['status'] = true;
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}



	public function singlequestion()
	{

		$queID = $this->getID();
		$user_id = jwACL::authUserId();
		$role = jwACL::authRole();

		if($question = $this->module->getsingle($user_id, $role, $queID))
		{
			$data['question'] = $question[0];
			$statusCode = 200;	
		}
		else {

			$data['message'] = 'Question data not found';
			$statusCode = 500;	

		}

		return View::responseJson($data, $statusCode);

	}


	public function update()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();

		$queID = $this->getID();

		$user_id = jwACL::authUserId();

		$role = jwACL::authRole();

		$_POST = Route::$_PUT;


		$questionType = $_POST['type_id'];

		$keys = array('queDesc', 'answer');

		if($questionType == 1 || $questionType == 3)
		{
			array_push($keys, 'optionA', 'optionB', 'optionC', 'optionD');
		}

		else if($questionType == 2)
		{
			array_push($keys, 'optionA', 'optionB');	
		}


		$dataPayload = $this->module->DB->sanitize($keys);

		if($this->module->updateQuestionBasic($dataPayload, $queID))
		{

			$data['message'] = "Question updated successfully";
			$data['payload'] = $dataPayload;

			$statusCode = 200;

		}

		else {

			$data['message'] = "Failed updating question";
			$statusCode = 500;
		}

		return View::responseJson($data, $statusCode);

	}







}