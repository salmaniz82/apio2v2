<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');

class enrollmentCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'enroll');
	}


	public function generateInviteToken($payload)
	{

		return urlencode(base64_encode(json_encode($payload)));
	}


	
	public function listEnrolledStudents()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		if(!jwACL::has('quiz-enroll-list')) 
			return $this->accessDenied();

		


		$quizId = $this->getID();

		if($enroll = $this->module->enrolledStudents($quizId))
		{
			$data['enroll'] = $enroll;
			$data['status'] = true;
			$statusCode = 200;
		}

		else {

			$data['message'] = "NO Students is enrolled to this quiz yet";
			$data['status'] = false;
			$statusCode = 404;
		}


		return View::responseJson($data, $statusCode);


	}



	public function saveEnrollment()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();




		if(!jwACL::has('quiz-enroll-add')) 
			return $this->accessDenied();




		$entity_id = jwACL::authUserId();

		$sendInvite = (isset($_POST['sendInviteEmail']) && $_POST['sendInviteEmail'] == true) ? true : false;



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
			
			'email' => 'required|valid_email',
			'quiz_id'    =>  'required|integer'
			
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





		$email = $_POST['email'];

		$quiz_id = $_POST['quiz_id'];


		$profileModule = $this->load('module', 'profile');

		$profile = $profileModule->getProfileByUserId($entity_id);

		if($sendInvite)
		{

			$inviteModule = $this->load('module', 'invitations');

			 if(!$profile)
			 {



			 	$data['message'] = "Cannot continue without profile information";
				$statusCode = 406;
				return View::responseJson($data, $statusCode);
			 }

			 else if($profile[0]['companyTitle'] == null)  
			{

			$data['message'] = "Profile set organization name";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			}

			else if($profile[0]['logo'] == null)
			{
			$data['message'] = "Profile set logo image";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			}

			else if($profile[0]['slug'] == null || $profile[0]['slug'] == "")
			{
			$data['message'] = "Slug is not defined in profile settings";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			}

			else {
			$slug = $profile[0]['slug'];
			}

		}

		$quizModule = $this->load('module', 'quiz');

		if(1 != $quizModule->quizEnrollmentEnabled($quiz_id))
		{	
			// break the flow
			$data['message'] = "Quiz Enrollment is disabled";
			$statusCode = 406;
			$data['status'] = false;
			return View::responseJson($data, $statusCode);
			die();
		}

		if(isset($_POST['dtsScheduled'])) 
		{
			$dateScheduled = $_POST['dtsScheduled'];
		}
		else {
			$dateScheduled = $this->Dt_24();
		}

					
		if(!$quizModule->validateEnrollmentRange($quiz_id, $dateScheduled))
		{
			$data['message'] = "Enrollment out of range from Quiz startDatetime | endDateTime ";
			$statusCode = 406;
			$data['status'] = false;
			return View::responseJson($data, $statusCode);
			die();
		}


		$userModule = $this->load('module', 'user');

		if($user_id = $userModule->pluckIdByEmail($email))
		{


			$userDetails = $userModule->userById($user_id);
			$userDetails = $userDetails[0];

			if($userDetails['role'] != 'students' && $userDetails['role'] != 'candidate')
			{
				
				$data['message'] = "User is out of scope for enrollment";
				$data['role'] = $userDetails['role'];
				$statusCode = 406;
				return View::responseJson($data, $statusCode);

			}


			// duplicate check

			if(!$this->module->duplicateCheck($user_id, $quiz_id))
			{
				if($last_id = $this->module->enrolltoQuiz($user_id, $quiz_id))
				{


					$taggedUserModule  = $this->load('module', 'taggedusers');

					$taggedUserModule->linkusertoentity($last_id, $entity_id);

					
					if($sendInvite)
					{

						$data['inviteStatus'] = "invite activated";

						/*
						send invitiation
						1. add invite record
						2. 
						*/

						$invitePayload = array(
							'enroll_id'=> $last_id,
							'entity_id'=> $entity_id
						);
						
						if($inviteId = $inviteModule->saveInvitation($invitePayload))
						{

							$uriPayload = array(
							'action' => 'quizInvitation',
							'entitySlug' => $slug,
							'enroll_id' => $last_id,
							'entity_id' => $entity_id,
							'invite_id' => $inviteId
						);


						$uriToken = $this->generateInviteToken($uriPayload);

						if(!$inviteModule->updateRecordwithToken(array('uriToken'=> $uriToken), $inviteId))
						{

							$data['message'] = 'Invitation cannot be send failed updating token';
							$statusCode = 500;
							return View::responseJson($data, $statusCode);
						}

							return $this->TriggerInvitationEmail($inviteId);

						}

					}

					else {
						$data['inviteStatus'] = "Not activated";
					}

					if($lastItem = $this->module->returnLastById($last_id))
					{
						$data['lastEnroll'] = $lastItem;
						$data['message'] = "Enrollment Successfull";
						$statusCode = 201;
					}

					else {
						$data['message'] = "Enrollment Successfull but to load last Record";
						$statusCode = 200;
					}

					$data['status'] = true;

				}
				else {

					$data['message'] = "Error While Enrollment";
					$data['user_id'] = $user_id;
					$statusCode = 500;
					$data['status'] = false;

				}	
			}

			else {

				$data['message'] = "Enrollment Already Exists";
				$statusCode = 400;
				$data['status'] = false;

			}

			


		}
		else 
		{
			$data['message'] = "User with this email does not exists";
			$statusCode = 404;
			$data['status'] = false;
		}


		return View::responseJson($data, $statusCode);

	}



	public function toggleRetake()
	{



		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(!jwACL::has('quiz-retake-toggle')) 
			return $this->accessDenied();




		$enroll_id = $this->getID();
		$_POST = Route::$_PUT;
		$retake  = (int) $_POST['retake'];

		if($this->module->toggleRetake($enroll_id, $retake))
		{

			$data['message'] = ($retake == 1) ? "Retake is Now Enabled" : "Retake is Disabled";
			$data['value'] = $retake;
			$statusCode = 200;

			$filePayloadContents = array(

				"message"=> "user Updated With REtake toggle"


			);

				if($candidateId = $this->module->pluckStudentByEnrollmentId($enroll_id))
				{

					// ping activity file	
	        	$FileName = ABSPATH."pooling/stdquiz/candidate_"."{$candidateId}".".json";
    	    	$dataFilePath = $FileName;
        		$data_source_file = fopen($dataFilePath, "w");
        		fwrite($data_source_file, json_encode($filePayloadContents));
				fclose($data_source_file);

				$data['candidateId'] = $candidateId;

				}
				


				

		}

		else {

			$data['message'] = "Retake cannot be updated on server";
			$data['db'] = $this->module->DB;
			$data['value'] = $retake;
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}




	public function udpateScheduleDatetime()
	{



		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		if(!jwACL::has('enroll-schedule')) 
			return $this->accessDenied();


		$enroll_id = $this->getID();

		$quiz_id = (int) $this->getParam('quiz_id');


		if($enroll_id == 0 || $quiz_id == 0)	
		{

			return $this->nonIntegorResponse();

		}


		$_POST = Route::$_PUT;



		$dateScheduled  = (isset($_POST['dtsScheduled'])) ? $_POST['dtsScheduled'] : NULL;


		if($dateScheduled == NULL)
		{

			return $this->responseNullDatetime();	

		}



		if(!$this->validateDateTime24hrs($dateScheduled))
		{
			return $this->responseInvalidDatetime();
		}






		$quizModule = $this->load('module', 'quiz');


		if(!$quizModule->validateEnrollmentRange($quiz_id, $dateScheduled))
		{

			$data['message'] = "Enrollment and Scheduled dates must be in range of Quiz startDatetime | endDateTime ";
			$statusCode = 406;
			$data['status'] = false;

			return View::responseJson($data, $statusCode);
			die();
		}

		$dataPayload['dtsScheduled'] = $dateScheduled;


		if($this->module->udpateScheduleDateTime($dataPayload, $enroll_id))
		{
			$data['message'] = "Enrollment Scheduled with Success";
			$statusCode = 200;
		}

		else {

			$data['message'] = "Enrollment failed make sure provided datetime is not the same";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}



	public function removeEnrollment()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();




		if(!jwACL::has('quiz-enroll-delete')) 
			return $this->accessDenied();

		


		$id = $this->getID();

		if($this->module->delete($id))
		{
			$data['message'] = "Enrollment removed Successfully";
			$statusCode = 200;
			
		}

		else {

			$data['message'] = "Failed while removing enrollment";
			$statusCode = 500;

		}


		return View::responseJson($data, $statusCode);


	}


	public function resetdefault()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(!jwACL::has('quiz-enroll-reset')) 
			return $this->accessDenied();


		
		$enrollID = $this->getID();

		$enrollResetPayload = array(
			'attempts' => 0,
			'retake'=> 0,
			'invited'=> 0
		);

		$attemptModule = $this->load('module', 'attempt');



		if($this->module->update($enrollResetPayload, $enrollID) && $attemptModule->clearAttemptsOnResetEnroll($enrollID))
		{
			
			$data['message'] = "Enrollment reset to default successfully";
			$statusCode = 200;

		}

		else {

			$data['message'] = "Failed while reseting enrollment";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}



	public function TriggerInvitationEmail($inviteID)
	{
		
		$emailModule = $this->load('module', 'email');

		$inviteModule = $this->load('module', 'invitations');

		$invitation = $inviteModule->invitationQuizDetails($inviteID);

		$invitation = $invitation[0];

		$enroll_id = $invitation['enroll_id'];

		$payload = array(

			'inviteId' => $invitation['id'],
			'toAddress' => $invitation['name'],
			'toEmail' => $invitation['email']

		);

			if($lastItem = $this->module->returnLastById($enroll_id))
			{
				$data['lastEnroll'] = $lastItem;
				$data['message'] = "Enrollment Successfull";
				$statusCode = 201;
			}

			else {
					$data['message'] = "Enrollment Successfull but to load last Record";
					$statusCode = 500;
				}

		if($emailModule->sendInviationEmail($payload))
		{

			if($this->module->updateInvited($enroll_id))
			{
				$data['enroll'] = 'updated to true';
			}
			
			$data['message'] = "Invitation Sent to " . $payload['toAddress'] . " successfully";
			$statusCode = 200;
		}

		else {

			$data['message'] = "Cannot trigger email";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);		

	}



	public function defineIntercept()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();

		
		if(!jwACL::has('enroll-intercept')) 
			return $this->accessDenied();
		

		$_POST = Route::$_PUT;

		$enrollID = $this->getID();


		$quizId = $this->getParam('quizId');

		
		$quizModule = $this->load('module', 'quiz');

		if($quizModule->isDLSEnabledQuiz($quizId))
		{
			$data['message'] = "Dls Quiz cannot be intercepted";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
		}

		$keys  = array('intercept', 'direction', 'lastLimit');

		$dataPayload = $this->module->DB->sanitize($keys);

		$quizDetails = $quizModule->getQuizById($quizId);

		$quizDetails = $quizDetails[0];


		$passPerCentage = ($quizDetails['minScore'] / $quizDetails['maxScore']) * 100;

		$data['passPErcentage'] = $passPerCentage;

		if($dataPayload['direction'] == 'Pass' && $dataPayload['lastLimit'] < $passPerCentage)
		{
			$data['message'] = "Passing below quiz " . $passPerCentage . " % not allowed ";
			$statusCode = 500;
			return View::responseJson($data, $statusCode);
		}

		else if($dataPayload['direction'] == 'Fail' && ($dataPayload['lastLimit'] >= $passPerCentage) )
		{
			$data['message'] = "Fail limit must be lower than passing i.e $passPerCentage";
			$statusCode = 500;
			return View::responseJson($data, $statusCode);
		}


		else if($dataPayload['direction'] == 'Pass' && $dataPayload['lastLimit'] > 100)
		{
			$data['message'] = " Pass Percentage canot be highier than 100";
			$statusCode = 500;
			return View::responseJson($data, $statusCode);
		}

		

		else if ($dataPayload['lastLimit'] < 0 ||  $dataPayload['lastLimit'] > 100)
		{
			$data['message'] = "Limit must be in a range of 100";
			$statusCode = 500;
			return View::responseJson($data, $statusCode);	
		}


		if($this->module->update($dataPayload, $enrollID))
		{

			$data['message'] = "Intercept updated successfully";
			$statusCode = 200;
			

		}

		else {

			$data['message'] = "Failed while updateing values for intercept";
			$statusCode = 500;
			

		}

		return View::responseJson($data, $statusCode);

	}


}