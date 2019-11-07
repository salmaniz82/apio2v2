<?php 
class invitationsModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'quizinvitations';

	}


	public function saveInvitation($payload)
	{


		if($last_id = $this->DB->insert($payload))
		{
			return $last_id;	
		}	

		return false;

	}


	public function invitationQuizDetails($inviteID)
	{
		$sql = "SELECT qi.id, qi.uriToken, qz.title, qz.duration, 
		en.id as enroll_id, 
		DATE_FORMAT(qz.endDateTime, '%d %b %Y') as endDate, 
		DATE_FORMAT(qz.endDateTime, '%h:%m %p') as endTime, 
		cnd.name, cnd.email from enrollment en 
				INNER JOIN quiz qz on qz.id = en.quiz_id 
				INNER JOIN quizinvitations qi on qi.enroll_id = en.id
				INNER JOIN users cnd on cnd.id = en.student_id
				WHERE qi.id = $inviteID";


				if($data = $this->DB->rawSql($sql)->returnData($sql))
				{

					return 	$data;
				}

				return false;

	}


	public function updateRecordwithToken($uriToken, $inviteId)
	{

		if($this->DB->update($uriToken, $inviteId))
		{
			return true;
		}

		return false;


	}



	public function validateInvitation($enroll_id, $candidate_id)
	{

		$sql = "SELECT qi.id as inviteID, en.id as enroll_id, u.id as user_id from quizinvitations qi 
				INNER JOIN enrollment en on en.id = qi.enroll_id 
				INNER JOIN users u on u.id = en.student_id 
				WHERE u.id = $candidate_id AND en.id = $enroll_id  
				LIMIT 1";

		if($invitation = $this->DB->rawSql($sql)->returnData())
		{
			return true;
		}


		return false;

	}


	






}