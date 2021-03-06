<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');

class quizCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'quiz');
	}


	public function index()
	{	


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::has('quiz-list')) 
			return $this->accessDenied();

		
		$allowedRoles = [1,2, 6];
    	if( JwtAuth::validateToken() && in_array((int) JwtAuth::$user['role_id'], $allowedRoles) )
		{

			$user_id = $this->jwtUserId();
			$role_id = $this->jwtRoleId();

			if($role_id == 6)
			{
				/*
				creator entity id
				*/

				$user_id = JwtAuth::$user['created_by'];


				
			}

			if($quiz = $this->module->fetchQuizList($user_id, $role_id))
			{

				$data['status'] = true;
				$data['quiz'] = $quiz;
				$statusCode = 200;

			}

			else {

				$data['status'] = false;
				$data['message'] = "NO Quiz found";
				$statusCode = 404;

			}

			return View::responseJson($data, $statusCode);

		}
		else {
			return $this->uaReponse();		
		}

	}



	public function single()
	{

		

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		$quizID = $this->getID();


		if($quizID === 0)
			return $this->nonIntegorResponse();	


		


		if($quiz = $this->module->getQuizById($quizID))
		{
			$data['quiz'] = $quiz;
			$data['status'] = true;
			$statusCode = 200;
		}

		else {

			$data['message'] = "Cannot Find Quiz with ID of $quizID";
			$statusCode = 500;

		}	



		return View::responseJson($data, $statusCode);



	}


	public function save()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();

		
		if(!jwACL::has('quiz-add')) 
			return $this->accessDenied();



		return die('This route is updated to wizard and no longer in use');
		

		$keys = array('title', 'category_id', 'minScore', 'maxScore', 'startDateTime', 'endDateTime', 'noques', 'duration');
		$dataPayload = $this->module->DB->sanitize($keys);


		$dataPayload['status'] = 0;
		$dataPayload['enrollment'] = 0;
		$dataPayload['user_id'] = $this->jwtUserId();

		$decipline = $_POST['cleanDesp'];
		$subDecipline = $_POST['cleanSubDesp'];

		if($res = $this->module->addQuiz($dataPayload))
		{
			
			$statusCode = 200;
			$data['last_id'] = $res;

			$categoryModule = $this->load('module', 'category');
        	$subDescIds = $categoryModule->verifySubDeciplines($decipline, $subDecipline);
        	$quiz_id = $res;
        	// insert sub decipline with ids

        	$subjectModule = $this->load('module', 'subject');
        	if($subjectModule->saveQuizSubjects($quiz_id, $subDescIds))
        	{
        		$data['message'] = "New Quiz added with subjects";
        	}
        	else {
        		$data['message'] = "New Quiz failed to save subjects";	
        	}

		}
		else {

			$data['message'] = "New Quiz added";
			$data['debug'] = $res;
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}


	public function globals()
	{

		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		$categoryModule = $this->load('module', 'category');
		$sectionModule = $this->load('module', 'sections');

		$typeModule = $this->load('module', 'type');
		$levelModule = $this->load('module', 'level');

		$data['cat'] = $categoryModule->catTree();

		
		$data['sections'] = $categoryModule->flatJoinList();

		
		$data['types'] = $typeModule->listall();

		
		$data['levels'] = $levelModule->listall();

		if(jwACL::authRole() == 'contributor' || jwACL::authRole() == 'content developer')
		{

			$boundcategoryModule = $this->load('module', 'boundcategory');
			$user_id = jwACL::authUserId();
			$data['topCategory'] = $boundcategoryModule->pluckTopCategoryByUserId($user_id);

		}


		View::responseJson($data, 200);


	}



	public function studentQuizList()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		$studentId = $this->jwtUserId();
		$this->module->studentQuizList($studentId);

	}


	public function adminQuizListHandler()
	{
		if($quiz = $this->module->listQuiz())
		{
			$data['quiz'] = $quiz;
			$data['status'] = true;
			$statusCode = 200;	
		}
		else {

			$data['status'] = false;
			$statusCode = 404;	

		}

		return View::responseJson($data, $statusCode);
	}


	public function studentQuizListHandler($timestamp = null)
	{



		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		$allowedRoles = [4];
    	if( JwtAuth::validateToken() && in_array((int) JwtAuth::$user['role_id'], $allowedRoles) )
		{

			$student_id = $this->jwtUserId();
			$role_id = $this->jwtRoleId();

			if($attempted = $this->module->getStudentAttemptedQuizList($student_id))
			{
					$data['attempted'] = $attempted;
			}
			else {
					$data['attempted'] = 0;
			}
			
			if($quiz = $this->module->getStudentPendingQuizList($student_id))
			{
				$data['quiz'] = $quiz;
				$data['status'] = true;
				$statusCode = 200;			
			}

			else {
				$data['quiz'] = 0;
				
			}

			if($data['quiz'] == 0 && $data['attempted'] == 0)
			{
				$data['message'] = "Not enrolled to any quiz";
				$data['status'] = false;
				$statusCode = 400;				
			}
			else {

				$statusCode = 200;				

			}


			if(isset($timestamp))
			{
				$data['timestamp'] = $timestamp;
			}



			return View::responseJson($data, $statusCode);


		}

		else {

			return $this->accessDenied();
			
		}



	}



	public function teacherQuizListHandler($user_id, $role_id)
	{

		// fetch teacher quiz list

	}


	public function checkValidityCount()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();
			


		$quiz_id = $this->getID();



		if($quiz_id === 0)
			return $this->nonIntegorResponse();





		if($row = $this->module->quizQuestionEligible($quiz_id))
		{
			
			$data['validity'] = $row;

			$validity = $data['validity'][0]['validity'];
			$required = $data['validity'][0]['required'];
			$allocated = $data['validity'][0]['allocated'];

			$diff = $required - $allocated;

			$statusCode = 400;


			if($allocated == 0 && $required > 0)
			{
				$data['message'] = "Quiz has not been allocated any questions $required questions are required for this quiz";
			}

			else if($allocated > 0 && $validity == 'invalid')
			{
				$data['message'] = "$allocated currently allocated $diff more questions required for this quiz";
			}

			else if($validity == 'valid' && $allocated >= $required)
			{
				$data['message'] = "Required $required currently allocated $allocated";
				$statusCode = 200;
			}

			else if ($allocated == 0 && $required == NULL)
			{
				$data['message'] = "Unable to access quiz required questions";
				
			}

		}

		else {

			$statusCode = 404;
			$data['message'] = "unable to fetch record";

		}

		View::responseJson($data, $statusCode);
	}


	public function enrollToggle()
	{

		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::has('quiz-enroll-toggle') && !jwACL::isAdmin()) 
			return $this->accessDenied();



		$quiz_id = $this->getID();
		$_POST = Route::$_PUT;

		$userID = jwACL::authUserId();

		if(!$this->module->isOwnedByUser($quiz_id, $userID) && !jwACL::isAdmin())
			return $this->ownerDisqualifyResponse();





		$quizQuestionModule = $this->load('module', 'quizQuestions');

		

		if($this->module->isDLSEnabledQuiz($quiz_id) && !$quizQuestionModule->isDlsQualifiedNitroMode($quiz_id) && $_POST['enrollment'] == 1)
		{

			$data['message'] = "Not qualified for DLS mode, for static mode disable DLS and try again";
			$statusCode = 406;

			$data['quiz_id'] = $quiz_id;
			$data['value'] = $_POST['enrollment'];

			return View::responseJson($data, $statusCode);

			die();

		}




		$validity = $this->module->quizQuestionEligible($quiz_id);

		$statusValidity = $validity[0]['validity'];
		$required = $validity[0]['required'];
		$allocated = $validity[0]['allocated'];

		$missingCount = $required - $allocated;

		$dataPayload['enrollment'] = $_POST['enrollment'];


		if($_POST['enrollment'] == 0)
		{
			return $this->enrollmentToggleHandler($dataPayload, $quiz_id);
		}


		if($statusValidity == 'valid' && $required > 0 && $allocated != 0)
		{
			// attempt to enable enrollment	once passed the allocation test


			$distroData = $this->module->quizDistroValidity($quiz_id);	

			if($distroData == false || $distroData['distStatus'] == 'invalid')
			{
				$data['message'] = "Please resolve Weight distribution invalid configuration";
				$statusCode  = 400;
			}

			else {

				return $this->enrollmentToggleHandler($dataPayload, $quiz_id);

			}


		}
		else if($statusValidity == 'invalid' && $required != NULL && $allocated == 0) {

			// valid quiz out of count
			$data['message'] = "Please allocated " . $required . " questions to quiz";
			$statusCode  = 400;
		}

		else if ($statusValidity == 'invalid' && $required != NULL && $allocated > 0)
		{
			$data['message'] = "Allocated " . $allocated . " required " . $missingCount . " more to be allocated";	
			$statusCode  = 400;

		}

		else if($statusValidity == 'invalid' && $required == NULL)
		{
			$data['message'] = "Cannot access quiz requirement";
			$statusCode  = 400;
		}

		else {
			$data['message'] = "Invalid Request";
			$statusCode  = 400;
		}


			$data['quiz_id'] = $quiz_id;
			$data['value'] = $dataPayload['enrollment'];

		return View::responseJson($data, $statusCode);

	}



	public function statusToggle()
	{
		
		
		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::has('quiz-status-toggle') && !jwACL::isAdmin()) 
			return $this->accessDenied();


		$quiz_id = $this->getID();
		$_POST = Route::$_PUT;



		$userID = jwACL::authUserId();

		if(!$this->module->isOwnedByUser($quiz_id, $userID) && !jwACL::isAdmin())
			return $this->ownerDisqualifyResponse();


		$dataPayload['status'] = $_POST['status'];


		if($this->module->update($dataPayload, $quiz_id))
		{

			$data['message'] = ($dataPayload['status'] == 1) ? 'Status Enabled' : 'Status Disabled';
			$statusCode  = 200;
			$data['qstatus'] = $dataPayload['status'];

		}

		else {
				$data['message'] = "Unable to update quiz status";
				$data['qstatus'] = $dataPayload['status'];
				$statusCode  = 500;

		}


		return View::responseJson($data, $statusCode);


	}


	public function enrollmentToggleHandler($dataPayload, $quiz_id)
	{


		if($this->module->update($dataPayload, $quiz_id))
		{
			$data['message'] = ($dataPayload['enrollment'] == 1) ? 'Enrollment Enabled' : 'Enrollment Disabled';
			$statusCode  = 200;
		}
		
		else {
				$data['message'] = "Unable to update enrollment status";
				$statusCode  = 500;
		}

			$data['quiz_id'] = $quiz_id;
			$data['value'] = $dataPayload['enrollment'];

			return View::responseJson($data, $statusCode);

	}




	public function studentQuizInitiate()
	{


		
		if(!jwACL::isLoggedIn()) 
		{
			return $this->uaReponse();
		}



		



			
		


		$enroll_id = $this->getID();

		if(!isset($_POST['enroll_id']) || !is_numeric($_POST['enroll_id']))
		{
			$enroll_id = $_POST['enroll_id'];
			$data['message'] = "Required Enrollment id is missing";
			$statusCode = 400;
			return View::responseJson($data, $statusCode);
		}

		else {
			$enroll_id = $_POST['enroll_id'];
		}

		$allowedRoles = [4];

    	if( JwtAuth::validateToken() && in_array((int) JwtAuth::$user['role_id'], $allowedRoles) )
		{

			$enrollmentModule = $this->load('module', 'enroll');

			$activityModule = $this->load('module', 'activity');

			$attemptModule = $this->load('module', 'attempt');

			$enrollment = $enrollmentModule->registerAttempt($enroll_id);

			$dls = $enrollmentModule->quizDLSByEnrollmentId($enroll_id);


			if($attempt_id = $attemptModule->initiateQuiz($enroll_id))
			{
				
				// mark entry in the enrollment for attempt


				// start stamp the activity to a json file

				/*
				i need the entity id to get to that file
				*/

				

				// end stamp activity to a a json file

				$enrollmentModule->toggleRetake($enroll_id, 0);
				$data['message'] = "Quiz Initiated";
				$data['attempt_id'] = $attempt_id;
				
				$data['usedXTimes'] = $attemptModule->getXTimesUsed($attempt_id);


				$attemptModule->toggleActive($attempt_id, "1");

				$activityModule = $activityModule->startNewActivity($attempt_id);


				$activityModule = $this->load('module', 'activity');

				$entity_id = $activityModule->pluckEntityIDFromAttempt_id($attempt_id);

				$FileName = ABSPATH."pooling/activities/activity_"."{$entity_id}".".json";

        		$dataFilePath = $FileName;

        		$data_source_file = fopen($dataFilePath, "w");

        		$dataContents['message'] = "new quiz has been initiated";

        		fwrite($data_source_file, json_encode($dataContents));

				fclose($data_source_file);


				if($data['usedXTimes'] > 0)
				{
				
					$data['warning_message'] = "Very Smart!, Duration of this attempt has expired";

				}


				$data['type'] = ($dls == 1) ? 'dls' : 'static';
				$statusCode = 200;
			}

			else {

				$data['message'] = "Quiz Failed to initialize";
				$data['debug'] = $attemptModule->DB;
				$statusCode = 500;

			}

		}
		else 
		{

			$data['message'] = "Authenitcated Students are authorized to initiate quiz";
			$statusCode = 401;

		}

		return View::responseJson($data, $statusCode);

	}


	public function studentQuizData()
	{


				
		if(!jwACL::isLoggedIn()) 
		{
			return $this->uaReponse();
		}



		$allowedRoles = [4];
    	if( JwtAuth::validateToken() && !in_array((int) JwtAuth::$user['role_id'], $allowedRoles) )
		{

			return $this->accessDenied();

		}


		$quiz_id = (int) Route::$params['quiz_id'];

		$attempt_id = (int) Route::$params['attempt_id'];


		if($quiz_id == 0 || $attempt_id == 0) 
		{
			return $this->nonIntegorResponse();
		}




		$attemptModule = $this->load('module', 'attempt');

		$usageXTimes = $attemptModule->getXTimesUsed($attempt_id);


		
		

		if($usageXTimes === false)
		{


			$erroMessage['message'] = "Invalid Attempt";
			$erroMessage['status'] = false;
			$erroMessage['usageXTimes'] = $usageXTimes;
			$erroMessage['action'] = 'redirect';
			return View::responseJson($erroMessage, 200);
		
		}

		else if ($usageXTimes != 0)
		{

			$erroMessage['message'] = "Attempt cannot be instantiated Twice";
			$erroMessage['status'] = false;
			$erroMessage['usageXTimes'] = $usageXTimes;
			$erroMessage['action'] = 'redirect';
			return View::responseJson($erroMessage, 200);
			
		}

		

		


		$attemptModule->incrementUsageXTimes($attempt_id);

		$quizQuestionModule = $this->load('module', 'quizQuestions');


		if($quiz = $this->module->getQuizInfo($quiz_id))
		{


			
			$data['quiz'] = $quiz;

			$data['imagesPreload'] = array();

			$requiredQuestions = $quiz[0]['noques'];

			if($questions = $quizQuestionModule->listQuizPlayQuestionsDistro($quiz_id))
			{
				
				// inject media to each question if available
				for($i=0; $i<sizeof($questions); $i++)
				{

					
					$question_id = $questions[$i]['questionId'];

					
					if($this->stringIsAbsoluteImagePath($questions[$i]['optionA']))
					{

						$data['imagesPreload'][] = $questions[$i]['optionA'];

					}

					

					if($this->stringIsAbsoluteImagePath($questions[$i]['optionB']))
					{
						
						$data['imagesPreload'][] = $questions[$i]['optionB'];
					}

					if($this->stringIsAbsoluteImagePath($questions[$i]['optionC']))
					{
						$data['imagesPreload'][] = $questions[$i]['optionC'];

					}

					if($this->stringIsAbsoluteImagePath($questions[$i]['optionD']))
					{
						
						$data['imagesPreload'][] = $questions[$i]['optionD'];

					}

					


					if($media = $quizQuestionModule->getQuestionMedia($question_id))
					{					
						
						$questions[$i]['media'] = $media;


						foreach ($media as $key => $imgObject) {

							// array_push($data['imagesPreload'], $imgObject['filepathurl']);

							$data['imagesPreload'][] = $imgObject['filepathurl'];
							
						}


						


					}

				}

				// $data['questions'] = $this->encodeData($questions);

				$data['imagesPreload'] = array_values(array_unique($data['imagesPreload']));

				$data['questions'] = $questions;	

				// $attemptModule->toggleActive($attempt_id, "1");

				$data['usageXTimes'] = $attemptModule->getXTimesUsed($attempt_id);

				$data['count'] = sizeof($questions);


				
				if($logo = $this->module->getQuizEntityLogo($quiz_id))
				{
					$data['entityLogo'] = $logo;	
				}

				else {

					$data['entityLogo'] = false;

				}

			}



		}


		if($quiz != false)
		{
			$statusCode = 200;
		}

		else {
			$data['message'] = 'question cannot be loaded';
			$statusCode = 500;
		}

		return View::responseJson($data, $statusCode);

	}



    public function quizProgress()
    {
    	
    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		if(!jwACL::has('quiz-progress')) 
			return $this->accessDenied();


		$quiz_id = $this->getID();



		if($quiz_id === 0)
			return $this->nonIntegorResponse();	




        if($progress = $this->module->quizProgress($quiz_id))
        {
        	$data['attempted'] = $progress;
        	$data['status'] = true;
        	$statusCode = 200;
        }
        else {

        	$data['message'] = "No attempts found for this quiz yet";
        	$data['status'] = false;
        	$statusCode = 500;

        }
        return View::responseJson($data, $statusCode);

    }



    public function prepareDls()
    {

    		
    	if(!jwACL::isLoggedIn()) 
    	{
    		return $this->uaReponse();
    	}
    	

		
		$quiz_id = (int) Route::$params['quiz_id'];
		$attempt_id = (int) Route::$params['attempt_id'];


		$allowedRoles = [4];
    	if( JwtAuth::validateToken() && !in_array((int) JwtAuth::$user['role_id'], $allowedRoles) )
		{

			return $this->accessDenied();

		}



		if($quiz_id == 0 || $attempt_id == 0) 
		{
			return $this->nonIntegorResponse();
		}




		$attemptModule = $this->load('module', 'attempt');
		$usageXTimes = $attemptModule->getXTimesUsed($attempt_id);


		


		if($usageXTimes === false)
		{

			$erroMessage['message'] = "Invalid Attempt";
			$erroMessage['status'] = false;
			$data['usageXTimes'] = $usageXTimes;
			$erroMessage['action'] = 'redirect';
			return View::responseJson($erroMessage, 200);	

		}

		else if ($usageXTimes != 0)
		{

			$erroMessage['message'] = "Attempt cannot be instantiated Twice";
			$erroMessage['status'] = false;
			$erroMessage['usageXTimes'] = $usageXTimes;
			$erroMessage['action'] = 'redirect';
			return View::responseJson($erroMessage, 200);
			
		}
	

		
		$attemptModule->incrementUsageXTimes($attempt_id);

		$quizQuestionModule = $this->load('module', 'quizQuestions');

		$studentId = $this->jwtUserId();

		
		if($quiz = $this->module->getQuizInfo($quiz_id))
		{

			$statusCode = 200;
			$data['quiz'] = $quiz[0];	

			//$data['stream'] = $this->encodeData($quizQuestionModule->listQuizPlayQuestionsDLS($quiz_id, $studentId));



			$data['stream'] = $quizQuestionModule->listQuizPlayQuestionsDLS($quiz_id, $studentId);
			$data['usageXTimes'] = $attemptModule->getXTimesUsed($attempt_id);

			$data['action'] = 'play';


			$data['imagesPreload'] = array_values($data['stream']['imagesPreload']);

			unset($data['stream']['imagesPreload']);

			$data['entityLogo'] = $this->module->getQuizEntityLogo($quiz_id);

		}
		
		else {

			$statusCode = 500;
			$data['message'] = "Quiz info is not available";

		}	



		return View::responseJson($data, $statusCode);

    }



    public function saveWithWizard()
    {

    	/*

    	JSON BODY
    		{
		
		"title": "Pass Validation",
		"category_id": "54",
		"cleanDesp": ["79"],
		"cleanSubDesp": ["82", "81", "80"],
		
		"duration": "30",

		"startDateTime": "2019-07-20 20:19",
		"endDateTime": "2019-07-31 20:19",
		"maxAllocation": "3",

		"maxScore": "100",
		"minScore": "70",
		"noques": "10",
		
		
		"threshold": "500",

		"uniqueOnRetake": "1",
		"showGPA": "1",
		"showGrading": "1",
		"showResult": "1",
		"showScore": "1",
		"dls": "0"

		}

    	*/


    	/*

    "title": "this must fail",
    "category_id": "54",
    "cleanDesp": ["79"],
    "cleanSubDesp": ["80","81","82"],
    "duration": "30",
    "startDateTime": "2019-07-21 16:48",
    "endDateTime": "2019-07-31 16:48",

    "maxScore": "100",
    "minScore": "70",

    "maxAllocation": "4"
    
    "noques": "100",
    "threshold": 500,
    "dls": 0,
    "uniqueOnRetake": "1",
    "showScore": 0,
    "showResult": "1",
    "showGrading": "1",
    "showGPA": "1",   

    	*/

    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		if(!jwACL::has('quiz-add')) 
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
			
			'code' => 'required',
			'title' => 'required',
			'category_id' => 'required|integer',
			'minScore' => 'required|integer',
			'maxScore' => 'required|integer',
			'startDateTime' => 'required',
			'endDateTime' => 'required',
			'noques' => 'required|integer',
			'duration' => 'required|integer',
			'threshold' => 'required|integer',
			'maxAllocation' => 'required|integer',	
			'venue' => 'required',
			
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








		$keys = array(
			'title', 'category_id', 'minScore', 'maxScore', 'startDateTime', 'endDateTime', 'noques', 'duration',
			'threshold', 'maxAllocation', 'code', 'venue' 
		);

		foreach ($keys as $key => $value) {


			if(!isset($_POST[$keys[$key]]))
			{

				$data['message'] = $keys[$key] . " is required field";

				$statusCode = 406;

				return View::responseJson($data, $statusCode);

				die();

			}
			
		}


		$optionalKeys = ['dls', 'uniqueOnRetake', 'showScore', 'showResult', 'showGrading', 'showGPA'];


		foreach ($optionalKeys as $key => $value) {

			if( isset($_POST[$optionalKeys[$key]]) )
			{

				$keys[] = $optionalKeys[$key];

			}

		}


		
		$dataPayload = $this->module->DB->sanitize($keys);
		$dataPayload['maxAllocation'] = (int) $dataPayload['noques'] * (int) $dataPayload['maxAllocation'];


		if($dataPayload['maxAllocation'] == 0)
		{



			$data['message'] = "Max Allocated value not provided";

			return View::responseJson($data, 406);


		}
		


		$dataPayload['status'] = 1;
		$dataPayload['enrollment'] = 0;
		$dataPayload['user_id'] = $this->jwtUserId();

		$decipline = $_POST['cleanDesp'];
		$subDecipline = $_POST['cleanSubDesp'];



		if($res = $this->module->addQuiz($dataPayload))
		{
			
			
			$statusCode = 200;
			$data['last_id'] = $res;

			$categoryModule = $this->load('module', 'category');
        	$subDescIds = $categoryModule->verifySubDeciplines($decipline, $subDecipline);
        	$quiz_id = $res;
        	// insert sub decipline with ids

        	$subjectModule = $this->load('module', 'subject');
        	
        	if($subjectModule->saveQuizSubjects($quiz_id, $subDescIds))
        	{
        		$data['message'] = "New Quiz added with subjects";
        	}
        	else {
        		$data['message'] = "New Quiz failed to save subjects";	
        	}
        	
		}
		else {

			$data['message'] = "Fail Quiz Cannot be saved";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

		

    }



    public function currentAct()
    {

    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(!jwACL::has('activity-monitor')) 
			return $this->accessDenied();	





    	$attemptModule = $this->load('module', 'attempt');

    	$authenticatedRole = jwACL::authRole();
    	

    	if($authenticatedRole == 'proctor')
    	{
    		$entity_id = JwtAuth::$user['created_by'];
    	} 

    	else if($authenticatedRole == 'entity') {

    		$entity_id = jwACL::authUserId();

    	}



    	if($activity = $attemptModule->activeMonitoring($entity_id))
    	{
    		$data['actvity'] = $activity;
    		$statusCode = 200;   			
    	}

    	else {

			$statusCode = 204;    		
    		$data['message'] = "No Current Activities";
    	}



    	return View::responseJson($data, $statusCode);

    }

    public function pollingonfinish()
    {

    	
    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();




    	set_time_limit(0);

    	$user_id = $this->jwtUserId();

    	$dataFilePath = ABSPATH."pooling/stdquiz/candidate_{$user_id}.json";


    	if(!file_exists($dataFilePath))
		{

			$handle = fopen($dataFilePath, 'w');
			fclose($handle);

		}


		while(true)
		{

			$last_ajax_call = ( isset($_GET['timestamp']) && $_GET['timestamp'] != 0 ) ? (int)$_GET['timestamp'] : null;

			clearstatcache();

			$last_change_in_data_file = filemtime($dataFilePath);

			if ($last_ajax_call == null || $last_change_in_data_file > $last_ajax_call) 
			{


				// fetch the latest record and then send it back	

				

				///this->studentQuizData($last_change_in_data_file);

				
				return $this->studentQuizListHandler($last_change_in_data_file);

				View::responseJson($data, 200);
				
				break;

			}
			else {

				sleep(1);

				continue;

			}


		}



    }


    public function updateDateTime()
    {

    	$_POST = Route::$_PUT;


    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


		if(!jwACL::has('quiz-modify-datetime')) 
			return $this->accessDenied();


		$id = $this->getID();

		$startDateTime = (isset($_POST['startDateTime'])) ? $_POST['startDateTime'] : NULL;

		$endDateTime = (isset($_POST['endDateTime'])) ? $_POST['endDateTime'] : NULL;



		if($startDateTime == NULL || $endDateTime == NULL)
		{

			return $this->responseNullDatetime();

		}


		$dataPayload = array(

			'startDateTime' => $startDateTime,
			'endDateTime' => $endDateTime

		);



		if(!$this->validateDateTime24hrs($startDateTime, 'Y-m-d H:i:s') && !$this->validateDateTime24hrs($startDateTime))
		{
			
			return $this->responseInvalidDatetime();

		}


		if(!$this->validateDateTime24hrs($endDateTime, 'Y-m-d H:i:s') && !$this->validateDateTime24hrs($endDateTime))
		{
			
			return $this->responseInvalidDatetime();

		}




		if($this->module->update($dataPayload, $id))
		{

			$data['message'] = "Quiz datetime updated successfully";
			$statusCode = 200;

		}

		else {

			$data['message'] = "Operation failed whilte updating datetime";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

    }



    public function destroy()
    {


    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		
		if(!jwACL::has('quiz-delete') && !jwACL::isAdmin()) 
			return $this->accessDenied();

    	
    	$id = $this->getID();


    	$userID = jwACL::authUserId();

		if(!$this->module->isOwnedByUser($id, $userID) && !jwACL::isAdmin())
			return $this->ownerDisqualifyResponse();

		
    	if($this->module->deleteQuiz($id))
    	{

    		$data['message'] = "removed successfully";
			$statusCode = 200;

    	}

    	else {

    		$data['message'] = "Failed while removing Quiz";
			$statusCode = 500;

    	}


    	return View::responseJson($data, $statusCode);

    }



    public function optionsToggle()
    {


    	$PUT = Route::$_PUT;


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


		if(!jwACL::has('quiz-edit')) 
			return $this->accessDenied();



    	$id = $this->getID();



    	$data['keys'] = array_keys($PUT);

    	/*
    	typeKey
    	statusValue
    	*/


    	$PUT['typeKey'];

    	$PUT['statusValue'];


    	$dataPayload = array(

    		$PUT['typeKey'] => $PUT['statusValue']

    	);


    	if($PUT['typeKey'] == 'dls' && $PUT['statusValue'] == 1)
    	{
			
			return $this->dlsToggleHandler($dataPayload, $id);

    	}


    	if($this->module->optionToggleHandler($dataPayload, $id))
    	{
    		$reponse['message'] = 'updated';	
    		$statusCode = 200;
    	}

    	else {

    		$reponse['message'] = 'Fail while trying to update';	
    		$statusCode = 500;	

    	}

    	return View::responseJson($reponse, $statusCode);

    }


    public function isdlsQualified()
    {



    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



        $quiz_id = $this->getID();
        $quizQuestionModule = $this->load('module', 'quizQuestions');



        if(!$allocatedSummary = $quizQuestionModule->dlsQuizQueAllocatedSummary($quiz_id))
        {

        	$data['message'] = 'Allocated distribution data not found for quiz';
        	$statusCode = 500;
        	$data['status'] = false;
            return View::responseJson($data, $statusCode);

        }



        $output = $quizQuestionModule->dlsQualificationCheck($allocatedSummary);


        if($output === true)
        {
            $data['message'] = 'DLS Qualified';
        	$statusCode = 200;
        	$data['status'] = true;
            return View::responseJson($data, $statusCode);
        }


        if(is_array($output))
        {

        	$data['message'] = 'Quiz is not Eligible for DLS';
        	$data['detailError'] = $output;
        	$statusCode = 406;
        	$data['status'] = true;
            return View::responseJson($data, $statusCode);
        }

        	$data['message'] = 'Un Expected Error';
        	$statusCode = 500;
        	$data['status'] = true;
            return View::responseJson($data, $statusCode);


    }




    public function dlsToggleHandler($dataPayload, $quiz_id)
    {

	   	$quizQuestionModule = $this->load('module', 'quizQuestions');
    	if($quizQuestionModule->isDlsQualifiedNitroMode($quiz_id))
    	{

    		if($this->module->optionToggleHandler($dataPayload, $quiz_id))
    		{	
    		
    			$data['message'] = 'DLS : Enabled';	
    			$statusCode = 200;
    		}	

    		else {
    			$data['message'] = 'Failed while updating dls status';	
    			$statusCode = 500;
    		}

    	}

    	else {

    		$data['message'] = 'Quiz is not elibile please match allocation and distribution';
    		$statusCode = 406;

    	}
    	
    	return View::responseJson($data, $statusCode);

    }



    public function invitationQuizListHandler()
	{

			if(!jwACL::isLoggedIn()) 
				return $this->uaReponse();


			$enroll_id = $this->getID();

			
			$candidate_id = $this->jwtUserId();


			$invitationModule = $this->load('module', 'invitations');


			if(!$invitationModule->validateInvitation($enroll_id, $candidate_id))
			{
				$data['message'] = "Invalid invitation request";
				$statusCode = 406;
				return View::responseJson($data, $statusCode);

			}

			
			if($attempted = $this->module->getAttemptedInvitedQuizList($candidate_id, $enroll_id))
			{
					$data['attempted'] = $attempted;
			}
			else {
					$data['attempted'] = 0;
			}
			
			if($quiz = $this->module->getPendingInvitedQuiList($candidate_id, $enroll_id))
			{
				$data['quiz'] = $quiz;
				$data['status'] = true;
				$statusCode = 200;			
			}

			else {
				$data['quiz'] = 0;
				$statusCode = 200;
				
			}

			
			return View::responseJson($data, $statusCode);

		}


		public function quizDirectAccessHandler()
		{


			if(!jwACL::isLoggedIn()) 
				return $this->uaReponse();

			$quiz_id = $this->getID();

			$user_id = $this->jwtUserId();

			/*
			1. check if auth role type is candidate
			2. check if quiz enroll is enabled
			2. check if that is enrolled
			3. enroll if not already enrollledd
			4. get enroll_id
			5. get list of quiz
			*/


			$quizModule = $this->load('module', 'quiz');

			$enrollModule = $this->load('module', 'enroll');


			if(JwtAuth::$user['role_id'] != 4)
			{
				$data['message'] = "User type is not allowed to access this route";
				$statusCode = 406;
				return View::responseJson($data, $statusCode);
			}


			if(!$quizModule->quizEnrollmentEnabled($quiz_id))
			{


				$data['message'] = "Quiz enrollment is disabled cannot continue";
				$statusCode = 406;
				return View::responseJson($data, $statusCode);	

			}


			if(!$enroll_id = $enrollModule->getEnrollviaCandidateId($user_id, $quiz_id))
			{

				
				$enroll_id = $enrollModule->enrolltoQuiz($user_id, $quiz_id);

			}




			if($attempted = $quizModule->getAttemptedInvitedQuizList($user_id, $enroll_id))
			{
					$data['attempted'] = $attempted;
			}
			else {
					$data['attempted'] = 0;
			}
			
			if($quiz = $quizModule->getPendingInvitedQuiList($user_id, $enroll_id))
			{
				$data['quiz'] = $quiz;
				$data['status'] = true;
				$statusCode = 200;			
			}

			else {
				$data['quiz'] = 0;
				$statusCode = 200;
				
			}
			
			return View::responseJson($data, $statusCode);

		}





		public function canidateSelfProgressDetails()
		{


			if(!jwACL::isLoggedIn()) 
				return $this->uaReponse();	

		
			$attemptId = $this->getID();
			$canidateId = $this->jwtUserId();


			$allowedRoles = [4];
    		if( JwtAuth::validateToken() && in_array((int) JwtAuth::$user['role_id'], $allowedRoles) )
			{




			if($progress = $this->module->candidateSelfProgressReport($attemptId, $canidateId))
			{

				$data['progress'] = $progress;
				$data['status'] = true;
				$statusCode = 200;
			}

			else {
				$data['message'] = 'Cannot fetch candidate progress';
				$data['status'] = false;
				$statusCode = 500;
			}


			return View::responseJson($data, $statusCode);

			}

			else {


				return $this->accessDenied();

			}

			

		}



		public function directUrl()
		{

			
			$alphaID = $this->getParam('alphaID');


			$quizModule = $this->load('module', 'quiz');


			if(!$quizID = $quizModule->pluckIdfromAlphaID($alphaID))
			{
				
				$data['message'] = "Invalid Access Id provided";
				$statusCode  = 406;
				return View::responseJson($data, $statusCode);			

			}


			if(!$quizInfo = $quizModule->extractEntitySlugviaQuizID($quizID))
			{

				$data['message'] = "Invalid Quiz Id";
				$statusCode  = 500;
				return View::responseJson($data, $statusCode);

			}

			$quizInfo = $quizInfo[0];

			if($quizInfo['slug'] == null)
			{

				$data['message'] = "Please set slug in profile";
				$statusCode  = 406;
				$data['slug'] = false;
				return View::responseJson($data, $statusCode);

			}


			$slug = $quizInfo['slug'];

			$actionToken = array(
				'action' => 'directQuiz',
				'entitySlug'=> $slug,
				'quiz_id'=> $quizID

			);

			$encodedToken = urlencode(base64_encode(json_encode($actionToken)));

			$data['urltoken'] = $encodedToken;
			$data['slug'] = $slug;

			return View::responseJson($data, 200);


		}


	public function generateAlphaID()
    {

        $quizId = $this->getID();

        $urlShortnerModule = $this->load('module', 'urlshortner');

        $quizModule = $this->load('module', 'quiz');      

        $inputID = (int) $quizId . time(); 

        $isShortCodeDuplicate = true;

        $generatedCode = null;

        $oldAlphaExists = false;


        if(!$quizModule->pluckQuizID($quizId))
        {


            $data['message'] = "Invalid quiz id";
            $statusCode = 500;
            return View::responseJson($data, $statusCode);
            die();


        }


        if($oldAlpha = $quizModule->pluckAlphaIDviaID($quizId))
        {

             $oldAlphaExists = true;   

             $data['alphaID'] = $oldAlpha;
             $data['type'] = 'existing';
             $statusCode = 200;
             return View::responseJson($data, $statusCode);

             die();

        }


       while($isShortCodeDuplicate & !$oldAlphaExists)
       {
            $generatedCode  = $urlShortnerModule->convertIntToShortCode($inputID);
            if(!$quizModule->alphaIDBinaryCheck($generatedCode))
            {                   

                    $payload = array(
                    'alphaID' => $generatedCode
                    );
                $quizModule->update($payload, $quizId);
                $isShortCodeDuplicate = false;
                   
            }

        }

            $data['alphaID'] = $generatedCode;
            $data['type'] = 'generated';
            $statusCode = 200;
            return View::responseJson($data, $statusCode);

    }


    public function demoQuizplay()
    {



		$quiz_id = 197;

		/*
		pick from random array
		*/


		$quizQuestionModule = $this->load('module', 'quizQuestions');


		if($quiz = $this->module->getQuizInfo($quiz_id))
		{


			
			$data['quiz'] = $quiz;

			$data['imagesPreload'] = array();

			$requiredQuestions = $quiz[0]['noques'];

			if($questions = $quizQuestionModule->listQuizPlayQuestionsDistro($quiz_id))
			{
				
				// inject media to each question if available
				for($i=0; $i<sizeof($questions); $i++)
				{

					
					$question_id = $questions[$i]['questionId'];

					
					if($this->stringIsAbsoluteImagePath($questions[$i]['optionA']))
					{

						$data['imagesPreload'][] = $questions[$i]['optionA'];

					}

					
					if($this->stringIsAbsoluteImagePath($questions[$i]['optionB']))
					{
						
						$data['imagesPreload'][] = $questions[$i]['optionB'];
					}

					if($this->stringIsAbsoluteImagePath($questions[$i]['optionC']))
					{
						$data['imagesPreload'][] = $questions[$i]['optionC'];

					}

					if($this->stringIsAbsoluteImagePath($questions[$i]['optionD']))
					{
						
						$data['imagesPreload'][] = $questions[$i]['optionD'];

					}

					


					if($media = $quizQuestionModule->getQuestionMedia($question_id))
					{					
						
						$questions[$i]['media'] = $media;


						foreach ($media as $key => $imgObject) {

							

							$data['imagesPreload'][] = $imgObject['filepathurl'];
							
						}


						


					}

				}

				

				$data['imagesPreload'] = array_values(array_unique($data['imagesPreload']));

				$data['questions'] = $questions;	


				$data['usageXTimes'] = 1;

				$data['count'] = sizeof($questions);


				
				if($logo = $this->module->getQuizEntityLogo($quiz_id))
				{
					$data['entityLogo'] = $logo;	
				}

				else {

					$data['entityLogo'] = false;

				}

			}



		}


		if($quiz != false)
		{
			$statusCode = 200;
		}

		else {
			$data['message'] = 'question cannot be loaded';
			$statusCode = 500;
		}

		return View::responseJson($data, $statusCode);

    }


}

