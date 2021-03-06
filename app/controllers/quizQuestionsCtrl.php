<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class quizQuestionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'quizQuestions');
	}


	public function getQuestionByQuizId()
	{
		
		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		
		


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
		
			

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();		

	


		if(!jwACL::has('quiz-add')) 
			return $this->accessDenied();




		$quiz_id = $this->getID();

		$this->module->globalThresholdByQuizId($quiz_id);

		$entity_id = $this->jwtUserId();


		$quizModule = $this->load('module', 'quiz');


		$quizModule = $this->load('module', 'quiz');


		if(!$quizModule->checkExists($quiz_id))
		{


			$data['message'] = 'Quiz does not exist with supplied id';
			$statusCode = 400;
			return View::responseJson($data, $statusCode);

		}



		if($quizModule->isDLSEnabledQuiz($quiz_id))
		{

			return $this->dlsAllocationHandler($quiz_id, $entity_id);

		}

		if($affectedRows = $this->module->allocateQuestionsByQuizId($quiz_id, $entity_id))
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

		


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		
		if(!jwACL::has('quiz-questions-list')) 
			return $this->accessDenied();



		$quiz_id = $this->getID();




		$this->module->globalThresholdByQuizId($quiz_id);


		if($questions = $this->module->listMatchQuestions($quiz_id))
		{
			$data['status'] = true;
			$data['questions'] = $questions;
			$data['noQues'] = sizeof($questions);
			$data['category'] = $questions[0]['category'];
			$data['qqSubjects'] = $this->module->quizAllocatedQuestionsSubjects($quiz_id);
			$data['threshold'] = $this->module->thresholdValidation($quiz_id);
			$data['globalThreshold'] = GLOBAL_Threshold;
			$data['gThresholdCount'] = (int) $this->module->globalThresholdCount($quiz_id);

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

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


		
		if(!jwACL::has('quiz-questions-sync')) 
			return $this->accessDenied();



		$quiz_id = $this->getID();

		$quizModule = $this->load('module', 'quiz');

		$this->module->globalThresholdByQuizId($quiz_id);


		if($quizModule->isDLSEnabledQuiz($quiz_id))
		{

			return $this->dlsQuestionsSynchronizeCheck($quiz_id);

		}


		$entity_id = $this->jwtUserId();


		if($newQuesAvailable = $this->module->synchronizeCheck($quiz_id, $entity_id))
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
			$statusCode = 204;
		}

		return View::responseJson($data, $statusCode);

	}


	public function dlsQuestionsSynchronizeCheck($quizId)
	{

		
		$entity_id = $this->jwtUserId();


		if($newQuesAvailable = $this->module->dlsgetUnallocatedQuestionIds($quizId, $entity_id))
		{
			$data['queIDs'] = $newQuesAvailable[0]['question_id'];
			$data['noQues'] = $newQuesAvailable[0]['quecount'];
			$data['status'] = true;
			$data['message'] = $data['noQues'] . " New Questions Available";
			$statusCode = 200;
		}

		else {
			$data['querystatus'] = $newQuesAvailable;
			$data['noQues'] = 0;
			$data['message'] = "All Question are already sync";
			$data['status'] = false;
			$statusCode = 204;
		}

		return View::responseJson($data, $statusCode);

	}


	public function processSynchronize()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		
		if(!jwACL::has('quiz-questions-sync')) 
			return $this->accessDenied();


		if(!isset($_POST)) 
		{
			return $this->emptyRequestResponse();
		}


		$quiz_id = (isset($_POST['quiz_id'])) ? $_POST['quiz_id'] : NULL;


		if($quiz_id == NULL)
		{


			$data['message'] = "Quiz id not provided";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}



		$quizModule = $this->load('module', 'quiz');


		if(!$quizModule->checkExists($quiz_id))
		{

			$data['message'] = "Quiz not found with provided id";
			$data['status'] = false;
			$statusCode = 406;

			return View::responseJson($data, $statusCode);

		}






		$queIDs = ( isset($_POST['queIDs']) ) ? $_POST['queIDs'] : NULL;


		if($queIDs == NULL)
		{


			$data['message'] = "Question ids not provided";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

			die();
		}



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


		return View::responseJson($data, $statusCode);


	}



	public function qqStatusToggle()
    {


    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		
		
		
		if(!jwACL::has('quiz-question-edit')) 
			return $this->accessDenied();





    	$_POST = Route::$_PUT;

    	$qqid = $_POST['qqId'];

        $quiz_id = (int) Route::$params['quiz_id'];
        $subject_id = (int) Route::$params['subject_id'];

        $subjectModule = $this->load('module', 'subject');

        $quizModule = $this->load('module', 'quiz');

        if(!$quizModule->isDLSEnabledQuiz($quiz_id))
        {
        	$subjectsState = $subjectModule->canToggleQuizQuestions($quiz_id, $subject_id);
        }

        else {

        	$subjectsState = $subjectModule->canDLSToggleQuestion($quiz_id, $qqid);
        	
        }


        


        $dataPayload['status'] = $_POST['status'];
        


        if($dataPayload['status'] == 0 && is_array($subjectsState) && $subjectsState['enableStatusToggle'] == 0)
        {

            $statusCode = 400;
            $data['status'] = false;
            $data['message'] = $subjectsState['quePerSection'] . " questions required ". " available " . $subjectsState['allocated'];
            $data['subject'] = $subjectsState['subjects'];
            return View::responseJson($data, $statusCode);

        }

        $data['toggleRequest'] = $dataPayload['status'] == 1;

        
        if($this->module->statusToggle($dataPayload, $qqid))
        {
        	
	       	$statusCode = 200;
            $data['status'] = true;
            $data['message'] = ($dataPayload['status'] == 1) ? 'Questions Enabled for Quiz' : 'Questions Disabled for Quiz';
        }

        else {

        	$statusCode = 500;
            $data['status'] = false;
            $data['message'] = "Failed while updating question status";
        }

        return View::responseJson($data, $statusCode);

    }



    public function dlsAllocationHandler($quiz_id, $entity_id)
    {



    	if($affectedRows = $this->module->allocateDLSQuestionsByQuizId($quiz_id, $entity_id))
		{

			
			if($affectedRows < 1)
			{

				$data['message'] = 'Already allocated try synchornization instead';
				$statusCode = 400;

			}

			else {
				$data['message'] = $affectedRows . " Questions were added to Quiz DLS ";
				$statusCode = 200;	
			}
			
		}

		else {
			$data['message'] = 'No Matching Questions were found';
			$statusCode = 404;
		}


		return View::responseJson($data, $statusCode);
    }


}