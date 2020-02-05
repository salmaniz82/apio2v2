<?php 


if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


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
			'companyTitle' => 'required',
			'url'    =>  'required',
			'email'    =>  'required|valid_email',
			'contactPerson' 	=> 'required',
			'address' 	=> 'required',
			'mobile' 		=> 'required',
			'landline' 	=> 'required'
		));



		$pdata = $gump->run($_POST);



		if($pdata === false) 
		{

			$data['status'] = false;

			$errorList = $gump->get_errors_array();
			$errorFromArray = array_values($errorList);
			$data['errorlist'] = $errorList;
			$data['message'] = $errorFromArray[0];
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

			die();
			
		}








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



		$this->load('external', 'gump.class');

		$gump = new GUMP();


		if(isset($_FILES)) 
		{

			$_FILES = $gump->sanitize($_FILES);

		}

		else {

			return $this->emptyRequestResponse();

		}


		$gump->validation_rules(array(
			
			'file'   =>  'required_file|extension,png;jpg'
			
		));



		$pdata = $gump->run($_FILES);


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


		 	$maxLogoSize = 1048576;

		 	if( $_FILES["file"]["size"] > $maxLogoSize)
			{
				
				$maxReachedFileSize = true;
				$data['message'] = "File size must not be highier than 1 MB";
				$statusCode = 406;
				return View::responseJson($data, $statusCode);

			}
			


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
		

		$this->load('external', 'gump.class');

		$gump = new GUMP();

		$_POST = Route::$_PUT;

	
		$_POST = $gump->sanitize($_POST);


		$gump->validation_rules(array(
			'companyTitle' => 'required',
			'url'    =>  'required',
			'email'    =>  'required|valid_email',
			'contactPerson' 	=> 'required',
			'address' 	=> 'required',
			'mobile' 		=> 'required',
			'landline' 	=> 'required'
		));



		$pdata = $gump->run($_POST);



		if($pdata === false) 
		{

			// validation failed
		
			$data['status'] = false;
			$data['message'] = 'Required fields were missing or supplied with invalid format';
			$data['errorlist'] = $gump->get_errors_array();
			$statusCode = 406;

			return View::responseJson($data, $statusCode);

			die();
			
		}


		$user_id = jwACL::authUserId();


		$keys = array('companyTitle', 'url', 'email', 'contactPerson', 'address', 'mobile', 'landline');

		$dataPayload = $this->module->DB->sanitize($keys);

		$condArr = array('user_id', $user_id);

		return $this->updateHandler($dataPayload, $condArr);



	}



	public function entityProfilebyslug()
	{

		$slug = Route::$params['slug'];

		if($profile = $this->module->entityProfileBySlug($slug))
		{
			$data['profile'] = $profile;
			$data['status'] = true;
			$statusCode = 200;
		}

		else {

			$data['message'] = 'Profile Information not found';
			$data['status'] = false;
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}



	public function verify()
	{


	}



	public function slugAvailable()
	{



		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


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
			
			'slug'    =>  'required'
			
		));
		

		$slug = $_POST['slug'];	


		if($this->module->isSlugTaken($slug))
		{
			
			$data['message'] = "Slug already taken";
			$statusCode = 406;

		}

		else {

			$data['message'] = "Slug is available";
			$statusCode = 200;				

		}

		return View::responseJson($data, $statusCode);

	}


	public function authProfileLogo()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	

		$user_id = jwACL::authUserId();

		if($logo = $this->module->autoProfileLogo($user_id))
		{
			$data['logo'] = $logo;
			$statusCode = 200;		
		}
		else {

			$data['message'] = 'logo is not uploaded';
			$statusCode = 406;

		}



		return View::responseJson($data, $statusCode);




	}



}