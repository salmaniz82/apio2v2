<?php class userCtrl extends appCtrl{

	
	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'user');
	}



	public function index()
	{

		$data['users'] = $this->module->allUsers();
		$data['status'] = true;
		$statusCode = 200;

		View::responseJson($data, $statusCode);

	}

	public function single()
	{

		$id = $this->getID();
		echo "show single user with id" . $id;

	}

	public function save()
	{

		if(!isset($_POST['role_id']))
		{
		
			// if role is not give then it must be a student
			$keys = array('name', 'email', 'password');
			$dataPayload = sanitize($keys);
			$dataPayload['role_id'] = 4;

			if(!$this->module->emailExists($dataPayload['email']))
			{
				if($last_id = $this->module->addNewUser($dataPayload) )
				{
					$statusCode = 200;
					$data['message'] = "Registration User Created Successfully";
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

				$statusCode = 500;
				$data['message'] = "User With Email Already Exists";
				$data['status'] = false;				
			}

			return View::responseJson($data, $statusCode);

		}

	}

	public function udpate()
	{



	}

	public function destroy()
	{

	}


	public function statusToggle()
	{
		
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
		
        if($res = $this->module->changePassword($_POST['password'], $id))
        {
        	$data['status'] = true;
        	$data['message'] = "Password Updated";
			$statusCode = 200;        	
        }
        else {

        	$data['status'] = false;
        	$data['message'] = " Failed Password Updated";
        	$data['res'] = $res;
			$statusCode = 500;
        }

        View::responseJson($data, $statusCode);
	}


	public function registerEnroll()
	{

		$quiz_id = $_POST['examID'];

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



		if(!isset($_POST['role_id']))
		{
		
			// if role is not give then it must be a student
			$keys = array('name', 'email', 'password');
			$dataPayload = sanitize($keys);
			$dataPayload['role_id'] = 4;
			$dataPayload['status'] = 1;

			if(!$this->module->emailExists($dataPayload['email']))
			{
				
				if($last_id = $this->module->addNewUser($dataPayload) )
				{
				
					
					$statusCode = 200;
					$data['message'] = "Registration User Created Successfully";
					
					$enrollmentModule = $this->load('module', 'enroll');

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

				$statusCode = 500;
				$data['message'] = "User With Email Already Exists";
				$data['status'] = false;				
			}

			return View::responseJson($data, $statusCode);

		}


	}


}