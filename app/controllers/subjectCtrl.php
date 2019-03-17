<?php class subjectCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'subject');
	}


	public function index()
	{
		
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


}