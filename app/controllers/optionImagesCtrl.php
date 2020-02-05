<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class optionImagesCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
	
		$this->module = $this->load('module', 'optionimages');

	}



	public function saveandreturn()
	{
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		


		
		if(!jwACL::has('dashboard-questions-add')) 
			return $this->accessDenied();



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



			$maxLogoSize = 1048576;

		 	if( $_FILES["file"]["size"] > $maxLogoSize)
			{

				$data['message'] = "File size must not be highier than 1 MB";
				$statusCode = 406;
				return View::responseJson($data, $statusCode);

			}



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



		$user_id = jwACL::authUserId();
		$optionLabel = $this->getParam('optionLabel');

		


		$lastOptionId = $this->getLastId();
		$path_parts = pathinfo($_FILES["file"]["name"]);
		$extension = $path_parts['extension'];
	

		$target_dir = "uploads/optionimages/";
		$filename = $lastOptionId.'-option-'.$optionLabel.'.'.$extension;
		$target_file = $target_dir.$filename;


		if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
		{
     			
			$payload = array(

				'user_id' => $user_id,
				'optionImageUrl' => SITE_URL.$target_file

			);


    		if($this->module->save($payload))
    		{
    			$data['message'] = 'File uploaded Saved to Database';
    			$data['imageUrl'] = SITE_URL.$target_file;
    			$statusCode = 200;
    			$data['status'] = true;

    		}

    		else {

    			$data['message'] = 'File uploaded but failed while udpating records';
    			$statusCode = 500;

    		}



   		} else {

				$data['message'] = 'Error while handling file upload to server';
        		$statusCode = 500;

   		}	


   		return View::responseJson($data, $statusCode);
		

	}


	public function getLastId()
	{


		if($lastId = $this->module->getLastId())
		{

			$lastId = $lastId[0]['lastId'];

			if($lastId == null)
			{
				return 1;
			}

			return $lastId;

		}


		return 1;



	}



}