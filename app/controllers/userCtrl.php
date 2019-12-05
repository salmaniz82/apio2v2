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

					if($roleName == 'students' || $roleName == 'candidate')
					{

						// load module and insert the ticket

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
		$_POST = Route::$_PUT;
		$id = $this->getID();
		

		$password = trim($_POST['password']);

		
        if($res = $this->module->changePassword($password, $id))
        {
        	$data['status'] = true;
        	$data['message'] = "Password Updated";
			$statusCode = 200;        	
        }
        else {

        	$data['status'] = false;
        	$data['message'] = " Failed Password Updated";
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

}