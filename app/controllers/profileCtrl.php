<?php 


class profileCtrl extends appCtrl {


	public $module;



	public function __construct()
	{

		$this->module = $this->load('module', 'profile');

	}


	public function index()
	{



		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		$user_id = jwACL::authUserId();


		if($profile = $this->module->getProfileByUserId($user_id))
		{

			$data['profile'] = $profile;
			$data['status'] = true;
			$statusCode = 200;

		}

		else {

			$data['message'] = 'Profile information is not set';
			$data['status'] = false;
			$statusCode = 302;

		}

		return View::responseJson($data, $statusCode);

	}



	public function updateHandler($dataPayload, $condArr)
	{



		if($this->module->udpateProfileInformation($dataPayload, $condArr))
		{


			$data['message'] = "Profile Information Updated";
			$statusCode = 200;

		}

		else {
			$data['message'] = "Failed while updating profile information";
			$statusCode = 500;
		}

		return View::responseJson($data, $statusCode);



	}



	public function save()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	



		$user_id = jwACL::authUserId();

		$keys = array('companyTitle', 'slug', 'url', 'email', 'contactPerson', 'address', 'mobile', 'landline');

		$dataPayload = $this->module->DB->sanitize($keys);


		if($this->module->profileExists($user_id))
		{

			$condArr = array('user_id', $user_id);

			unset($dataPayload['logo']);

			return $this->updateHandler($dataPayload, $condArr);
		}

		$dataPayload['user_id'] = $user_id;

		
		if($lastId = $this->module->save($dataPayload))
		{
			
			$data['lastId'] = $lastId;
			$data['message'] = "Profile created";
			$statusCode = 200;

		}

		else {

			$data['message'] = "Failed While creating profile";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}



	public function updateLogo()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		$user_id = jwACL::authUserId();

		// var_dump($_FILES);

		$target_dir = "uploads/logo/";
		$filename = $user_id.basename($_FILES["file"]["name"]);
		$target_file = $target_dir.$filename;

		if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
		{
     			
				$data['message'] = 'File uploaded to server';
        		

        		if($this->module->updateOrSave($user_id, $target_file))
        		{
        			$data['message'] = 'File uploaded Saved to DAtabase';
        		}

        		else {

        			$data['message'] = 'File uploaded but failed while udpating records';

        		}


        		$statusCode = 200;




   		} else {

				$data['message'] = 'Error while handling file upload from server';
        		$statusCode = 500;

   		}	


   		return View::responseJson($data, $statusCode);
		

	}




	public function updateInfo()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		$user_id = jwACL::authUserId();

		$_POST = Route::$_PUT;

		$keys = array('companyTitle', 'url', 'email', 'contactPerson', 'address', 'mobile', 'landline');

		$dataPayload = $this->module->DB->sanitize($keys);

		$condArr = array('user_id', $user_id);

		return $this->updateHandler($dataPayload, $condArr);



	}



}