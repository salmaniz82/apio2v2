<?php class optionImagesCtrl extends appCtrl
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