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

			if($role_id == 1)
			{
				// list for admin
				return $this->adminQuizListHandler();
			}

			else if ($role_id == 2)
			{
				
				// list for entity / teacher
				return $this->teacherQuizListHandler($user_id, $role_id);

			}

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


	public function studentQuizListHandler()
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



			return View::responseJson($data, $statusCode);


		}

		else {

			return $this->uaReponse();
			
		}



	}



	public function teacherQuizListHandler($user_id, $role_id)
	{




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


	public function enrollmentToggleHandler($dataPayload, $quiz_id)
	{


		if($this->module->udpate($dataPayload, $quiz_id))
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
			$enrollment = $enrollmentModule->registerAttempt($enroll_id);
			
			$attemptModule = $this->load('module', 'attempt');

			if($attempt_id = $attemptModule->initiateQuiz($enroll_id))
			{
				// mark entry in the enrollment for attempt

				$enrollmentModule->toggleRetake($enroll_id, 0);
				$data['message'] = "Quiz Initiated";
				$data['attempt_id'] = $attempt_id;
				$statusCode = 200;

			}

			else {

				$data['message'] = "Quiz Failed to initialize";
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

		$quizQuestionModule = $this->load('module', 'quizQuestions');


		if($quiz = $this->module->getQuizInfo($quiz_id))
		{
			
			$data['quiz'] = $quiz;
			$requiredQuestions = $quiz[0]['noques'];

			if($questions = $quizQuestionModule->listQuizPlayQuestionsDistro($quiz_id))
			{
				
				$data['questions'] = $questions;

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


        	$data['status'] = false;
        	$statusCode = 500;

        }
        return View::responseJson($data, $statusCode);

    }






}