<?php class emailTemplateCtrl extends appCtrl {

	public $module;


	public function __construct()
	{

		$this->module = $this->load('module', 'user');

	}


	public function selfRegister()
	{

		$userId = $_GET['user_id'];

		if($tickets = $this->module->singnupDetails($userId))
		{
			
			$data['users'] = $tickets[0];
			$data['title'] = "Signup Details";
			return View::render('emails/signup-details', $data);

		}

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

	public function inviteExam()
	{


	}


	public function inviteBatch()
	{




	}







}