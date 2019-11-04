<?php class emailTemplateCtrl extends appCtrl {

	public $module;


	public function __construct()
	{

		$this->module = $this->load('module', 'user');

	}


	public function inviteExam()
	{


		$inviteID = $this->getID();

		$invitationModule = $this->load('module', 'invitations');

		$profileModule = $this->load('module', 'profile');

		$data = $invitationModule->invitationQuizDetails($inviteID);	

		return View::render('/emails/quiz-invitation', $data);

	}





	public function selfRegister()
	{



		$userId = $_GET['user_id'];


		$tickets = $this->module->singnupDetails($userId);

		if($tickets = $this->module->singnupDetails($userId))
		{
			
			$data['users'] = $tickets[0];
			$data['title'] = "Signup Details";
			return View::render('emails/signup-details', $data);

		}

		else {

			$data['message'] = "Ticket data is not available";
			$statusCode = 200;

		}

		return View::responseJson($data, $statusCode);
		

	}


	public function changePassword()
	{

		return View::render('emails/change-password');		

	}

	public function registerEnroll()
	{

	}

	public function registered()
	{

	}



	public function enrolled()
	{

	}


	


	public function examResult()
	{

	}

	


	public function inviteBatch()
	{




	}







}