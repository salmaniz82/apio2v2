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
    	$data['message'] = "Not Authenticated Request denied";
	    $statusCode = 401;
    	return view::responseJson($data, $statusCode);
	}

	public function accessDenied() 
	{

		$data['status'] = false;
    	$data['message'] = "Access Denied: No permission granted";
	    $statusCode = 403;
    	return view::responseJson($data, $statusCode);
	}


	public function ownerDisqualifyResponse()
	{

		$data['status'] = false;
    	$data['message'] = "Ownership rejected for this operation";
	    $statusCode = 403;
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
		
			return false;
		

    }

    public function jwtRoleId()
    {

    	if(JwtAuth::validateToken())
		{
			return (int) JwtAuth::$user['role_id'];
		}
		
			return false;
		

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


	public function encodeData($data)
	{

		// return urlencode(base64_encode(json_encode($data)));
		return base64_encode(json_encode($data));

	}


	public function decodeData($str)
	{

		return base64_decode($str);

	}



	public function slugify($text)
	{
	  // replace non letter or digits by -
	  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

	  // transliterate
	  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	  // remove unwanted characters
	  $text = preg_replace('~[^-\w]+~', '', $text);
	  
	  // trim
	  $text = trim($text, '-');

	  // remove duplicate -
	  $text = preg_replace('~-+~', '-', $text);

	  // lowercase
	  $text = strtolower($text);

	  if (empty($text)) {
	    return 'n-a';
	  }

	  return $text;

	}



	public function apiEncodeUri($payload)
	{
		return urlencode(base64_encode(json_encode($payload)));	
	}

	public function apiDecodeUri($payload)
	{

		return urldecode(base64_decode(json_decode($payload)));

	}



	public function stringIsAbsoluteImagePath($string)
	{

		if(preg_match('/(http(s?):)([\/|.|\w|\s|-])*\.(?:jpg|gif|png|jpeg|svg)/', $string))
		{
			return true;
		}

		return false;
	}


	public function startsWithUrl($string)
	{
		if(preg_match('/^(http(s?))/', $string))
		{
			return true;
		}

		return false;
	}



}
