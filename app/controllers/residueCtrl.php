<?php class residueCtrl extends appCtrl
{

	public $module;

	public $userModule;


	public function __construct()
	{
		
		$this->module = $this->load('module', 'residue');
		$this->userModule = $this->load('module', 'user');

	}


	public function makeEntrypoint()
	{

		

		$email = $_POST['email'];

		if(!$this->userModule->emailExists($email))
		{

			$data['message'] = "No signup found with this email";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
		}



		if(!$user_id = $this->userModule->pluckIdByEmail($email))
		{

			$data['message'] = "Unable to get user details";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
		}


		$datapayload = array(

			'user_id'=> $user_id

		);

		if(!$last_id = $this->module->save($datapayload))
		{


			$data['message'] = "unable to generate recovery information";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
		}


			$tokenPayload = array(
				'action'=> 'updatepswonforget',
				'id' => $last_id,
				'user_id'=> $user_id,
				'email'=> $email
			);

			$accesstoken = urlencode(base64_encode(json_encode($tokenPayload)));

			if($this->module->updateWithToken($accesstoken, $last_id))
			{
				
				$emailModule = $this->load('module', 'email');

				if($emailModule->triggerForgetPassword($tokenPayload))
				{

					$data['message'] = "Recovery information sent to : " . $email;
					$statusCode = 200;

				}

				else {
					$data['message'] = "Failed to trigger recover email to : " . $email;
					$statusCode = 500;
				}


				return View::responseJson($data, $statusCode);


			}


	}




	public function doaction()
	{


		$requestid = $this->getID();
		$_POST = Route::$_PUT;


		$password = $_POST['password'];


		//$accesstoken = urlencode(base64_encode(json_encode($tokenPayload)));


		if(!$accessToken = $_SERVER['HTTP_ACCESSTOKEN'])
		{
			$data['message'] = "Invalid Request";
			$statusCode = 400;
			return View::responseJson($data, $statusCode);
		}


		$decodedToken = json_decode(base64_decode(urldecode($accessToken)), true);

		if(!is_array($decodedToken) )
		{
			$data['message'] = "Invalid Request or not formatted";
			$statusCode = 400;
			return View::responseJson($data, $statusCode);		
		}

		extract($decodedToken);	

		if($requestid != $id)
		{
			$data['message'] = "Request not not matched with recovery information";
			$statusCode = 400;
			return View::responseJson($data, $statusCode);
		}



		if(!$verifyInfo = $this->module->verifyValidity($id))
		{

			$data['message'] = "No matching for recovery request";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
		}

		$verifyInfo = $verifyInfo[0];

		/*
		if($verifyInfo['status'] != 0)
		{

			$data['message'] = "Recovery request is valid for an hour";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}
		*/

		$userModule = $this->load('module', 'user');
		$user_id = $userModule->pluckIdByEmail($verifyInfo['email']);

		if($userModule->changePassword($password, $user_id))
		{
			$data['message'] = "Password updated successfully";
			$statusCode = 200;
			$this->module->removeToken($id);
			$data['id'] = $id;

			$data['verifiinfo'] = $verifyInfo;
			
		}

		else {

			$data['message'] = "Failed while updating password";
			$statusCode = 500;
		}

		return View::responseJson($data, $statusCode);


	}




}