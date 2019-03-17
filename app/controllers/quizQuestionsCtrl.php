<?php class quizQuestionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'quizQuestions');
	}


	public function getQuestionByQuizId()
	{
		
		$quiz_id = $this->getID();


		if($questions = $this->module->quizQuestionsByQuizId($quiz_id))
		{
			$data['questions'] = $questions;
			$data['status'] = true;
			$statusCode = 200;
		}

		else {

			$data['message'] = 'No matched categories in questions to be sync';
			$data['status'] = false;
			$statusCode = 404;
		}

		return View::responseJson($data, $statusCode);
	}


	public function allocateQuestions()
	{
		
		$quiz_id = $this->getID();

		if($affectedRows = $this->module->allocateQuestionsByQuizId($quiz_id))
		{
			

			if($affectedRows < 1)
			{

				$data['message'] = 'Already allocated try synchornization instead';
				$statusCode = 400;

			}

			else {
				$data['message'] = $affectedRows . " Questions were added to Quiz ";
				$statusCode = 200;	
			}
			
		}

		else {
			$data['message'] = 'No Matching Questions were found';
			$statusCode = 404;
		}


		return View::responseJson($data, $statusCode);

	}



	public function listMatchQuestions()
	{

		$quiz_id = $this->getID();


		if($questions = $this->module->listMatchQuestions($quiz_id))
		{
			$data['status'] = true;
			$data['questions'] = $questions;
			$data['noQues'] = sizeof($questions);
			$data['category'] = $questions[0]['category'];
			$data['qqSubjects'] = $this->module->quizAllocatedQuestionsSubjects($quiz_id);
			$statusCode = 200;
		}
		else 
		{
			$data['message'] = "NO Questions allocated for this quiz";
			$data['status'] = false;
			$data['debug'] = $this->module->DB;
			$statusCode = 400;
		}

		return View::responseJson($data, $statusCode);

	}


	public function questionSyncCheck()
	{

		$quiz_id = $this->getID();


		if($newQuesAvailable = $this->module->synchronizeCheck($quiz_id))
		{
			$data['queIDs'] = $newQuesAvailable[0]['question_id'];
			$data['noQues'] = $newQuesAvailable[0]['quecount'];
			$data['status'] = true;
			$data['message'] = $data['noQues'] . " New Questions Available";
			$statusCode = 200;
		}

		else {
			$data['noQues'] = 0;
			$data['message'] = "All Question are already sync";
			$data['status'] = false;
			$statusCode = 404;
		}


		View::responseJson($data, $statusCode);

	}


	public function processSynchronize()
	{

		$quiz_id = $_POST['quiz_id'];
		$queIDs = $_POST['queIDs'];

		if($rows = $this->module->SynchronizeHandler($quiz_id, $queIDs))
		{
			$data['added'] = $rows;
			$data['message'] = $rows . " New Questions Synchronized ";
			$data['status'] = true;
			$statusCode = 200;


			if($newQuestions = $this->module->newSyncAddedQuestions($quiz_id, $queIDs))
			{
				$data['newQuestions'] = $newQuestions;
			}



		}
		else {


			$data['message'] =  " Error while Synchronize ";
			$data['status'] = false;
			$statusCode = 400;

			$data['db'] = $this->module->DB;

		}


		View::responseJson($data, $statusCode);


	}



}