<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class subjectCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'subject');
	}


	public function index()
	{
		

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		
		$quiz_id = $this->getID();
		$quizModule = $this->load('module', 'quiz');

		if($row = $this->module->baseDistro($quiz_id))
		{
			$data['quizBaseDistro'] = $row;
			$data['quiz'] = $quizModule->getQuizById($quiz_id);

			$data['quiz'][0]['totalAvailable'] = array_sum(array_column($data['quizBaseDistro'], 'questions'));

			$statusCode = 200;
		}

		else {
			$data['message'] = "Unable to load Distrubtion info";
			$statusCode = 400;
		}

		return View::responseJson($data, $statusCode);


	}



	public function updateDistro()
	{

		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


		if(!jwACL::has('quiz-edit')) 
			return $this->accessDenied();



		$_POST = Route::$_PUT;
		$quiz_id = $this->getID();

		if($this->module->updateDistro($quiz_id, $_POST))
		{
			$data['message'] = "Distribution Updated";
			$statusCode = 200;
			$data['status'] = true;
		}
		else {
			$data['message'] = "Failed while updating distribution";
			$statusCode = 500;
			$data['status'] = false;
		}

		return View::responseJson($data, $statusCode);

	}



    public function wsubjects()
    {


    	/*

    	usage : Method POST
    	body
    	header : token : authtoken
		body exams	
    	{
			"threshold" : 10,
			"subject_ids" : [80,81,82]
		}

    	*/


    	if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();

		
		if(!jwACL::has('quiz-add')) 
			return $this->accessDenied();


        $entityId = jwACL::authUserId();

        $threshold = (isset($_POST['threshold'])) ? $_POST['threshold'] : "10000";



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
			
			'subject_ids' => 'required'
			
		));



		$pdata = $gump->run($_POST);


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



        $subjectIds = $_POST['subject_ids'];

        if($distro = $this->module->questionsCountSummaryOnWizard($threshold, $subjectIds, $entityId))
        {
           
            $data['distro'] = $distro;    
            $data['status'] = true;
            $statusCode = 200;
        }

        else {

            $data['message'] = 'Unable to fetch distribution of chosen subjects';
            $statusCode = 500;

        }

        return View::responseJson($data, $statusCode);


    }


}