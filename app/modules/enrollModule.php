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
		
		$sql = "SELECT en.id, en.student_id, en.quiz_id, en.dtsScheduled, en.invited, 
		std.name, std.email, en.dateEnrolled as 'dateEnrolled', en.attempts, en.retake,
		qz.user_id, en.intercept, en.direction, en.lastLimit  
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


	public function udpateScheduleDateTime($dataPayload, $enroll)
	{

		
		if($this->DB->update($dataPayload, $enroll))
		{
			return $this->DB->connection->affected_rows;
		}
		return false;

	}


	public function quizDLSByEnrollmentId($enrollID)
	{
		
		$sql = "SELECT qz.dls from enrollment en INNER JOIN quiz qz on qz.id = en.quiz_id WHERE en.id = $enrollID";

		if($dls = $this->DB->rawSql($sql)->returnData())
		{
			return $dls[0]['dls'];
		}

		return false;

	}

	public function lastSevenDaysPending($user_id, $role_id)
	{
		$sql = "SELECT c.name, qz.id, qz.title, DATE_FORMAT(en.dtsScheduled, '%a %d %b %h:%i %p') as at  
				from quiz qz 
				INNER JOIN enrollment en on en.quiz_id = qz.id 
				INNER JOIN users c on en.student_id = c.id
				WHERE YEARWEEK(en.dtsScheduled)=YEARWEEK(NOW()) AND en.attempts = 0";


				if($role_id != 1)
				{
					$sql .= " AND qz.user_id = $user_id";
				}


				if($data = $this->DB->rawSql($sql)->returnData())
				{
					return $data;
				}

				return false;


	}




	public function weekSchedule($user_id, $role_id)
	{

			
		$sql ="SELECT c.name, qz.id, qz.title, DATE_FORMAT(en.dtsScheduled, '%a %d %b %h:%i %p') as at  
			from quiz qz 
			INNER JOIN enrollment en on en.quiz_id = qz.id 
			INNER JOIN users c on en.student_id = c.id
			WHERE en.dtsScheduled BETWEEN DATE(NOW()) AND DATE_ADD(NOW(), INTERVAL 7 DAY) and en.attempts = 0";


			if($role_id != 1)
			{
				$sql .= " AND qz.user_id = $user_id";
			}


			$sql .= " ORDER BY en.dtsScheduled ASC";



			if($data = $this->DB->rawSql($sql)->returnData())
			{
				return $data;
			}

				return false;

	}



	public function pluckStudentByEnrollmentId($enroll_id)
	{

		if($candiate_id = $this->DB->pluck('student_id')->Where("id = '".$enroll_id."'"))
		{
			return $candiate_id;
		}

		return false;

	}



	public function updateInvited($enroll_id)
	{


		$dataPayload = array('invited'=> 1);

		if($this->DB->update($dataPayload, $enroll_id))
		{
			return true;
		}

		return false;


	}


	public function delete($id)
	{

		$this->DB->delete($id);
		return $this->DB->resource;

	}


	public function update($payload, $id)
	{

		$this->DB->update($payload, $id);
		
		return $this->DB->resource;

	}



}
