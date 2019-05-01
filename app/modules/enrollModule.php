<?php class enrollModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'enrollment';

	}


	public function enrolledStudents($quiz_id)
	{
		
		$sql = "SELECT en.id, en.student_id, en.quiz_id, std.name, std.email, date(en.dateEnrolled) as dateEnrolled, en.attempts, en.retake,
		qz.user_id 
		from enrollment en 
		inner join users std on std.id = en.student_id 
        inner join quiz qz on qz.id = en.quiz_id 
		where en.quiz_id = $quiz_id";

		if($data = $this->DB->rawSql($sql)->returnData())
		{
			return $data;
		}

		return false;

	}


	public function returnLastById($id)
	{

		$sql = "SELECT en.id, en.student_id, en.quiz_id, std.name, std.email, date(en.dateEnrolled) as dateEnrolled, en.attempts, en.retake,
		qz.user_id 
		from enrollment en 
		inner join users std on std.id = en.student_id 
        inner join quiz qz on qz.id = en.quiz_id 
		where en.id = $id";

		if($data = $this->DB->rawSql($sql)->returnData())
		{
			return $data;
		}

		return false;

	}


	public function enrolltoQuiz($user_id, $quiz_id)
	{


		$dataPayload = array(
			'student_id' => $user_id,
			'quiz_id' => $quiz_id
		);


		if($last_id = $this->DB->insert($dataPayload))
		{
			return $last_id;	
		}
		return false;
	
	}


	public function duplicateCheck($user_id, $quiz_id)
	{

		if($this->DB->build('S')->Colums()->Where("student_id = '".$user_id."'")->Where("quiz_id = '".$quiz_id."'")->go()->returnData())
		{
			return true;
		}
		return false;
	}


	public function getEnrollRowById($id)
	{

		if($row = $this->DB->getbyId($id))
		{
			return $row;	
		}
		return false;
	}


	public function toggleRetake($enroll_id, $status)
	{

		$data['retake'] = (int) $status;
		if($this->DB->update($data, $enroll_id))
		{
			return $this->DB->connection->affected_rows;
		}
		return false;

	}

	public function registerAttempt($enroll_id)
	{
		$sql = "UPDATE enrollment set attempts = attempts + 1 where id = $enroll_id";

		if($this->DB->rawSql($sql))
		{
			return true;
		}

		return false;
	}


}