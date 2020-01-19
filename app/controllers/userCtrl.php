<?php class userCtrl extends appCtrl 
{

	
	public $module;

	public function __construct()
	{
		$this->module = $this->load('module', 'user');
	}

	public function destroy()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		if(!jwACL::has('user-delete')) 
			return $this->accessDenied();


		$user_id = $this->getID();

		$authID = jwACL::authUserId();


		if(!$this->module->isOwnedAuthUser($user_id, $authID) && !jwACL::isAdmin())
		{
			return $this->ownerDisqualifyResponse();	
		}


		if($this->module->deleteUser($user_id))
		{

			$data['message'] = "User removed";
			$statusCode = 200;
			$data['status'] = false;

		}

		else {

			$statusCode = 500;
			$data['message'] = "User removal failed";
			$data['status'] = false;

		}

		return View::responseJson($data, $statusCode);	

	}



	public function entityTaggedUserList()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		$entity_id = jwACL::authUserId();
		
		if($data['users'] = $this->module->taggedUsersList($entity_id))
		{
			
			$data['status'] = true;
			$statusCode = 200;
		}
		else {
			$data['message'] = "No Users Found";
			$statusCode = 206;
		}

		$roleModules = $this->load('module', 'role');
		$data['roles'] = $roleModules->returnAllRoles($this->jwtRoleId());


		$categoryModule = $this->load('module', 'category');	
		$data['topCategories'] = $categoryModule->flatRootList();

		return View::responseJson($data, $statusCode);
		
	}



	public function passtest()
	{
		echo $this->module->generateRandomPassword();
	}


	public function index()
	{


		if($data['users'] = $this->module->allUsers())
		{
			
			$data['status'] = true;
			$statusCode = 200;
		}
		else {
			$data['message'] = "No Users Found";
			$statusCode = 206;
		}

		$roleModules = $this->load('module', 'role');
		$data['roles'] = $roleModules->returnAllRoles($this->jwtRoleId());


		$categoryModule = $this->load('module', 'category');	
		$data['topCategories'] = $categoryModule->flatRootList();
		

		return View::responseJson($data, $statusCode);

	}

	public function single()
	{

		$id = $this->getID();
		echo "show single user with id" . $id;

	}

	public function save()
	{

			$authenticatedRequest = false;
			$sendMail = false;



			if(isset($_POST['sendEmail']) && $_POST['sendEmail'] == true)
			{
				$sendMail = true;				
			}


			$roleModule = $this->load('module', 'role');
			$taggedUserModule  = $this->load('module', 'taggedusers');

			$boudUserToCategory = false;	

			$keys = array('name', 'email', 'password');

			$dataPayload = $this->module->DB->sanitize($keys);

			$dataPayload['status'] = 1;


			if(jwACL::isLoggedIn())
			{

				$authenticatedRequest = true;
				// some authenticated user is creating the user to it must have a created_by user id with it.
				$dataPayload['created_by'] = jwACL::authUserId();
				$authenticatedRole = jwACL::authRole();

			}

			if(!isset($_POST['role_id']))
			{
				// if role is not give then it must be a student
				$dataPayload['role_id'] = 4;
				$assignContributor = false;
			}
			else{

				$dataPayload['role_id'] = $_POST['role_id'];			

			}

			$roleName = $roleModule->pluckRoleNameById($dataPayload['role_id']);


			$assignContributor = ($roleName == 'contributor') ? true : false;


			if($roleName == 'contributor' || $roleName == 'content developer')
			{
					$boudUserToCategory = true;
			}

			


			if($assignContributor && !isset($_POST['topCategory']))
			{

				$data['message'] = "Top Level Category was missing a ". $roleName;
				$statusCode = 406;
				return View::responseJson($data, $statusCode);

				die();

			}


			if(!$this->module->emailExists($dataPayload['email']))
			{
				if($last_id = $this->module->addNewUser($dataPayload) )
				{

					// load module and insert the ticket
					if($dataPayload['role_id'] == 2)
					{

						$quizTemplatesArray = QUIZ_TEMPLATES;		

						$templateQuizIDs = implode($quizTemplatesArray, ',');

						$this->triggerQuizTemplateGenerator($last_id, $templateQuizIDs);

					}


						if(isset($dataPayload['created_by']) && $dataPayload['created_by'] != null)
						{
							
							// if auth role is entity then attemp to tag users

							if($authenticatedRequest && $authenticatedRole == 'entity')
							{
								
								$taggedUserModule->linkusertoentity($last_id, $dataPayload['created_by']);

							}

						}

						if($sendMail)
						{

							$data['ticket'] = "attempt to create ticket and send email";

							$preliminaryModule = $this->load('module','preliminary');

							$ticketsPayload = array('user_id'=> $last_id, 'ticket' => $_POST['password']);

							if($preliminaryModule->addTickets($ticketsPayload))
							{

								$data['ticket'] = "ticket created";

								$emailModule =  $this->load('module', 'email');

								$newUserDetails = array(

								'user_id' => $last_id,
								'email' => $dataPayload['email']

								);

								if($emailModule->sendRegistrationEmail($newUserDetails))
								{

									$data['email'] = "Sent";
									$preliminaryModule->removeTicket(['user_id', $last_id]);
								}
								else {
										$data['email'] = "Email not sent";
								}

							}

							else {

								$data['ticket'] = "cannot be created";
							}



						}

						else {

							$data['email'] = "Disable unchecked email not triggered";

						}


					


					if($assignContributor)
					{
						$contributorModule = $this->load('module', 'contributor');

						$assignData = array(
							'contributor_id' => $last_id,
							'entity_id' => jwACL::authUserId()
						);						

						$contributorModule->assignContributor($assignData);
					}


					if($boudUserToCategory)
					{

						$boundCategoryModule = $this->load('module', 'boundcategory');


						$boundPayload = array(

							'user_id' => $last_id,
							'category_id' => $_POST['topCategory']

						);


						if($boundCategoryModule->save($boundPayload))
						{
							$data['boundMessage'] = $roleName. " linked to top category";
						}

						else {

							$data['boundMessage'] = "User link to top level failed";

						}



					}

					else {


						$data['boundMessage'] = "User boundUSerToCategoryWas not activated";

					}
					

					$userPermissionModule = $this->load('module', 'userpermissions');
					$assignedPermissions = $userPermissionModule->assignNewUserDefaultRolePermissions($last_id, $dataPayload['role_id']);

					$statusCode = 200;
					$data['message'] = "Registration User Created Successfully";
					$data['permissions'] = ($assignedPermissions) ? 'Permission Assigned' : 'Cannot assign default permission';
					$data['status'] = true;

					if($authenticatedRequest && $authenticatedRole == 'entity')
					{
						$data['lastCreatedUser'] = $this->module->singleTaggedUser($last_id, $dataPayload['created_by']);
					}

					else {

						$data['lastCreatedUser'] = $this->module->userById($last_id);

					}


				}
				else 
				{
					$statusCode = 500;
					$data['message'] = "Failed While Creating new Users";
					$data['status'] = false;				
				}
			}
			else{


				if($existingUser = $this->module->userByEmail($dataPayload['email']))
				{

					$existingUser = $existingUser[0];

					$existingUserID = $existingUser['id'];

					if($existingUser['role_id'] == $dataPayload['role_id'])
					{
						

						if($this->module->singleTaggedUser($existingUserID, $dataPayload['created_by']))
						{

							$data['message'] = "User already exist, duplicate operation denied";
							$statusCode = 500;
							return View::responseJson($data, $statusCode);

						}


						// request and exisiting roles are the same
						if($authenticatedRequest && $authenticatedRole == 'entity')
						{
							/*if request is authenticated then tag user to entity*/
							$taggedUserModule->linkusertoentity($existingUserID, $dataPayload['created_by']);
							$data['message'] = "New user added";
							$data['lastCreatedUser'] = $this->module->singleTaggedUser($existingUserID, $dataPayload['created_by']);
							$statusCode = 200;
						}

						else
						{

							$statusCode = 500;
							$data['message'] = "Email already exists";
							$data['status'] = false;

						}

						

						
					}

					else {

						$statusCode = 500;
						$data['message'] = "cannot create user role resolution conflict";
						$data['status'] = false;

					}

				}

				else {
					// failed fetching existing user
					$statusCode = 500;
					$data['message'] = "Failed procedure while creating user";
					$data['status'] = false;
				}

			}

			return View::responseJson($data, $statusCode);

	}




	public function udpate()
	{



	}

	


	public function statusToggle()
	{
		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		if(!jwACL::has('user-status-toggle')) 
			return $this->accessDenied();

		$_POST = Route::$_PUT;
		$user_id = $this->getID();




		$authID = jwACL::authUserId();


		if(!$this->module->isOwnedAuthUser($user_id, $authID) && !jwACL::isAdmin())
		{
			return $this->ownerDisqualifyResponse();	
		}



		$data['status'] = $_POST['status'];
		if($this->module->statusToggle($data, $user_id))
		{
			
			$data['status'] = true;
			$statusCode = 200;
			$data['user'] = $this->module->userById($user_id);

			if($data['user'][0]['status'] == 1)
			{
				$data['message'] = $data['user'][0]['email'] . ' : ' ." Enabled";
			}

			else {
				$data['message'] = $data['user'][0]['email'] . ' : ' ." Disabled";
			}
			
		}
		else {

			$data['message'] = "Operation failed";
			$data['status'] = false;
			$statusCode = 500;
		}

		View::responseJson($data, $statusCode);

	}


	public function changePassword()
	{
		
		if(!jwACL::isLoggedIn()) {
			return $this->uaReponse();

			die();
		}
		



		$_POST = Route::$_PUT;
		$id = $this->getID();
		

		$password = (isset($_POST['password'])) ? trim($_POST['password']) : null;


		$authID = jwACL::authUserId();


		if(!$this->module->isOwnedAuthUser($id, $authID) && !jwACL::isAdmin())
		{
			return $this->ownerDisqualifyResponse();	
		}



		
        if($res = $this->module->changePassword($password, $id))
        {
        	$data['status'] = true;
        	$data['message'] = "Password Updated";
			$statusCode = 200;        	
        }
        else {

        	$data['status'] = false;
        	$data['message'] = "Failed Password Updated";
			$statusCode = 500;
        }

        View::responseJson($data, $statusCode);
	}


	public function registerEnroll()
	{


		$sendRegistrationEmail = (isset($_POST['sendRegisterEmail']) && $_POST['sendRegisterEmail'] == true ) ? true : false;

		$sendInviteEmail = (isset($_POST['sendInviteEmail']) && $_POST['sendInviteEmail'] == true ) ? true : false;


		$quiz_id = $_POST['examID'];

		if(!isset($_POST['role_id']))
		{
			$dataPayload['role_id'] = 4;
		}
		else{
			$dataPayload['role_id'] = $_POST['role_id'];
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


			$taggedUserModule  = $this->load('module', 'taggedusers');
			$enrollmentModule = $this->load('module', 'enroll');
		
		
			// if role is not give then it must be a student
			$keys = array('name', 'email', 'password');
			$dataPayload = sanitize($keys);
			$dataPayload['role_id'] = 4;
			$dataPayload['status'] = 1;

			if(!$this->module->emailExists($dataPayload['email']))
			{

				if(jwACL::isLoggedIn())
				{
					$dataPayload['created_by'] = jwACL::authUserId();				
				}
				
				if($last_id = $this->module->addNewUser($dataPayload) )
				{

					/*
					check if user is tagged if not then do it
					*/

					$taggedUserModule->linkusertoentity($last_id, $dataPayload['created_by']);


					if($sendRegistrationEmail)
					{

						$preliminaryModule = $this->load('module','preliminary');

						$ticketsPayload = array('user_id'=> $last_id, 'ticket' => $_POST['password']);

						if($preliminaryModule->addTickets($ticketsPayload))
						{

						/* start trigger email */
						$emailModule =  $this->load('module', 'email');

						$newUserDetails = array(

						'user_id' => $last_id,
						'email' => $dataPayload['email']

						);

						if($emailModule->sendRegistrationEmail($newUserDetails))
							{

								$data['email'] = "Sent";
								$preliminaryModule->removeTicket(['user_id', $last_id]);
							}
							else {
									$data['email'] = "Email not sent";
							}
						}
					}

					else {

						$data['email'] = "Register email not activaed";

					}


					$statusCode = 200;
					
					$data['message'] = "Registration User Created Successfully";

					$userPermissionModule = $this->load('module', 'userpermissions');

					$assignedPermissions = $userPermissionModule->assignNewUserDefaultRolePermissions($last_id, $dataPayload['role_id']);

					$data['permissions'] = ($assignedPermissions) ? 'Permission Assigned' : 'Cannot assign default permission';
					
					

					if($enrollmentModule->enrolltoQuiz($last_id, $quiz_id))
					{
						$data['message'] = "Registration & Enrollment Successfull";						
					}
					else {
						$data['message'] = "Registration done but failed while enrollment";
					}

					$data['status'] = true;

				}
				else 
				{
					$statusCode = 500;
					$data['message'] = "Failed While Creating new Users";
					$data['status'] = false;				
				}
			}
			else{


				if($existingUser = $this->module->userByEmail($dataPayload['email']))
				{

					$existingUser = $existingUser[0];

					$existingUserID = $existingUser['id'];

					if($existingUser['role_id'] != 4)
					{
						$data['message'] = "User is not in scope for enrollment";
						$statusCode = 406;
						return View::responseJson($data, $statusCode);

					}

					if($this->module->singleTaggedUser($existingUser, $dataPayload['created_by']))
					{

							$data['message'] = "User already exist, try enroll";
							$statusCode = 406;
							return View::responseJson($data, $statusCode);

					}

					$taggedUserModule->linkusertoentity($existingUserID, $dataPayload['created_by']);

					
					if($enrollmentModule->enrolltoQuiz($existingUserID, $quiz_id))
					{
						
						$data['message'] = "Registration & Enrollment Successfull";
						$statusCode = 200;
						$data['status'] = true;

					}
					else {

						$data['message'] = "Registration done but failed while enrollment";
						$statusCode = 406;
						$data['status'] = false;

					}
					
			}

				return View::responseJson($data, $statusCode);
	}


}


	public function uploadCandidates()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();

		$fileRowsLimit = 100;

		$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv', 'application/octet-stream');


		if(!isset($_FILES['file']))
		{
			
			$data['message'] = "No File Provided cannot proceed further";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
		}

		else if(!in_array($_FILES["file"]["type"], $mimes) )
		{


			$data['message'] = "File type is not supported";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			
		}

		else {


				$target_dir = "uploads/datafiles/users/";

				$filename = sanitizeFilename(basename($_FILES["file"]["name"]));

				$target_file = $target_dir.$filename;
				
		}


		if(file_exists($target_file))
		{
			unlink($target_file);	
		}


		if(!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file))
		{

				$data['message'] = "unable to upload file";
				return View::responseJson($data, 500);
		}



		$fileRowsLimit = 100;

		$csv = array_map('str_getcsv', file($target_file));

		$fileRowsCount = sizeof($csv) - 1;


		if($fileRowsCount > $fileRowsLimit)
		{
				$data['message'] = $fileRowsLimit . " rows of data is allowed for upload provied with " . $fileRowsCount;
				$statusCode = 406;
				return View::responseJson($data, $statusCode);

				die();
		}


		$requiredColumns = array('name', 'email');

		/* make sure all require columns exists in the file */
		foreach ($requiredColumns as $value) 
		{	

				if(!$idx = array_search($value, $csv[0]))
				{
						$data['message'] = "Required column ". $value . " not provided in file ";
						return View::responseJson($data, 406);
				}

		}


			$entity_id = jwACL::authUserId();


			$roleIndex = array_push($csv[0], 'role_id');
			$creatorIndex = array_push($csv[0], 'created_by');
			$passwordIndex = array_push($csv[0], 'password');
			$statusIndex = array_push($csv[0], 'status');

			$nameIndex = array_search('name', $csv[0]);
			$emailIndex = array_search('email', $csv[0]);

			$infoPayload = array();


			for($i=0; $i<sizeof($csv); $i++)
			{


				if($i > 0 ) 
				{

						if($csv[$i][$nameIndex] == "" || strlen($csv[$i][$nameIndex]) < 3)
						{

							$data['message'] = "Provided name is invalid valid at line" . $i + 1;
							$statusCode = 406;
							return View::responseJson($data, 406);

							die();

						}

						if($csv[$i][$emailIndex] == "")
						{

							$data['message'] = "Invalid email at line " . $i + 1;
							$statusCode = 406;
							return View::responseJson($data, 406);

							die();

						}


						$csv[$i][$roleIndex] =  '4';
						$csv[$i][$creatorIndex] = $entity_id;

						$password = $this->module->generateRandomPassword();

						$infoPayload[$i] = array('email'=> $csv[$i][$emailIndex], 'password'=> $password);
						$csv[$i][$passwordIndex] = $this->module->hashPasswordLowCost($password);
						$csv[$i][$statusIndex] = 1;



				}

			}	

			$finalArray = array('name', 'email', 'password', 'role_id', 'created_by', 'status');

			$unmatchedArrayKeys = [];

			for($nk = 0; $nk < sizeof($csv[0]); $nk++ )
			{

			
				if(array_search($csv[0][$nk], $finalArray) === false)
				{

					array_push($unmatchedArrayKeys, $nk);

				};
		

			}


			foreach(array_keys($csv) as $key) {

				foreach ($unmatchedArrayKeys as $ukey => $removeIndex) {

					unset($csv[$key][$removeIndex]);
				
				}

			}


			$lastMaxId = $this->module->lastCreatedUserByEntity($entity_id) || null;

			$csvColValue = array_values($csv[0]);

			$cols = array_shift($csv);

			$dataset['cols'] = $cols;

			$dataset['vals'] = $this->module->DB->escArray($csv);

			$recordLength = sizeof($dataset['vals']);


			if($this->module->uploadBulkCanidates($dataset))
			{

				$data['message'] = $recordLength . " candidates uploaded successfully";

				$data['info'] = $infoPayload;

				/*
				catch that value in response and show that in as a list 
				*/
				$statusCode = 200;

				$data['assginedUsers'] = $this->module->postUploadTaggEntityAssgiment($entity_id, $lastMaxId);

				$userpermissionsModule = $this->module = $this->load('module', 'userpermissions');

				$data['permission'] = ($userpermissionsModule->postUserUploadCanidatePermissionAssignment($entity_id, $lastMaxId)) ? 'Persmission Assgined' : 'Failed while assiging permissions';

			}

			else {

				$data['message'] = "Failed while uploading candidates";
				$statusCode = 500;

				$data['debug'] = $this->module->DB;
			}

			unlink($target_file);	
			
			return View::responseJson($data, $statusCode);
			

	}


	public function triggerQuizTemplateGenerator($entity_id, $templateQuizIDs)
	{


			$quizModule = $this->load('module', 'quiz');

			$subjectModule = $this->load('module', 'subject');

			$quizQuestionsModule = $this->load('module', 'quizQuestions');

			$templateArrays = explode(',', $templateQuizIDs);


			if($quizModule->quizTemplateClone($entity_id, $templateQuizIDs))
			{
				if($clonedIds = $quizModule->extractClonedQuizIds($entity_id))
				{

					$syncClonedKeys = [];
        			for($i = 0; $i < sizeof($templateArrays); $i++) {

            				$syncClonedKeys[$i] = array($templateArrays[$i] => $clonedIds[$i]['id']);
        			}

        			if($subjectModule->subjectsTemplateClone($syncClonedKeys, $templateArrays))
        			{

        				$quizQuestionsModule->cloneTemplateQuestions($syncClonedKeys, $templateArrays);
        				
        			}

				}	
			}

			
	}


}