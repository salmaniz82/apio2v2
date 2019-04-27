<?php 
class appCtrl {



	public function load($loadType, $Loadentity)
	{

		if($loadType == 'module')
		{
			
			require_once ABSPATH.'app/modules/'.$Loadentity.'Module.php';
			$ModuleClass =  $Loadentity.'Module';
			return new $ModuleClass();
		}

		elseif($loadType == 'external')
		{
			
			$path = ABSPATH.'app/external/'.$Loadentity.'.php';
			require_once($path);
			
		}

	}
	
	

	public function uaReponse() 
	{

		$data['status'] = false;
    	$data['message'] = "Access Denied";
	    $statusCode = 401;
    	return view::responseJson($data, $statusCode);
	}


	public function emptyRequestResponse()
	{

		$data['status'] = false;
    	$data['message'] = "The Request is Empty Process Terminated";
	    $statusCode = 403;
    	return view::responseJson($data, $statusCode);

	}



	public function getID()
	{
		
		if( isset(Route::$params['id']) )
		{
			return (int) Route::$params['id'];
		}
		else 
		{
			return null;
		}

	}

	
	
    public function jwtUserId()
    {

    	if(JwtAuth::validateToken())
		{
			return (int) JwtAuth::$user['id'];
		}
		else {
			return false;
		}

    }

    public function jwtRoleId()
    {

    	if(JwtAuth::validateToken())
		{
			return (int) JwtAuth::$user['role_id'];
		}
		else {
			return false;
		}

    }

    public function Dt_24()
    {
    	return Date('Y-m-d H:i:s');
    }



    public function getParam($label)
	{
		
		if( isset(Route::$params[$label]) )
		{
			return Route::$params[$label];
		}
		else 
		{
			return null;
		}

	}





   
    
}
