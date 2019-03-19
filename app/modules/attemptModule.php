<?php 
class attemptModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'stdattempts';		
	}





	public function initiateQuiz($enroll_id)
	{

		$data['enroll_id'] = $enroll_id;
		
		if($attemptId = $this->DB->insert($data))
		{
			return $attemptId;
		}
		else {
			return false;
		}
	}



	

}