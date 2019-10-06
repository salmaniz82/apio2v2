<?php class quizCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'quiz');
	}


	public function index()
	{	
		/*
			list quiz list for all users
			admin will see all list
			teacher will see his own
			student will see the courses he is enrolled in
		*/

		$allowedRoles = [1,2];
    	if( JwtAuth::validateToken() && in_array((int) JwtAuth::$user['role_id'], $allowedRoles) )
		{

			$user_id = $this->jwtUserId();
			$role_id = $this->jwtRoleId();

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

		$quizID = $this->getID();


		if($quiz = $this->module->getQuizById($quizID))
		{
			$data['quiz'] = $quiz;
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
		

		if(!jwACL::has('add-quiz')) 
			return $this->accessDenied();
		

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

			return $this->uaReponse();
			
		}



	}



	public function teacherQuizListHandler($user_id, $role_id)
	{

		// fetch teacher quiz list




	}


	public function checkValidityCount()
	{


		$quiz_id = $this->getID();


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

		$quiz_id = $this->getID();
		$_POST = Route::$_PUT;

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
		
		
		$quiz_id = $this->getID();
		$_POST = Route::$_PUT;


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

		$quiz_id = (int) Route::$params['quiz_id'];

		$attempt_id = (int) Route::$params['attempt_id'];

		$attemptModule = $this->load('module', 'attempt');

		$attemptModule->incrementUsageXTimes($attempt_id);
	
		$quizQuestionModule = $this->load('module', 'quizQuestions');


		if($quiz = $this->module->getQuizInfo($quiz_id))
		{
			
			$data['quiz'] = $quiz;
			$requiredQuestions = $quiz[0]['noques'];

			if($questions = $quizQuestionModule->listQuizPlayQuestionsDistro($quiz_id))
			{
				
				// inject media to each question if available
				for($i=0; $i<sizeof($questions); $i++)
				{


					$question_id = $questions[$i]['questionId'];

		
					
					if($media = $quizQuestionModule->getQuestionMedia($question_id))
					{					
						
						$questions[$i]['media'] = $media;
					}

				}

				$data['questions'] = $this->encodeData($questions);
			

				// $attemptModule->toggleActive($attempt_id, "1");

				$data['usageXTimes'] = $attemptModule->getXTimesUsed($attempt_id);


				


			}

		}


		if($questions != false AND $quiz != false)
		{
			$statusCode = 200;
		}

		else {
			$statusCode = 500;
		}

		return View::responseJson($data, $statusCode);

	}



    public function quizProgress()
    {
    	/*
    	teacher view of progress
    	*/
        $quiz_id = $this->getID();


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

  	
    	$quiz_id = (int) Route::$params['quiz_id'];

		$attempt_id = (int) Route::$params['attempt_id'];

		$quizQuestionModule = $this->load('module', 'quizQuestions');

		// $studentId = $this->jwtUserId();

		$studentId = 68;




		if($quiz = $this->module->getQuizInfo($quiz_id))
		{

			$statusCode = 200;
			$data['quiz'] = $quiz[0];	
			//$data['stream'] = $this->encodeData($quizQuestionModule->listQuizPlayQuestionsDLS($quiz_id, $studentId));

			$data['stream'] = $quizQuestionModule->listQuizPlayQuestionsDLS($quiz_id, $studentId);


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


		$keys = array(
			'title', 'category_id', 'minScore', 'maxScore', 'startDateTime', 'endDateTime', 'noques', 'duration',
			'threshold', 'maxAllocation'
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


    	$attemptModule = $this->load('module', 'attempt');
    	$entity_id = jwACL::authUserId();

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


		/*
		no permission in db
		if(!jwACL::has('quiz-update')) 
			return $this->accessDenied();
		*/


		$id = $this->getID();

		$startDateTime = $_POST['startDateTime'];
		$endDateTime = $_POST['endDateTime'];



		$dataPayload = array(

			'startDateTime' => $startDateTime,
			'endDateTime' => $endDateTime

		);


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

		/*
		no permission in db
		if(!jwACL::has('quiz-delete')) 
			return $this->accessDenied();
		*/

    	
    	$id = $this->getID();

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


    	if($this->module->optionToggleHandler($dataPayload, $id))
    	{
    		$data['message'] = 'working on';	
    	}

    	


    	return View::responseJson($data, 200);


    }



}