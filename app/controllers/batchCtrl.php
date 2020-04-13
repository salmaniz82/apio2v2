<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');

class batchCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		
		$this->module = $this->load('module', 'batch');

	}


	public function batchList()
	{

		

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::has('batch-list')) 
	 		return $this->accessDenied();



		$user_id = $this->jwtUserId();

		$role_id = jwACL::roleId();

		if($batches = $this->module->listAllBatches($user_id, $role_id))
		{
			$data['batches'] = $batches;

			if($eligQuiz = $this->module->listBatchEligibleQuiz($user_id))
			{
				$data['eligQuiz'] = $eligQuiz;
			}

			else {

				$data['eligQuiz'] = false;

			}

			$statusCode = 200;
		}
		else {
		
			$statusCode = 404;
			$data['message'] = "No Batch Created";

		}	
		
		return View::responseJson($data, $statusCode);
	}


	public function batchItems()
	{

		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::has('batch-list')) 
	 		return $this->accessDenied();



		$batchId = $this->getID();

		$user_id = $this->jwtUserId();

		$role_id = jwACL::roleId();

		$partProgress = $this->module->candParticipation($batchId, $user_id, $role_id);

		$data['batchProgress'] = $partProgress;

		$statusCode = 200;

		return View::responseJson($data, $statusCode);

		die();

	}



	public function listEligibleQuiz()
	{

		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(!jwACL::has('batch-add')) 
	 		return $this->accessDenied();


		
		$user_id = $this->jwtUserId();



		if($quiz = $this->module->listBatchEligibleQuiz($user_id))
		{
			
			$data['eligQuiz'] = $quiz;
			$statusCode = 200;

		}

		else {

			$data['message'] = "There are no eligible quiz available for batch processing";
			$statusCode = 404;

		}

		return View::responseJson($data, $statusCode);

		die();

	}



	public function enrollToBatch($batch_id, $student_id)
	{



	}



	public function save()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		


		if(!jwACL::has('batch-add')) 
			return $this->accessDenied();


		$this->load('external', 'gump.class');

		$gump = new GUMP();


		if(isset($_POST)) 
		{

			$_POST = $gump->sanitize($_POST);

		}

		else {

			return $this->emptyRequestResponse();

		}

		
		$gump->validation_rules(array(
			
			'title'    =>  'required|min_len,6',
			'maxScore' 	=> 'required|integer',
			'passingScore' => 'required|integer',
			'quizIds' => 'required'
			
		));



		$pdata = $gump->run($_POST);


		if($pdata === false) 
		{

			// validation failed
			$data['status'] = false;

			$errorList = $gump->get_errors_array();
			$errorFromArray = array_values($errorList);
			$data['errorlist'] = $errorList;
			$data['message'] = $errorFromArray[0];
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}


		$keys = array('title', 'maxScore', 'passingScore');

		$user_id = $this->jwtUserId();

		$quizIds = $_POST['quizIds'];

		$dataPayload = $this->module->DB->sanitize($keys);

		$dataPayload['user_id'] = $this->jwtUserId();

		if($batch_id = $this->module->saveBatchMaster($dataPayload))
		{

			if($this->module->saveBatchQuiz($batch_id, $quizIds))
			{

				$data['message'] = "All done with Success";
				$statusCode = 200;
			}

			else {

				$data['message'] = "quiz attach failed";
				$data['debug'] = $this->module->DB;
				$statusCode = 500;

			}


			$data['lastBatch'] = $this->module->getBatchById($batch_id, $user_id);

		}

		else {

			$data['message'] = "New Batch Save Failed";
			$statusCode = 500;
		}


		return View::responseJson($data, $statusCode);


	}


	public function progressOverview()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(!jwACL::has('batch-list')) 
	 		return $this->accessDenied();



		$batchId = $this->getID();

		$canidateId = $this->getParam('candiateId');


		if($rows = $this->module->candiateBatchQuizPerformance($batchId, $canidateId))
		{
			
			$data['batchCanidateOverview'] = $rows;
			$statusCode = 200;


			
			$summary['tmin'] = array_sum(array_column($rows, 'minScore'));
			$summary['tmax'] = array_sum(array_column($rows, 'maxScore'));
			$summary['tscore'] = array_sum(array_column($rows, 'score'));
			$summary['tper'] = round($summary['tscore'] / $summary['tmax'] * 100, 2);
			$summary['result'] = ($summary['tscore'] < $summary['tmin'] ) ? 'Fail' : 'Pass';

			$data['summary'] = $summary;
			
		}

		else {

			$data['message'] = "No Available";
			$statusCode = 404;
		}

		return View::responseJson($data, $statusCode);

	}


	public function enrollProcedure()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		
		if(!jwACL::has('quiz-enroll-add')) 
			return $this->accessDenied();


		
		$email = $_POST['email'];
		$batch_id = $this->getID();

		$usersModule = $this->load('module', 'user');


		



		if(!$usersModule->emailExists($email))
		{

			$data['message'] = "User does not exist with provide email";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			die();
		}


		$candidate_id = $usersModule->pluckIdByEmail($email);

		$user = $usersModule->userById($candidate_id);


		if($user[0]['status'] == 0)
		{
			$data['message'] = "Disabled user cannot be enrolled to a batch";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			die();
		}


		$quizCount = $this->module->batchQuizCount($batch_id);

		$activeQuizCount = $this->module->batchActiveQuizCount($batch_id);


		if($activeQuizCount != $quizCount)
		{
			
			$data['message'] = "All Quiz must be active and allowing enrollment";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			die();

		}


		$quizValidDateCount = $this->module->batchValidDatesQuizCount($batch_id);

		if($quizValidDateCount != $quizCount)
		{
			$data['message'] = "Expired quiz cannot allow enrollment for batch";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			die();
		}


		if($this->module->duplicateCheck($batch_id, $candidate_id))
		{
			$data['message'] = "Already enrolled to Batch";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			die();
		}

		$this->module->patchEnroll($batch_id, $candidate_id);



		if($this->module->batchTagging($batch_id, $candidate_id))
		{
			$data['message'] = "Batch Enrollment completed Successfully";
			$data['lastCandiate'] = $this->module->getSingleTaggedEnrolledCanidate($candidate_id, $batch_id);
			$statusCode = 200;
		}

		else {

			$data['message'] = "Batch Enrollment Failed";
			$data['debug'] = $this->module->DB;
			$statusCode = 503;

		}
		
		return View::responseJson($data, $statusCode);

	}



	public function taggedCanidates()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(!jwACL::has('batch-list')) 
	 		return $this->accessDenied();



		$batch_id = $this->getID();

		if($candidates = $this->module->listTaggedStudents($batch_id))
		{
			$data['candidates'] = $candidates;
			$statusCode = 200;
		}

		else {

			$data['message'] = 'No canidates enrolled to batch';
			$statusCode = 206;

		}


		return View::responseJson($data, $statusCode);


	}


	public function batchDetails()
	{
		
		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::has('batch-list')) 
	 		return $this->accessDenied();



		$batch_id = $this->getID();

		


		if($details = $this->module->batchDetails($batch_id))
		{
			$data['batchDetails'] = $details;
			$statusCode = 200;


			$summary['tmin'] = array_sum(array_column($data['batchDetails'], 'minScore'));
			$summary['tmax'] = array_sum(array_column($data['batchDetails'], 'maxScore'));

			$data['summary'] = $summary;

			/*


			$summary['tmin'] = array_sum(array_column($rows, 'minScore'));
			$summary['tmax'] = array_sum(array_column($rows, 'maxScore'));
			$summary['tscore'] = array_sum(array_column($rows, 'score'));
			$summary['tper'] = round($summary['tscore'] / $summary['tmax'] * 100, 2);
			$summary['result'] = ($summary['tscore'] < $summary['tmin'] ) ? 'Fail' : 'Pass';

			*/


		}

		else {

			$data['message'] = 'Batch Details were not found';
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);


	}






}